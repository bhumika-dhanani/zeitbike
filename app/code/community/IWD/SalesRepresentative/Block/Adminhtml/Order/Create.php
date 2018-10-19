<?php
class IWD_SalesRepresentative_Block_Adminhtml_Order_Create extends  Mage_Core_Block_Template{
	
	
	public function __construct(){
		parent::__construct();
		
		$this->setTemplate('salesrep/order/view/create_sales.phtml');
	}
	
	public function getUsers(){
		$collection = Mage::getModel('admin/user')->getCollection()->addFieldToFilter('is_active', array('eq'=>1))->setOrder('lastname', 'asc');
		return $collection;
	}
	
	public function isManual(){
		$type = Mage::getStoreConfig('salesrep/create_order/type');
		if ($type==1){
			return true;
		}else{
			return false;
		}
	}
	
	public function isAllow(){
		return (bool)Mage::getStoreConfig('salesrep/create_order/allow');
	}
	
}