<?php
class IWD_AAC_Helper_Render extends Mage_Core_Helper_Data{

   
     public function _renderProductOptions($observer){
       
        $controller	=	Mage::app()->getFrontController(); 
        $layout = Mage::app()->getLayout();
        $block = $layout->getBlock('product.info');
        $block->setTemplate('aac/catalog/product/view.phtml');
        return $block->toHtml();

    }

   
   
	/** RENDER CONFIRMATION BLOCK FOR ADD TO CART **/
	public function renderConfirmationCart($product, $update=false){
		$layout = Mage::app()->getLayout();
		$block = $layout->createBlock('core/template')->setData('product', $product)->setData('updateProduct', $update)->setTemplate('aac/ajax/success-cart.phtml');
		
		return $block->toHtml();
	} 
	
	/** RENDER CONFIRMATION BLOCK FOR ADD TO WISHLIST **/
	public function renderConfirmationWishlist($product){
		$layout = Mage::app()->getLayout();
		$block = $layout->createBlock('core/template')->setData('product', $product)->setTemplate('aac/ajax/success-wishlist.phtml');
	
		return $block->toHtml();
	}
	
	/** RENDER SHOPPING CART **/
	public function _renderShoppingCart(){
	
		$layout = Mage::app()->getLayout();
		$layout->getUpdate()->load('checkout_cart_index');
		$layout->generateXml();
		$layout->generateBlocks();
		$block = $layout->getBlock('checkout.cart');
		if ($block) {
			return $block->toHtml();
		}
		return false;
	}
	
	/** RENDER TOP LINKS **/
	public function _renderHeader(){
		$layout = Mage::app()->getLayout();
		$updater = $layout->getUpdate();
		$updater->load('default');
		if (!Mage::getSingleton('customer/session')->isLoggedIn()){
			$updater->merge('customer_logged_out');
		}else{
			$updater->merge('customer_logged_in');
		}
		
		$layout->generateXml();
		$layout->generateBlocks();
		$block = $layout->getBlock('header');
		if ($block) {
			return $block->getChild('topLinks')->toHtml();
		}
		return false;
		
	}
	
	public function _renderDropdown(){
	    $version =Mage::getVersionInfo();
	    
	    //if magento version 1.9
	    if ($version['minor']>=9){
	        $layout = Mage::app()->getLayout();
	        $updater = $layout->getUpdate();
	        $updater->load('default');
	        $layout->generateXml();
	        $layout->generateBlocks();
	        $block = $layout->getBlock('minicart_head');
	        if ($block) {
	            return $block->toHtml();
	        }
	        return false;
	    }
		
		$layout = Mage::app()->getLayout();
		$updater = $layout->getUpdate();
		$updater->load('aac_topcart_dropdown');
		$layout->generateXml();
		$layout->generateBlocks();
		$block = $layout->getBlock('top_cart');
		if ($block) {
			return $block->toHtml();
		}
		return false;
	}
}