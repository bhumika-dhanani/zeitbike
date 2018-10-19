<?php
class IWD_AutoRelatedProducts_Model_System_Config_Position
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'not', 'label' => 'Not display'),
            array('value' => 'left', 'label' => 'Left panel'),
            array('value' => 'right', 'label' => 'Right panel'),
            array('value' => 'bottom', 'label' => 'Additional product (bottom)'),
            array('value' => 'custom_h', 'label' => 'Custom place (horizontal)'),
            array('value' => 'custom_v', 'label' => 'Custom place (vertical)'),
        );
    }
}