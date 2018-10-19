<?php
class IWD_FeaturedProducts_Block_List extends Mage_Catalog_Block_Product_List {
	
	function __construct(){
		$this->setTemplate('iwdfeaturedproduct/list.phtml');
	}
	
	function getAttributes() {
		$attributes = Mage::getSingleton ( 'catalog/config' )->getProductAttributes ();
		return $attributes;
	}
	function getStoreId(){		
		return Mage::app()->getStore()->getStoreId();
	}
	
	function getSort(){
		$config = Mage::getStoreConfig('iwdfeaturedproduct/main/sort', $this->getStoreId());		
		return $config;
	}
	
	function getLimit(){
		$limit = Mage::getStoreConfig('iwdfeaturedproduct/main/peritems', $this->getStoreId());
		return $limit;
	}
	
	function getProductCollectionMain() {
	
		$collection = Mage::getResourceModel ( 'catalog/product_collection' );
		$collection->addAttributeToSelect ( $this->getAttributes () )
					->addAttributeToFilter ( 'iwd_featured_product', 1 )
					/*
					 ->addMinimalPrice()
					->addFinalPrice()
					->addTaxPercents()
					*/
					->addStoreFilter()
					->addAttributeToSort('position', 'ASC');
				
		Mage::getSingleton ( 'catalog/product_status' )->addVisibleFilterToCollection ( $collection );
		Mage::getSingleton ( 'catalog/product_visibility' )->addVisibleInCatalogFilterToCollection ( $collection );		
		return $collection;
	}

}