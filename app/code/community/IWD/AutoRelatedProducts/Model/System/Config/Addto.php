<?php
class IWD_AutoRelatedProducts_Model_System_Config_Addto
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'nothing', 'label' => '-- Not select --'),
            array('value' => 'checkbox', 'label' => 'Checkbox "Add to Cart"'),
            array('value' => 'cart', 'label' => 'Link "Add to Cart"'),
            array('value' => 'wishlist', 'label' => 'Link "Add to Wishlist"'),
            array('value' => 'compare', 'label' => 'Link "Add to Compare"'),
        );
    }
}