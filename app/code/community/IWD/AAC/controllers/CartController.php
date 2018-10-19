<?php
class IWD_AAC_CartController extends  Mage_Core_Controller_Front_Action{
	
	protected function _getCart(){
		return Mage::getSingleton('checkout/cart');
	}
	
	
	protected function _getSession(){
		return Mage::getSingleton('checkout/session');
	}
	
	protected function _getQuote(){
		return $this->_getCart()->getQuote();
	}
	
	
	protected function _initProduct()
	{
		$productId = (int) $this->getRequest()->getParam('product');
		if ($productId) {
			$product = Mage::getModel('catalog/product')
			->setStoreId(Mage::app()->getStore()->getId())
			->load($productId);
			if ($product->getId()) {
				return $product;
			}
		}
		return false;
	}
	
	public function getUrlAction(){
		$params = $this->getRequest()->getParams();
		$product = $this->_initProduct();
		
		$responseData = array();
		if ($product){
			$responseData['error'] = false;
			$responseData['url'] = $product->getProductUrl() . '?options=cart';
		}else{
			$responseData['error'] = true;
			$responseData['message'] = $this->__('Cannot add the item to shopping cart.');
		}
		
		$this->getResponse()->setHeader('Content-type','application/json', true);
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($responseData));
	}
	
	/** 
	 * REMOVE ITEM FROM CHECKOUT CART
	 */
	public function removeAction(){
		if (!$this->getRequest()->isAjax()){
			$this->norouteAction();
			return;
		}
		$id = (int) $this->getRequest()->getParam('id');
		$responseData = array();
		if ($id) {
			try {
				
				$this->_getCart()->removeItem($id)->save();
				$responseData['error'] = false;
				
				$_helper = Mage::helper('aac/render');
				
				$responseData['shopping_cart'] = $_helper->_renderShoppingCart();
				$responseData['header']	= $_helper->_renderHeader();
				$responseData['dropdown']	= $_helper->_renderDropdown();
			} catch (Exception $e) {
				$responseData['error'] = true;
				$response['message'] = $this->__('Cannot remove the item.');
				Mage::logException($e);
			}
		}
		
		$this->getResponse()->setHeader('Content-type','application/json', true);
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($responseData));
		
	}
	

}