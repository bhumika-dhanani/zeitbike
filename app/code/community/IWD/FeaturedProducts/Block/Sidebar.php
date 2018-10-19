<?php
class IWD_FeaturedProducts_Block_Sidebar extends Mage_Catalog_Block_Product_List {
	
	function getAttributes() {
		$attributes = Mage::getSingleton ( 'catalog/config' )->getProductAttributes ();
		return $attributes;
	}
	
	function getStoreId(){		
		return Mage::app()->getStore()->getStoreId();
	}
	
	function getSort(){
		$config = Mage::getStoreConfig('iwdfeaturedproduct/sidebar/sort', $this->getStoreId());		
		return $config;
	}
	
	function getLimit(){
		$limit = Mage::getStoreConfig('iwdfeaturedproduct/sidebar/peritems', $this->getStoreId());
		return $limit;
	}
	
	function getProductCollectionMain() {
		
		$collection = Mage::getResourceModel ( 'catalog/product_collection' );
		$collection->getSelect()->order('rand()');
		$collection->addAttributeToSelect ( $this->getAttributes () )
					->addAttributeToFilter ( 'iwd_featured_product', 1 )
					 ->addMinimalPrice()
					 ->addFinalPrice()
					 ->addTaxPercents()
					 ->addStoreFilter()
					 //->addAttributeToSort($this->getSort(), 'DESC')
					->getSelect();
				
		Mage::getSingleton ( 'catalog/product_status' )->addVisibleFilterToCollection ( $collection );
		Mage::getSingleton ( 'catalog/product_visibility' )->addVisibleInCatalogFilterToCollection ( $collection );	
		$collection->getSelect()->limit($this->getLimit());
		return $collection;
	}

	
	function isEnabled(){
		$config = Mage::getStoreConfig('iwdfeaturedproduct/sidebar/isenabled', $this->getStoreId());
		return $config;
	}
}