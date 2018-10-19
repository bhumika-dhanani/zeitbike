<?php
class IWD_AAC_Model_Observer{
	
	//TODO ADD LOGIC FOR CHECK IF EXTENSION IS ENABLE
	
	const XML_PATH_STATUS_CONFIRMATION = 'aac/default/confirmation';
	
	private $_allowTypes	=	array(
			'simple',
			'grouped',
			'configurable',
			'virtual',
			'bundle',
			'downloadable'
	);
	
	/** CHANGE RESPONSE IF PRODUCT UPDATE **/
	public function productupdate($observer){
		if (!Mage::getStoreConfig ( 'aac/global/status' )){
			return;
		}

		/* @var $controller Mage_Core_Controller_Varien_Action */
		$controller = $observer->getEvent()->getData('controller_action');
		$result = Mage::helper('core')->jsonDecode(
				$controller->getResponse()->getBody('default'),
				Zend_Json::TYPE_ARRAY
		);

			
		
		$responseData  = array();
		$_helper = Mage::helper('aac/render');
		$responseData['error'] = false;
		$responseData['confirmation'] = $_helper->renderConfirmationCart(false, true);
		$responseData['header']	= $_helper->_renderHeader();
		$responseData['dropdown'] = $_helper->_renderDropdown();
		$isShoppingCart = $controller->getRequest()->getParam('cart', false);
		
		if ($isShoppingCart && $isShoppingCart!='false'){
		
			$responseData['shopping_cart'] = $_helper->_renderShoppingCart();
		}
		
		$controller->getResponse()->clearHeader('Location');
		$controller->getResponse()->setHttpResponseCode(200);
		$controller->getResponse()->setBody(Mage::helper('core')->jsonEncode($responseData));
		
		return false;
	}

	protected function _checkLocation($headers){
		
		foreach ($headers as $item){
			if ($item['name']=='Location'){
				return $item['value'];
			}
		}
		
		return false;
	}
	
	
	/** get checkout session **/
	protected function _getSession(){
		return Mage::getSingleton('checkout/session');
	}
	
	/**
	 * EVENT WHEN PRODUCT HAS BEEN ADDED TO SHOPPING CART
	 */
	public function checkoutCartAddProductComplete($observer){
	
		if (!Mage::getStoreConfig ( 'aac/global/status' )){
			return;
		}
		
		
		$request = $observer->getData('request');
		$response = $observer->getData('response');
	
		$isAjaxModule = $request->getParam('aac', false);
		$isShoppingCart = $request->getParam('cart', false);
	
		if ($isAjaxModule){
	
			$responseAjax = array();
			try{
				
				$_product = $observer->getData('product');
				
				$_helper = Mage::helper('aac/render');
				
				if (Mage::getStoreConfig(self::XML_PATH_STATUS_CONFIRMATION)){
					$responseAjax['confirmation'] = $_helper->renderConfirmationCart($_product);
				}else{
					$responseAjax['confirmation'] = false;
				}
				
				if ($isShoppingCart && $isShoppingCart!='false'){

					$responseAjax['shopping_cart'] = $_helper->_renderShoppingCart();
				}
				
				$responseAjax['error'] = false;
				
				$responseAjax['header']	= $_helper->_renderHeader();
				$responseAjax['dropdown']	= $_helper->_renderDropdown();

	
			}catch(Exception $e){
	
				$responseAjax['error'] = true;
				$responseAjax['error'] = $e->getMessage();
	
				Mage::logException($e);
			}
	
			$this->_getSession()->setNoCartRedirect(true);
				
			$response->setHeader('Content-type','application/json', true);
			$response->setBody(Mage::helper('core')->jsonEncode($responseAjax));
		}
	}
	
	/** PRODUCT VIEW **/
	public function productView($observer){
	
		if (!Mage::getStoreConfig ( 'aac/global/status' )){
			return;
		}
		//return;
		
		$frontendController	=	$observer->getData('controller_action');
		$request =	$frontendController->getRequest();
		$response =	$frontendController->getResponse();
	
		$isAjaxModule = $request->getParam('aac', false);
		$isAjaxRequest = $request->isAjax();
		if ($isAjaxModule || $isAjaxRequest){
			$product = Mage::registry ( 'current_product' );
			$typeProduct = $product->getTypeId();
			//show only supported product types
			if (in_array($typeProduct, $this->_allowTypes)){
				$_helper = Mage::helper('aac/render');
				$responseData = array();
				try{
						
					$responseData['content'] =  $_helper->_renderProductOptions($observer);
					$responseData['error'] = false;
				}catch (Exception $e){
					$responseData['error'] = true;
					$responseData['error'] = $e->getMessage();
					Mage::logException($e);
				}
	
			}else{
				
				$responseData['error'] = true;
				$responseData['redirect'] = $product->getProductUrl();;
					
			}
			$response->clearAllHeaders();
			$response->setHeader('Content-type','application/json', true);
			$response->setBody(Mage::helper('core')->jsonEncode($responseData));
			return;
		}
	}
	
	
	
	/** IF REQUEST EDIT PRODUCT PAGE **/
	public function productConfigure($observer){
	
		if (!Mage::getStoreConfig ( 'aac/global/status' )){
			return;
		}
		
		$frontendController	=	$observer->getData('controller_action');
		$request =	$frontendController->getRequest();
		$response =	$frontendController->getResponse();
	
		$isAjaxModule = $request->getParam('aac', false);
		$isAjaxRequest = $request->isAjax();
		if ($isAjaxModule || $isAjaxRequest){
			$product = Mage::registry ( 'current_product' );
			$typeProduct = $product->getTypeId();
			//show only supported product types
			if (in_array($typeProduct, $this->_allowTypes)){
				$_helper = Mage::helper('aac/render');
				$responseData = array();
				try{
	
					$responseData['content'] =  $_helper->_renderProductOptions($typeProduct, true);
					$responseData['error'] = false;
				}catch (Exception $e){
					$responseData['error'] = true;
					$responseData['error'] = $e->getMessage();
					Mage::logException($e);
				}
	
			}else{
				//TODO if type id not supported return response with redirect url
				$responseData['error'] = true;
				$responseData['redirect'] = $product->getProductUrl();;
					
			}
			$response->clearAllHeaders();
			$response->setHeader('Content-type','application/json', true);
			$response->setBody(Mage::helper('core')->jsonEncode($responseData));
			return;
		}
	
	}
	
	
	public function sendResponseBefore($observer){
	    $request = Mage::app()->getFrontController()->getRequest();
	    $response = Mage::app()->getFrontController()->getResponse();
	    if ($request->getParam('aac', false)) {
	       $headers = $response->getHeaders();
	      //print_r($this->_getErrorMessages());
	    }
	}
	
	private function _getErrorMessages()
	{
	    $allMessages = array_merge(
	        $this->_getErrorMessagesFromSession(Mage::getSingleton('checkout/session')),
	        $this->_getErrorMessagesFromSession(Mage::getSingleton('wishlist/session')),
	        $this->_getErrorMessagesFromSession(Mage::getSingleton('catalog/session')),
	        $this->_getErrorMessagesFromSession(Mage::getSingleton('customer/session'))
	    );
	    return $allMessages;
	}
	
	private function _getErrorMessagesFromSession($session)
	{
	    $messages = $session->getMessages(true);
	    $sessionMessages = array_merge(
	        $messages->getItems(Mage_Core_Model_Message::ERROR),
	        $messages->getItems(Mage_Core_Model_Message::WARNING),
	        $messages->getItems(Mage_Core_Model_Message::NOTICE)
	    );
	    return $sessionMessages;
	}
	
	
}