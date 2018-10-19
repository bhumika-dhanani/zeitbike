<?php
class IWD_AAC_Block_Product extends Mage_Catalog_Block_Product_View{
	
	public function __construct(){
		$this->setTemplate('aac/catalog/product/view.phtml');
	}
	
	
	public function getProduct(){
		return $product = Mage::registry ( 'current_product' );
	}
}