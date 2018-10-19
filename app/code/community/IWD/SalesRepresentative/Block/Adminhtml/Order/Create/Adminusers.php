<?php

class IWD_SalesRepresentative_Block_Adminhtml_Order_Create_Adminusers extends Mage_Core_Block_Template
{


    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('salesrep/order/create/adminusers.phtml');

    }


    public function getAdminUsers()
    {
        $collection = Mage::getModel('admin/user')->getCollection()->addFieldToFilter('is_active', array('eq' => 1))->setOrder('lastname', 'asc');
        return $collection;
    }
}