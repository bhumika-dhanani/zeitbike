<?php
class IWD_AutoRelatedProducts_Model_Block_Options_Storeview
{
    public function getOptionArray()
    {
        $arr = array('0' => Mage::helper("iwd_autorelatedproducts")->__('All Store Views'));

        $store = Mage::getSingleton('adminhtml/system_store')->getStoreCollection();
        foreach ($store as $item)
            $arr[$item['store_id']] = $item['name'];

        return $arr;
    }

    public function getAllStores()
    {
        $store = Mage::getSingleton('adminhtml/system_store')->getStoreCollection();
        $arr = array();

        foreach ($store as $item)
            $arr[] = $item['store_id'];

        return $arr;
    }
}