<?php
class IWD_AutoRelatedProducts_Model_Block_Options_Status
{
    public function toOptionArray()
    {
        return array(
           1 => Mage::helper("iwd_autorelatedproducts")->__("Enable"),
           0 => Mage::helper("iwd_autorelatedproducts")->__("Disable"),
        );
    }
}