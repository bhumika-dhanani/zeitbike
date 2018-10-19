<?php
class IWD_BirthdayCoupon_Block_System_Config_Form_Fieldset_Coupon_Firstpurchase extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return Mage::getModel('iwd_coupon/coupon_firstpurchase')->getCustomerGroupRuleSettingsTable();
    }
}