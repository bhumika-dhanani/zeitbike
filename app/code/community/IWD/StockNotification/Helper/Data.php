<?php
class IWD_StockNotification_Helper_Data extends Mage_Core_Helper_Abstract{
	
	
	const XML_PATH_ENABLED = 'stocknotification/default/status';
	const XML_PATH_EXCLUDE = 'stocknotification/default/exclude';
	
	public function getRequestJsonUrl(){
		return Mage::getUrl(
				'stocknotification/json/requestNotification',
				array('_secure' => Mage::app()->getFrontController()->getRequest()->isSecure())
		);
	}
	
	
	public function saveRequest($request){
		
		$this->validateData($request);
		$this->save($request);
		
	}
	
	
	private function validateData($request){
		if (empty($request['email'])){
			throw new Exception($this->__('Email is required field.'));
		}
		if (empty($request['id'])){			
			throw new Exception($this->__('Please specify product data.'));
		}
		
		
		$collection = Mage::getModel('stocknotification/notice')->getCollection();
		$collection->addFieldToFilter('email', array('eq'=>$request['email']))
							->addFieldToFilter('product_id', array('eq'=>(int)$request['id']))
							->addFieldToFilter('is_notified', array('eq'=>0));
		
		if ($collection->getSize()>0){
			throw new Exception($this->__('This email already notified.'));
		}
	}
	
	
	private function save($request){
		
		$model = Mage::getModel('stocknotification/notice');
		
		$model->setData('product_id', (int) $request['id']);
		$model->setData('parent_id', $this->getParentProductId($request));
		$model->setData('email', $request['email']);
		$model->setData('customer_id', $this->getCustomer());
		$model->setData('store_id', Mage::app()->getStore()->getId());
        $model->setData('is_notified', false);
		
		try{
			$model->save();
		}catch (Exception $e){
			throw new Exception($e);
		}
	}
	
	
	private function getProductType($request){
		return null;
	}
	
	private function getParentProductId($request){
		if (isset($request['parent']) && !empty($request['parent'])){
			return (int) $request['parent'];
		}
		return null;
	}
	
	private function getCustomer(){
		if (Mage::getSingleton('customer/session')->isLoggedIn()){
			return Mage::getSingleton('customer/session')->getCustomerId();
		}
		return null;
	}
	
	
	public function isEnabled(){
		$status	=	Mage::getStoreConfig(self::XML_PATH_ENABLED);
		return $status;
	}
	
	public function isSkipProductType(Mage_Catalog_Model_Product $_product){
		$types	=	Mage::getStoreConfig(self::XML_PATH_EXCLUDE);
		if (empty($types)){
			return false;
		}
		
		$listTypes = explode(',', $types);
		
		$typeId = $_product->getTypeId();
		if (in_array($typeId, $listTypes)){
			return true;
		}
		return false;
	}
	
	public function getRemoveUrl($_item){
		
		return $this->_getUrl('stocknotification/customer/remove',
				array('item' => $_item->getId())
		);
	}
	
}