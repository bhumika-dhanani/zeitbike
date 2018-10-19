<?php

class IWD_B2BStoreName_Block_Adminhtml_Customer_Online_Render_Options extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    protected static $_statuses;

    protected $_options = false;

    public function render(Varien_Object $row)
    {
        return Mage::helper('IWD_B2BStoreName')->__($this->getCustomerStoreName($row));
    }

    public static function getCustomerStoreName($row)
    {
        $customer_id = $row->getData('customer_id');
        if(isset($customer_id)){
            $model = Mage::getModel('b2b/customer')->load($customer_id, 'customer_id');
            $storeName = $model['store_name'];
            return $storeName;
        }else{
            return "n/a";
        }
    }
}