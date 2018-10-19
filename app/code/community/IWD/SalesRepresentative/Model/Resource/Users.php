<?php 
class IWD_SalesRepresentative_Model_Resource_Users extends Mage_Core_Model_Resource_Db_Abstract{
	
    protected function _construct(){
        $this->_init('salesrep/users', 'iwd_entity_id');
    }
    
    
    protected function _prepareDataForSave(Mage_Core_Model_Abstract $object){
    	$currentTime = Varien_Date::now();
    	if ((!$object->getId() || $object->isObjectNew()) && !$object->getIwdCreatedAt()) {
    		$object->setCreatedAt($currentTime);
    	}
    	$object->setUpdatedAt($currentTime);
    	$data = parent::_prepareDataForSave($object);
    	return $data;
    }
    
    /**
     * Perform operations after object load
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Cms_Model_Resource_Block
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
    	if ($object->getId()) {
//    		VarDumper::dump($object->getUserId(),6,1);
//            die();
    		/** get related products*/
    		$products = $this->searchRelatesIds($object->getId());
    		$collection  = Mage::getModel('catalog/product')->getCollection()->addFieldToFilter('entity_id', array('in'=>$products));
    		$object->setData('related_products', $collection);
    		
    		
    		/** get related customers **/
    		$customers = $this->searchCustomersIds($object->getId());
    		$collection  = Mage::getModel('customer/customer')->getCollection()->addFieldToFilter('entity_id', array('in'=>$customers));
    		$object->setData('related_customers', $collection);
    		
    		/** get admin info **/
    		 $info = Mage::getModel('admin/user')->load($object->getIwdUserId());

    		 $object->setData('iwd_adminname', $info->getFirstname() . ', ' . $info->getLastname());
    	} 
    	
    	
    	
    
    	return parent::_afterLoad($object);
    }
    
    
    /**
     * Get product ids to which specified item is assigned
     *
     * @param int $id
     * @return array
     */
    public function searchRelatesIds($id){
    	
    	$adapter = $this->_getReadAdapter();
    
    	$select  = $adapter->select()
	    	->from($this->getTable('salesrep/link'), 'iwd_linked_product_id')
	    	->where('iwd_user_id = :user');
    
    	$binds = array(
    			':user' => (int) $id
    	);
    
    	return $adapter->fetchCol($select, $binds);
    }
    
    /**
     * Get customers ids to which specified item is assigned
     *
     * @param int $id
     * @return array
     */
    public function searchCustomersIds($id){
    	 
    	$adapter = $this->_getReadAdapter();
    
    	$select  = $adapter->select()
    	->from($this->getTable('salesrep/customers'), 'iwd_linked_customer_id')
    	->where('iwd_user_id = :user');
    
    	$binds = array(
    			':user' => (int) $id
    	);
    
    	return $adapter->fetchCol($select, $binds);
    }
} 