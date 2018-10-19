<?php
class IWD_AutoRelatedProducts_Model_Block_Options_Links
{
    const BUTTON_ADD_TO_CART = 'cart';
    const BUTTON_ADD_TO_COMPARE = 'compare';
    const BUTTON_ADD_TO_WISHLIST = 'wishlist';

    public function getOptionArray()
    {
        return array(
                '-1'=> array(
                    'value' => '-1',
                    'label' =>  Mage::helper("iwd_autorelatedproducts")->__("-- Not select --"),
                ),
                'cart' => array(
                    'value'=> self::BUTTON_ADD_TO_CART,
                    'label' => Mage::helper("iwd_autorelatedproducts")->__("Add To Cart")
                ),
                'compare' => array(
                    'value'=> self::BUTTON_ADD_TO_COMPARE,
                    'label' => Mage::helper("iwd_autorelatedproducts")->__("Add To Compare")
                ),
                'wishlist' => array(
                    'value'=> self::BUTTON_ADD_TO_WISHLIST,
                    'label' => Mage::helper("iwd_autorelatedproducts")->__("Add To Wishlist")
                ),
        );
    }
}