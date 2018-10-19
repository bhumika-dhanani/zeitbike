<?php
require_once(Mage::getModuleDir('controllers','Mage_Wishlist').DS.'IndexController.php');
class IWD_AAC_WishlistController extends Mage_Wishlist_IndexController{

	private function _getErrorMessages(){
		$allMessages = array_merge(
				$this->_getErrorMessagesFromSession(Mage::getSingleton('wishlist/session')),
				$this->_getErrorMessagesFromSession(Mage::getSingleton('customer/session'))
	
		);
		return $allMessages;
	}
	
	private function _getErrorMessagesFromSession($session){
		$messages = $session->getMessages(true);
		$sessionMessages = array_merge(
				$messages->getItems(Mage_Core_Model_Message::ERROR),
				$messages->getItems(Mage_Core_Model_Message::WARNING),
				$messages->getItems(Mage_Core_Model_Message::NOTICE)
		);
		return $sessionMessages;
	}
	
	
	private function updateResponse($responseData){
		
		$response = $this->getResponse();
		$response->clearBody();
		$response->setHttpResponseCode(200);	
		$response->clearHeaders();
				
		$this->getResponse()->setHeader('Content-type','application/json', true);
		
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($responseData));
		$this->getResponse()->sendResponse();
		exit(0);
	}


	/**
	 * ADD ITEM TO WISHLIST
	 */
	public function addAction(){
	
		parent::addAction();
	
		$messages = $this->_getErrorMessages();
		$responseData = array();
		if (count($messages)>0){
			$responseData['error'] = true;
			$responseData['message'] = implode('<br />', $messages);
			$this->updateResponse($responseData);
		}else{
	
			$productId = (int) $this->getRequest()->getParam('product');
			$product = Mage::getModel('catalog/product')->load($productId);
			$_helper = Mage::helper('aac/render');
			$responseData['error'] = false;
			$responseData['confirmation'] = $_helper->renderConfirmationWishlist($product);
			$responseData['header']	= $_helper->_renderHeader();
			
			$this->updateResponse($responseData);
	
		}
	}
	
}