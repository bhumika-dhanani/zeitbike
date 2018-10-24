<?php
class IWD_B2B_AccountController extends Mage_Core_Controller_Front_Action{

    const XML_PATH_SUCCESS_MESSAGE = 'b2b/register_form/success';
    const XML_PATH_SUCCESS_URL = 'b2b/register_form/redirect';
    
    
    const XML_PATH_EMAIL_SENDER     = 'contacts/email/sender_email_identity';
    const XML_PATH_EMAIL_RECIPIENTS  = 'b2b/emails/admins';    
    const XML_PATH_RECIEVED_EMAIL_TEMPLATE   = 'b2b/emails/recieved_template';
    const XML_PATH_CUSTOMER_RECIEVED_EMAIL_TEMPLATE   = 'b2b/emails/customer_recieved_template';
    
    
    
 	protected function _getSession(){
        return Mage::getSingleton('customer/session');
    }
    
    public function postDispatch(){
    	parent::postDispatch();
    	$this->_getSession()->unsNoReferer(false);
    }
	
	
	/** RENDER BLOCK BY NAME **/
	protected function _prepareBlock($blockName){
		
		$layout = $this->getLayout();
		$update = $layout->getUpdate();
		
		$this->_initLayoutMessages('customer/session');
		$this->_initLayoutMessages('catalog/session');
		
		$update->load('signin_customer_' . $blockName);
		$layout->generateXml();
		$layout->generateBlocks();
		$output = $layout->getOutput();
		
		return $output;
	}
	
	
	public function redirectAction(){
		$url = $this->getRequest()->getParam('url', false);
		Mage::getSingleton('core/session')->setSigninRedirect($url);
	} 
	
	
	public function registerAction(){
	    $this->loadLayout();
	    $this->_initLayoutMessages('customer/session');
	    $this->renderLayout();
	}
	
	public function isLoginAction(){
	    $response = array();
	    $isLoggin = Mage::getSingleton('customer/session')->isLoggedIn();
	    if (!$isLoggin){
	        $response['isLogin'] = 'false';
	    }else{
	        $response['isLogin'] = Mage::helper('b2b')->getDashboardUrl();
	    }
	    
	    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
	}

    public function loginAction(){
        $isLoggin = Mage::getSingleton('customer/session')->isLoggedIn();
        if (!$isLoggin){
            $this->loadLayout();
            $this->renderLayout();
        }else{
            $this->_redirectUrl(Mage::helper('b2b')->getDashboardUrl());
        }
    }
	
	/** CHECK LOGIN DATA FROM LOGIN FORM **/
	public function loginPostAction(){
		$response = array();
		$redirectUrl = Mage::helper('b2b')->getDashboardUrl();
		if ($this->_getSession()->isLoggedIn()) {
			$response['redirect'] = $redirectUrl;
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
			return;
		}
		
		
		$session = $this->_getSession();
		
		if ($this->getRequest()->isPost()) {
			$login = $this->getRequest()->getPost('login');
		
			if (!empty($login['username']) && !empty($login['password'])) {
				
				
				try {
					
					$session->login($login['username'], $login['password']);
					
					if ($session->getCustomer()->getIsJustConfirmed()) {
						$this->_welcomeCustomer($session->getCustomer(), true);
						$response['linkAfterLogin'] = $redirectUrl;
						$response['message'] = $this->__('Thank you for registering with %s.', Mage::app()->getStore()->getFrontendName());
						
						$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
						return;
					}
					
					
					if (!$session->getCustomer()->getB2bActive()) {
					    $session->logout();
					    $response['error'] = 1;
					    $response['message'] = Mage::getStoreConfig('b2b/access/message');
					
					    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
					    return;
					}
					
				    $response['linkAfterLogin'] = $redirectUrl;
                    //TODO ADD LOGIC TO CLEAR QUOTE AFTER LOGIN
					$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
					return;
					
				} catch (Mage_Core_Exception $e) {
					
					switch ($e->getCode()) {
						case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
							$value = Mage::helper('customer')->getEmailConfirmationUrl($login['username']);
							$message = Mage::helper('customer')->__('This account is not confirmed. <a href="%s">Click here</a> to resend confirmation email.', $value);
							break;
						case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
							$message = $e->getMessage();
							break;
						default:
							$message = $e->getMessage();
					}
					
					
					$response['error'] = 1;
					$response['message'] = $message;
					
					$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
					return;
					
				} catch (Exception $e) {
					// Mage::logException($e); // PA DSS violation: this exception log can disclose customer password
				}
				
				
			} else {
				

				$response['error'] = 1;
				$response['message'] = $this->__('Login and password are required.');
					
				$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
				return;
			}
		}
		
		$response['error'] = 1;
		$response['message'] = $this->__('Login and password are required.');
			
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
		return;
		
	}
	
	
	
	/**
     * Create customer account action
     */
    public function registerPostAction(){
        $redirectUrl = $this->_redirect(Mage::getStoreConfig(self::XML_PATH_SUCCESS_URL), array('_secure'=>true));
        $session = $this->_getSession();
        if ($session->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }
        
       
        $shipping = $this->getRequest()->getParam('shipping', array());
        $billing = $this->getRequest()->getParam('billing', array());
        $address = $this->getRequest()->getParam('address', array());
       // print_r($shipping);die();
        
        $session->setEscapeMessages(true); // prevent XSS injection in user input
        if ($this->getRequest()->isPost()) {
            
            $account = $this->getRequest()->getParam('account');
            
            $errors = array();

            if (!$customer = Mage::registry('current_customer')) {
                $customer = Mage::getModel('customer/customer')->setId(null);
            }

            /* @var $customerForm Mage_Customer_Model_Form */
            $customerForm = Mage::getModel('customer/form');
            $customerForm->setFormCode('customer_account_create')
                ->setEntity($customer);

            $customerData = $account;


            /**
             * Initialize customer group id
             */
            $customer->getGroupId();
            
            try {
                $customerErrors = $customerForm->validateData($customerData);
                if ($customerErrors !== true) {
                    $errors = array_merge($customerErrors, $errors);
                } else {
                    $customerForm->compactData($customerData);
                    $customer->setPassword($account['password']);
                    $customer->setConfirmation($account['confirmation']);
                    $customer->setPasswordConfirmation($account['confirmation']);
                    $customer->setData('b2b_active', '0');//custom activation for b2b
                    $customerErrors = $customer->validate();
                    if (is_array($customerErrors)) {
                        $errors = array_merge($customerErrors, $errors);
                    }
                }

                $validationResult = count($errors) == 0;

                if (true === $validationResult) {
                    $customer->save();
                    Mage::helper('b2b/customer')->createCustomerAddress($customer, $shipping, $billing, $address);
                    Mage::dispatchEvent('customer_register_success',
                        array('account_controller' => $this, 'customer' => $customer)
                    );
                    
                    $url = $this->_welcomeCustomer($customer);
                    $session->addSuccess(Mage::getStoreConfig(self::XML_PATH_SUCCESS_MESSAGE));
                    $this->_redirectSuccess(Mage::getUrl('*/*/register', array('_secure' => true)));
                    return;
                   
                } else {
                    $session->setCustomerFormData($this->getRequest()->getPost());
                    if (is_array($errors)) {
                        foreach ($errors as $errorMessage) {
                            $session->addError($errorMessage);
                        }
                    } else {
                        $session->addError($this->__('Invalid customer data'));
                    }
                }
            } catch (Mage_Core_Exception $e) {
                $session->setCustomerFormData($this->getRequest()->getPost());
                if ($e->getCode() === Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS) {
                    $url = Mage::getUrl('customer/account/forgotpassword');
                    $message = $this->__('There is already an account with this email address. If you are sure that it is your email address, <a href="%s">click here</a> to get your password and access your account.', $url);
                    $session->setEscapeMessages(false);
                } else {
                    $message = $e->getMessage();
                }
                $session->addError($message);
            } catch (Exception $e) {
                $session->setCustomerFormData($this->getRequest()->getPost())
                    ->addException($e, $this->__('Cannot save the customer.'));
            }
        }

        $this->_redirectError(Mage::getUrl('*/*/register', array('_secure' => true)));
    }
	
	
	
	public function forgotpasswordAction(){
		$response = array();
		$email = (string) $this->getRequest()->getPost('email');
		
		if ($email) {
			if (!Zend_Validate::is($email, 'EmailAddress')) {
				$this->_getSession()->setForgottenEmail($email);
				
				$response['error'] = 1;
				$response['message'] = $this->__('Invalid email address.');
				$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
				return;
			}
		
			/** @var $customer Mage_Customer_Model_Customer */
			$customer = Mage::getModel('customer/customer')
					->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
					->loadByEmail($email);

            $customerId = $customer->getId();
            if ($customerId) {
				try {
					$newResetPasswordLinkToken = Mage::helper('customer')->generateResetPasswordLinkToken();
                    $newResetPasswordLinkCustomerId = Mage::helper('customer')
                        ->generateResetPasswordLinkCustomerId($customerId);
                    $customer->changeResetPasswordLinkCustomerId($newResetPasswordLinkCustomerId);
					$customer->changeResetPasswordLinkToken($newResetPasswordLinkToken);
					$customer->sendPasswordResetConfirmationEmail();
				} catch (Exception $exception) {
					
					$response['error'] = 1;
					$response['message'] = $exception->getMessage();
					$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
				
					return;
				}
			}
			$response['link'] = Mage::helper('customer')->__('If there is an account associated with %s you will receive an email with a link to reset your password.', Mage::helper('customer')->htmlEscape($email));
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
			return;
		} else {
			
			
			$response['error'] = 1;
			$response['message'] = $this->__('Please enter your email.');
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
			
			return;
		}
	}
	
	protected function _welcomeCustomer($_customer){
	    
	    $this->_notifyAdmin($_customer);
	   
	    $url = Mage::getModel('adminhtml/url')->getUrl('adminhtml/customer/edit', array('id' => $_customer->getId(),'_secure'=>true));
	
	    $info = $this->getRequest()->getParam('info');
	    $postObject = new Varien_Object();
	    $postObject->setData('url', $url);
	    $postObject->setData('storename', Mage::app()->getStore()->getName());
	    $postObject->setData('customer', $_customer->getName());
	    
	
	    $translate = Mage::getSingleton('core/translate');
	    $translate->setTranslateInline(false);
	    try{
	        $mailTemplate = Mage::getModel('core/email_template');
	        /* @var $mailTemplate Mage_Core_Model_Email_Template */
	        $mailTemplate->setDesignConfig(array('area' => 'frontend'))
                	       // ->setReplyTo($post['email'])
                	        ->sendTransactional(
                	            Mage::getStoreConfig(self::XML_PATH_CUSTOMER_RECIEVED_EMAIL_TEMPLATE),
                	            Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
                	            $_customer->getEmail(),
                	            null,
                	            array('data' => $postObject)
                	        );
	        
	        if (!$mailTemplate->getSentSuccess()) {
	            throw new Exception();
	        }
	        
	        $translate->setTranslateInline(true);
	         
	    }catch(Exception $e){
	        $translate->setTranslateInline(true);
	        echo $e->getMessage();
	    }
	  
	   
		//todo add to send email of application submission
		
	}
	
	protected function _notifyAdmin($_customer){
	     
	     
	     
	    $emails = $this->_getAdminsEmails();
	     
	     
	    $url = Mage::getModel('adminhtml/url')->getUrl('adminhtml/customer/edit', array('id' => $_customer->getId(),'_secure'=>true));
	
	    $info = $this->getRequest()->getParam('info');
        $account = $this->getRequest()->getParam('account');
        $address = $this->getRequest()->getParam('address');
        $shipping = $this->getRequest()->getParam('shipping');
        $billing = $this->getRequest()->getParam('billing');
        $additional = $this->getRequest()->getParam('add');
        $shipping_region = Mage::getModel('directory/region')->load($shipping['region_id']);
        $shipping_state_name = $shipping_region->getName();
        $billing_region = Mage::getModel('directory/region')->load($billing['region_id']);
        $billing_state_name = $billing_region->getName();
        $websites = Mage::getModel('core/website')->getCollection()->addFieldToFilter('is_default', 1);

        $website = $websites->getFirstItem();
        $websiteCode = $website->getCode();
        $baseurl = Mage::getConfig()->getNode('web/unsecure/base_url', 'website', $websiteCode);
        $certificate = $baseurl.'media/iwd/b2b/'.$_FILES['certificate']['name'];
        $postObject = new Varien_Object();
	    $postObject->setData('url', $url);
	    $postObject->setData('storename', $info['store_name']);
        $postObject->setData('type_business', implode(", ",$info['type_business']));
        $postObject->setData('firstname', $account['firstname']);
        $postObject->setData('lastname', $account['lastname']);
        $postObject->setData('telephone', $address['telephone']);
        $postObject->setData('fax', $address['fax']);
        $postObject->setData('email', $account['email']);
        $postObject->setData('ssn', $info['ssn']);
        $postObject->setData('certificate', $certificate);
        $postObject->setData('classification', implode(", ",$info['classification']));
        $postObject->setData('classification_other', $info['classification']['other']);
        $postObject->setData('shipping_street1', $shipping['street'][0]);
        $postObject->setData('shipping_street2', $shipping['street'][1]);
        $postObject->setData('shipping_city', $shipping['city']);
        $postObject->setData('shipping_region_id', $shipping_state_name);
        $postObject->setData('shipping_postcode', $shipping['postcode']);
        $postObject->setData('shipping_country_id', $shipping['country_id']);
        $postObject->setData('number_location', $additional['number_location']);
        $postObject->setData('fedex', $additional['fedex']);
        $postObject->setData('ups', $additional['ups']);
        $postObject->setData('billing_street1', $billing['street'][0]);
        $postObject->setData('billing_street2', $billing['street'][1]);
        $postObject->setData('billing_city', $billing['city']);
        $postObject->setData('billing_region_id', $billing_state_name);
        $postObject->setData('billing_postcode', $billing['postcode']);
        $postObject->setData('billing_country_id', $billing['country_id']);
        $postObject->setData('owner_firstname', $additional['owner_firstname']);
        $postObject->setData('owner_lastname', $additional['owner_lastname']);
        $postObject->setData('owner_telephone', $additional['owner_telephone']);
        $postObject->setData('owner_fax', $additional['owner_fax']);
        $postObject->setData('owner_email', $additional['owner_email']);
        $postObject->setData('buyer_firstname', $additional['buyer_firstname']);
        $postObject->setData('buyer_lastname', $additional['buyer_lastname']);
        $postObject->setData('buyer_telephone', $additional['buyer_telephone']);
        $postObject->setData('buyer_fax', $additional['buyer_fax']);
        $postObject->setData('buyer_email', $additional['buyer_email']);
        $postObject->setData('assistant_firstname', $additional['assistant_firstname']);
        $postObject->setData('assistant_lastname', $additional['assistant_lastname']);
        $postObject->setData('assistant_telephone', $additional['assistant_telephone']);
        $postObject->setData('assistant_fax', $additional['assistant_fax']);
        $postObject->setData('assistant_email', $additional['assistant_email']);
        $postObject->setData('accounts_firstname', $additional['accounts_firstname']);
        $postObject->setData('accounts_lastname', $additional['accounts_lastname']);
        $postObject->setData('accounts_telephone', $additional['accounts_telephone']);
        $postObject->setData('accounts_fax', $additional['accounts_fax']);
        $postObject->setData('accounts_email', $additional['accounts_email']);

	    foreach ($emails as $email){
	        $translate = Mage::getSingleton('core/translate');
	        $translate->setTranslateInline(false);
	        try{
	            $mailTemplate = Mage::getModel('core/email_template');
	            /* @var $mailTemplate Mage_Core_Model_Email_Template */
	            $mailTemplate->setDesignConfig(array('area' => 'frontend'))
	            // ->setReplyTo($post['email'])
	            ->sendTransactional(
	                Mage::getStoreConfig(self::XML_PATH_RECIEVED_EMAIL_TEMPLATE),
	                Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
	                $email,
	                null,
	                array('data' => $postObject)
	            );
	             
	            if (!$mailTemplate->getSentSuccess()) {
	                throw new Exception();
	            }
	             
	            $translate->setTranslateInline(true);
	
	        }catch(Exception $e){
	            $translate->setTranslateInline(true);
	            echo $e->getMessage();
	        }
	    }
	
	    //todo add to send email of application submission
	
	}
	
	protected function _getAdminsEmails(){
	    $emails = array();
	    $ids = Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENTS);
	    $ids = explode(',', $ids);
	    foreach ($ids as $id){
	        if (!empty($id)){
	           $user = Mage::getModel('admin/user')->load($id);
	           $emails[] = $user->getEmail();
	        }
	    }
	    
	    return $emails;
	}
	
	
}