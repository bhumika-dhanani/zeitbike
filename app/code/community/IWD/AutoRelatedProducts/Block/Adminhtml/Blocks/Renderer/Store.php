<?php
class IWD_AutoRelatedProducts_Block_Adminhtml_Blocks_Renderer_Store extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {

        $store_views = unserialize($row['store_view']);

        if (in_array(0, $store_views))
            return Mage::helper('iwd_autorelatedproducts')->__("All Store Views");

        foreach($store_views as $item)
            $stores[] = Mage::getSingleton('adminhtml/system_store')->getStoreName($item);

        return implode(', ', $stores);
    }
}
