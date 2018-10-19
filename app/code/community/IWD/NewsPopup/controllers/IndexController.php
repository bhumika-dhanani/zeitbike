<?php 
class IWD_NewsPopup_IndexController extends Mage_Core_Controller_Front_Action{

	/**
	 * New subscription action
	 */
	public function subscribeAction(){

        $email = false;
        $responseData = array();
        $responseData['error'] = true;
			if ($this->getRequest()->isPost() && $this->getRequest()->getPost('email')) {
				$customerSession    = Mage::getSingleton('customer/session');
				$email              = (string) $this->getRequest()->getPost('email');
				$responseData['error'] = false;
				try {
					if (!Zend_Validate::is($email, 'EmailAddress')) {
						Mage::throwException($this->__('Please enter a valid email address.'));
					}
		
					if (Mage::getStoreConfig(Mage_Newsletter_Model_Subscriber::XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG) != 1 &&
                        !$customerSession->isLoggedIn()) {
						Mage::throwException($this->__('Sorry, but administrator denied subscription for guests. Please <a href="%s">register</a>.', Mage::helper('customer')->getRegisterUrl()));
					}
		
					$ownerId = Mage::getModel('customer/customer')
					->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
					->loadByEmail($email)
					->getId();
					if ($ownerId !== null && $ownerId != $customerSession->getId()) {
						Mage::throwException($this->__('Looks like this email address is alredy subscribed to our newsletter.'));
					}

                    if (Mage::getStoreConfig(IWD_NewsPopup_Helper_Data::XML_PATH_MULTIPLE_QUEST_SUBSCRIPTION) != 1) {
                        $emailExist = Mage::getModel('newsletter/subscriber')->load($email, 'subscriber_email');
                        if ($emailExist->getId()) {
                            /*Mage::throwException($this->__('This email address is already exist.'));*/
                            Mage::throwException($this->__('Looks like this email address is alredy subscribed to our newsletter.'));
                        }
                    }

					$status = Mage::getModel('newsletter/subscriber')->subscribe($email);
					if ($status == Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE) {
						$responseData['message'] = $this->__('Confirmation request has been sent.');
					}
					else {
						$responseData['message'] = $this->__('You have successfully subscribed to our newsletter.');
					}
				}
				catch (Mage_Core_Exception $e) {
					$responseData['message'] = $this->__($e->getMessage());
					$responseData['error'] = true;
				}
				catch (Exception $e) {
					$responseData['message'] = $this->__('There was a problem with the subscription.');
					$responseData['error'] = true;
				}
			}
			$response['message'] = $email;
			$this->getResponse()->setHeader('Content-type','application/json', true);
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($responseData));
		
	}
}
