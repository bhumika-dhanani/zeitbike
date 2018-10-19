<?php

class IWD_SalesRepresentative_Block_Adminhtml_Order_Users extends Mage_Core_Block_Template
{

    public function __construct()
    {
        parent::__construct();

        $this->setTemplate('salesrep/order/view/users.phtml');
    }

    public function getAdminUsers()
    {
        $collection = Mage::getModel('admin/user')
            ->getCollection()->addFieldToFilter('is_active', array('eq' => 1))->setOrder('lastname', 'asc');
        $select = $collection->getSelect();
        $select->joinLeft(array('link_user' => $collection->getTable('salesrep/users')), 'link_user.iwd_user_id=main_table.user_id', array("IF(link_user.iwd_name IS NULL or link_user.iwd_name = '', CONCAT(main_table.firstname,' ', main_table.lastname), link_user.iwd_name) as username"));
        return $collection;
    }

    public function getActiveUserOrder()
    {
        $order_id = Mage::app()->getRequest()->getParam('order_id');

        $user = Mage::getModel('salesrep/sales')->getCollection()->addFieldToFilter('iwd_order_id', array('eq' => $order_id))->getFirstItem();

        return $user->getUserId();
    }
}