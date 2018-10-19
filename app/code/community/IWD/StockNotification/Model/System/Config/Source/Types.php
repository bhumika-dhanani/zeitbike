<?php
class IWD_StockNotification_Model_System_Config_Source_Types
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
    	
    	$model  = Mage::getModel('catalog/product_type');
    	$listTypes = array();
    	$listTypes[] = array('value' => 0, 'label'=>Mage::helper('adminhtml')->__('None'));     
        foreach ($model->getTypes() as $key=>$data){        	
        	$listTypes[] = array('value' => $key, 'label'=>Mage::helper('adminhtml')->__($data['label']));
        }
       
        return $listTypes;
    }

}
