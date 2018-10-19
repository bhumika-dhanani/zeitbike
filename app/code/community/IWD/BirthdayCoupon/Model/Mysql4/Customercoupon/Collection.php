<?php
class IWD_BirthdayCoupon_Model_Mysql4_Customercoupon_Collection extends
    Mage_Core_Model_Mysql4_Collection_Abstract
    //Mage_Core_Model_Resource_Db_Collection_Abstract

{
	public function _construct(){
        parent::_construct();
        $this->_init('iwd_coupon/customercoupon');
    }
}