<?php

class IWD_B2BStoreName_Block_Adminhtml_Sales_Invoice_Render_Options extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    protected static $_statuses;

    protected $_options = false;

    public function render(Varien_Object $row)
    {
        return Mage::helper('IWD_B2BStoreName')->__($this->getCustomerStoreName($row));
    }

    public static function getCustomerStoreName($row)
    {
        $customer_id = Mage::getModel('sales/order')->loadByIncrementId($row->getData('order_increment_id'))->getData('customer_id');
        if(isset($customer_id)){
            $model = Mage::getModel('b2b/customer')->load($customer_id, 'customer_id');
            $storeName = $model['store_name'];
            $row->setData('customer_id',$customer_id);
            try{
                $row->save();
            }catch(Exception $e){
                Mage::log($e,null,'getCustomerStoreNameInvoice.log');
            }
            return $storeName;
        }else{
            return "N/A";
        }
    }
}