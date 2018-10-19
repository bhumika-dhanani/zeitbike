<?php
class IWD_AutoRelatedProducts_Model_Block_Options_Yesno
{

    /**
     * Options getter
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label' => Mage::helper('adminhtml')->__('Yes')),
            array('value' => 0, 'label' => Mage::helper('adminhtml')->__('No')),
        );
    }

    /**
     * Get options in "key-value" format
     * @return array
     */
    public function toArray()
    {
        return array(
            0 => Mage::helper('adminhtml')->__('No'),
            1 => Mage::helper('adminhtml')->__('Yes'),
        );
    }

}