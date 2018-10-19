<?php
class IWD_BirthdayCoupon_Model_System_Config_Rules
{
    public function toOptionArray()
    {
        $options = array(
            array(
                'value' => 'none',
                'label' => Mage::helper('adminhtml')->__('--- Without coupon ---')
            )
        );

        $rulesCollection = Mage::getModel('salesrule/rule')
            ->getCollection()
            ->addFieldToSelect(array('rule_id', 'name'))
            ->addFieldToFilter('is_active', '1')
            ->addFieldToFilter('to_date', array('null' => true))
            ->addFieldToFilter('coupon_type', '2');

        if (version_compare(Mage::getVersion(), '1.7.0.0', '>='))
            $rulesCollection->addFieldToFilter('use_auto_generation', '1');

        $rulesCollection->getSelect()->group(array('rule_id'));

        foreach ($rulesCollection as $rule) {
            $options[] = array(
                'value' => $rule->getRuleId(),
                'label' => $rule->getName()
            );
        }

        return $options;
    }
}