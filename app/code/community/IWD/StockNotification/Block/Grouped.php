<?php
class IWD_StockNotification_Block_Grouped extends Mage_Catalog_Block_Product_View_Type_Grouped {
	
	const XML_PATH_ENABLED = 'stocknotification/default/status';
	
	protected function _prepareLayout() {
		$groupedBlock = $this->getLayout()->getBlock('product.info.grouped');
		if (Mage::getStoreConfig(self::XML_PATH_ENABLED)){
		
			$groupedBlock->setTemplate ( 'stocknotification/grouped.phtml' );
		}else{
			
			$groupedBlock->setTemplate ( 'catalog/product/view/type/grouped.phtml' );
		}
	}
	
}