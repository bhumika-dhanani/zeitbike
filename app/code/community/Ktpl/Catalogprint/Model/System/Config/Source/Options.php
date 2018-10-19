<?php
class Ktpl_Catalogprint_Model_System_Config_Source_Options
{
    public function toOptionArray()
    {
        return array(
            array('value' => '4', 'label'=>Mage::helper('catalogprint')->__('4 Products (2 Rows, 2 Columns)')),
            array('value' => '6', 'label'=>Mage::helper('catalogprint')->__('6 Products (2 Rows, 3 Columns)')),
            array('value' => '9', 'label'=>Mage::helper('catalogprint')->__('9 Products (3 Rows, 3 Columns)')),
            array('value' => '12', 'label'=>Mage::helper('catalogprint')->__('12 Products (3 Rows, 4 Columns)')),
            
        );
    }
}

