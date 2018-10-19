<?php
class IWD_AutoRelatedProducts_Model_Block_Options_Sortorder
{
    const SORT_ORDER_ASC = 'ASC';
    const SORT_ORDER_DESC = 'DESC';

    const RANDOM = 1;
    const NAME_ASC = 2;
    const NAME_DESC = 3;
    const PRICE_ASC = 4;
    const PRICE_DESC = 5;
    const ID_ASC = 6;
    const ID_DESC = 7;
    const SKU_ASC = 8;
    const SKU_DESC = 9;

    public function getOptionArray()
    {
        $helper = Mage::helper('iwd_autorelatedproducts');
        return array(
            self::RANDOM => $helper->__("Random sort"),
            self::NAME_ASC => $helper->__("by Product Name (A-Z)"),
            self::NAME_DESC => $helper->__("by Product Name (Z-A)"),
            self::PRICE_ASC => $helper->__("by Price (0-9)"),
            self::PRICE_DESC => $helper->__("by Price (9-0)"),
            self::ID_ASC => $helper->__("by Product ID (0-9)"),
            self::ID_DESC => $helper->__("by Product ID (9-0)"),
            self::SKU_ASC => $helper->__("by Product SKU (0-9-A-Z)"),
            self::SKU_DESC => $helper->__("by Product SKU (Z-A-9-0)"),
        );
    }

    public function getDir($type)
    {
        if (in_array($type, array(self::NAME_ASC, self::PRICE_ASC, self::ID_ASC, self::SKU_ASC)))
            return self::SORT_ORDER_ASC;

        if (in_array($type, array(self::NAME_DESC, self::PRICE_DESC, self::ID_DESC, self::SKU_DESC)))
            return self::SORT_ORDER_DESC;

        return self::SORT_ORDER_ASC;
    }

    public function getAttribute($type)
    {
        switch ($type) {
            case self::NAME_ASC:
            case self::NAME_DESC:
                return "name";
            case self::PRICE_ASC:
            case self::PRICE_DESC:
                return "price";
            case self::ID_ASC:
            case self::ID_DESC:
                return "id";
            case self::SKU_ASC:
            case self::SKU_DESC:
                return "sku";
        }

        return null;
    }

    public function isRandom($type){
        return ($type == self::RANDOM);
    }
}