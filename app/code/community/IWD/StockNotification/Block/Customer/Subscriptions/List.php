<?php
class IWD_StockNotification_Block_Customer_Subscriptions_List extends Mage_Core_Block_Template
{
	
	
	protected function _getHelper()
	{
		return Mage::helper('stocknotification');
	}
	
	public function getCollection(){
		$_customer = Mage::getSingleton('customer/session')->getCustomer();
		
		$collection = Mage::getModel('stocknotification/notice')->getCollection()
								->addFieldToFilter('is_notified', array('eq'=>'0'))
								->addFieldToFilter('customer_id', array('eq'=>$_customer->getId()));

		return $collection;
	}
	
	
	public function getProductName(IWD_StockNotification_Model_Notice $_item){
		
		$dataObject = new Varien_Object();
		$model  = Mage::getModel('catalog/product')->setStoreId($_item->getStoreId());
		$product = $model->load($_item->getProductId());
		$data['name'] = $product->getName();
		
		if ($_item->getParentId()){
			$model  = Mage::getModel('catalog/product')->setStoreId($_item->getStoreId());
			$parent = $model->load($_item->getParentId());
			$data['url'] = $parent->getProductUrl();
		}else{
			$data['url'] = $product->getProductUrl();
		}
		
		$dataObject->addData($data);
		return $dataObject;
	}
	
	
	public function getProduct(IWD_StockNotification_Model_Notice $_item){
		$model  = Mage::getModel('catalog/product')->setStoreId($_item->getStoreId());
		$_product = $model->load($_item->getProductId());
		return $_product;
	}
	
	public function getFormatedDate($date)
	{
		return $this->formatDate($date, Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
	}
	
	
 	public function getItemRemoveUrl($_item)
    {
        return $this->_getHelper()->getRemoveUrl($_item	);
    }
}