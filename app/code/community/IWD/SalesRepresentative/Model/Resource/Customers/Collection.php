<?php 
class IWD_SalesRepresentative_Model_Resource_Customers_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract {
	
    public function _construct(){
            $this->_init('salesrep/customers');
    }
} 