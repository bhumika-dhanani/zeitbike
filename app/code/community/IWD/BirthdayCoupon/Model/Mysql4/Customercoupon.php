<?php
class IWD_BirthdayCoupon_Model_Mysql4_Customercoupon extends Mage_Core_Model_Mysql4_Abstract // _Db_Abstract
{
    protected function _construct()
    {
        $this->_init('iwd_coupon/customercoupon', 'id');
    }
}
