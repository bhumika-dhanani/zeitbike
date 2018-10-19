<?php 
class IWD_SalesRepresentative_Model_Resource_Customers extends Mage_Core_Model_Resource_Db_Abstract{
	
    protected function _construct(){
        $this->_init('salesrep/customers', 'iwd_link_id');
    }
} 