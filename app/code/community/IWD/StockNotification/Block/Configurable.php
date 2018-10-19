<?php
class IWD_StockNotification_Block_Configurable extends Mage_Catalog_Block_Product_View {
	
	private $_notAvailableProducts = array();
	
	private $_existNotAvailable = true;
	
	
	public function _construct(){
		parent::_construct();
		
		$this->prepareNotAvailableProduct();
	}
	
	public function getRequestJsonUrl(){
		return $this->getUrl(
								'stocknotification/json/requestNotification', 
								array('_secure' => Mage::app()->getFrontController()->getRequest()->isSecure())
							);
	}
	
	public function prepareNotAvailableProduct(){
		$_currentProduct  = $this->getProduct();
		
		$associatedProducts = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null, $_currentProduct);
		
		foreach($associatedProducts as $_product){
			if (!$_product->isSaleable()){
				$this->_notAvailableProducts[] = $_product;
			}
		}
		
		if (count($this->_notAvailableProducts)>0){
			$this->_existNotAvailable = false;
		}
		
	}
	
	public function isAvailableAssociatedProduct(){
		return $this->_existNotAvailable;
	}
	
	public function getNotAvailabelProducts(){
		return $this->_notAvailableProducts;
	}
	
}