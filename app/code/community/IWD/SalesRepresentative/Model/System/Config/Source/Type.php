<?php
class IWD_SalesRepresentative_Model_System_Config_Source_Type{
    protected $_options;

    public function toOptionArray($isMultiselect=false)
    {
        if (!$this->_options) {
            $this->_options = array(
            		array('value'=>'1', 'label'=> Mage::helper('salesrep')->__('Manual')),
            		array('value'=>'2', 'label'=> Mage::helper('salesrep')->__('Auto')),
            		array('value'=>'3', 'label'=> Mage::helper('salesrep')->__('Base on customer')),
            );
        }

        $options = $this->_options;
        if(!$isMultiselect){
            array_unshift($options, array('value'=>'', 'label'=> Mage::helper('adminhtml')->__('--Please Select--')));
        }

        return $options;
    }
}
