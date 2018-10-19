<?php
class IWD_BirthdayCoupon_Model_System_Config_Template
{
    public function toOptionArray()
    {
        $options1 = Mage::getModel('adminhtml/email_template')->getDefaultTemplatesAsOptionsArray();
        $options2 = Mage::getModel('adminhtml/system_config_source_email_template')->toOptionArray();
        return array_merge($options1, $options2);
    }
}