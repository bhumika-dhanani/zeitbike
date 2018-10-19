<?php
class Ktpl_Catalogprint_Model_System_Config_Source_Groups
{
    public function toOptionArray()
    {
        $options = array();
        
            $options = Mage::getResourceModel('customer/group_collection')
                ->loadData()->toOptionArray();
        
        return $options;
    }
}

