<?php
class IWD_BirthdayCoupon_Model_System_Config_Groups
{
    public function toOptionArray()
    {
        $options1[0] = array(
            'value'=> '0',
            'label'=> Mage::helper('adminhtml')->__('NOT LOGGED IN (Guest)')
        );
        $options2 = Mage::getResourceModel('customer/group_collection')
            ->setRealGroupsFilter()
            ->loadData()
            ->toOptionArray();
        return array_merge($options1, $options2);
    }
}