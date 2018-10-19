<?php 
class IWD_SalesRepresentative_Model_Resource_Sales extends Mage_Core_Model_Resource_Db_Abstract{
	
    protected function _construct(){
        $this->_init('salesrep/sales', 'iwd_entity_id');
    }
    
    
    protected function _prepareDataForSave(Mage_Core_Model_Abstract $object){
    	$currentTime = Varien_Date::now();
    	if ((!$object->getId() || $object->isObjectNew()) && !$object->getCreatedAt()) {
    		$object->setCreatedAt($currentTime);
    	}
    	$object->setUpdatedAt($currentTime);
    	$data = parent::_prepareDataForSave($object);
    	return $data;
    }

} 