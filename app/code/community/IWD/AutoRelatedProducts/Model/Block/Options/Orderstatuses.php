<?php
class IWD_AutoRelatedProducts_Model_Block_Options_Orderstatuses
{
    public function getOptionArray()
    {
        $statuses = Mage::getSingleton('sales/order_config')->getStatuses();

        $options = array(
            array(
                'value' => false,
                'label' => Mage::helper('adminhtml')->__('-- Not select --')
            )
        );

        foreach ($statuses as $code=>$label) {
            $options[] = array(
                'value' => $code,
                'label' => $label
            );
        }

        return $options;
    }
}