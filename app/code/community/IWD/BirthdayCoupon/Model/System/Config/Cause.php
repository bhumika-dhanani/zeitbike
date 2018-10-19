<?php
class IWD_BirthdayCoupon_Model_System_Config_Cause extends Mage_Payment_Model_Config
{
    const BIRTHDAY = 'birthday';
    const FIRST_PURCHASE ='first_purchase';
    const SUBSCRIBE = 'subscribe';
    const GOOD_CUSTOMER = 'good_customer';
    const NEW_REGISTRATION = 'new_registration';

    public function GetCauses()
    {
        return array(
            self::BIRTHDAY => Mage::helper('iwd_coupon')->__("Birthday"),
            self::FIRST_PURCHASE => Mage::helper('iwd_coupon')->__("First Purchase"),
            self::SUBSCRIBE => Mage::helper('iwd_coupon')->__("Subscribe"),
            self::GOOD_CUSTOMER => Mage::helper('iwd_coupon')->__("Good Customer"),
            self::NEW_REGISTRATION => Mage::helper('iwd_coupon')->__("New Registration"),
        );
    }
}