<?php
class IWD_StockNotification_Model_Mysql4_Notice extends Mage_Core_Model_Mysql4_Abstract{

	protected function _construct()
	{
		$this->_init('stocknotification/notice', 'entity_id');
	}

	protected function _prepareDataForSave(Mage_Core_Model_Abstract $object) 
	{
		$currentTime = now();
		if ((! $object->getId () || $object->isObjectNew ()) && ! $object->getCreatedAt ()) {
			$object->setCreatedAt ( $currentTime );
		}
		$data = parent::_prepareDataForSave ( $object );
		return $data;
	}
	
}
