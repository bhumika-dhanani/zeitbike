<?php
class IWD_AlsoBought_Model_Config_Type
{
    public function getOptionArray()
    {
        $helper = Mage::helper('iwd_alsobought');

        return array(
            'product_page' => $helper->__("Product page"),
            'cart_page' => $helper->__("Cart page"),
        );
    }

     public function toOptionArray()
    {
        return $this->getOptionArray();
    }
}