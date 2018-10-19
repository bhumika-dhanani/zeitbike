<?php
class IWD_AutoRelatedProducts_Model_System_Config_Slidemode
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'horizontal', 'label' => 'Horizontal'),
            array('value' => 'vertical', 'label' => 'Vertical'),
            array('value' => 'fade', 'label' => 'Fade'),
        );
    }
}