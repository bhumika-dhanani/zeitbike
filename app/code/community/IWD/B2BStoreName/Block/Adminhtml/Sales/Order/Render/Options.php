<?php

class IWD_B2BStoreName_Block_Adminhtml_Sales_Order_Render_Options extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    protected static $_statuses;

    protected $_options = false;

    public function render(Varien_Object $row)
    {
        return Mage::helper('IWD_B2BStoreName')->__($this->getCustomerStoreName($row));
    }

    public static function getCustomerStoreName($row)
    {
        $customerId = $row->getData('customer_id');
        $model = Mage::getModel('b2b/customer')->load($customerId, 'customer_id');
        $storeName = $model['store_name'];
        if(isset($storeName)){
            return $storeName;
        }else{
            return "N/A";
        }
    }
}