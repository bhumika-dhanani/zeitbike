<?php
class IWD_StockNotification_Block_Adminhtml_List_Render_Customer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{


    public function __construct()
    {
        parent::__construct ();
    }

    public function render(Varien_Object $row)
    {

        $_customerId = $row->getCustomerId ();


        if (!empty($_customerId)){
            $model = Mage::getModel('customer/customer')->setStoreId($row->getStoreId());

            $_customer = $model ->load($_customerId);

            return $_customer->getName();
        }

        return $this->helper('stocknotification')->__('Guest');
    }



}