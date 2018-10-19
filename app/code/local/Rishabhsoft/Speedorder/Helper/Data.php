<?php
class Rishabhsoft_Speedorder_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getSpeedorderUrl(){
        $login = Mage::getSingleton( 'customer/session' )->isLoggedIn();
        if($login) {
            $groupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
            if ($groupId == 2)
                return Mage::getUrl('fast-order', array('_secure' => true));
            return false;
        }else {
            return false;
        }

    }
}