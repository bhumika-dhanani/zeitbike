<?php

class IWD_SalesRepresentative_Adminhtml_Iwd_Salesrep_JsonController extends Mage_Adminhtml_Controller_Action
{

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/sales/order');
    }

    public function usersAction()
    {


        $html = $this->getLayout()->createBlock('salesrep/adminhtml_order_users')->toHtml();
        echo $html;
    }

    public function saveAction()
    {
        $orderId = Mage::app()->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order')->load($orderId);

        $user = Mage::app()->getRequest()->getParam('user_id', false);

        if ($order && $order->getId() && !empty($user) && $user !== false) {

            //remove related record
            $related = Mage::getModel('salesrep/sales')->getCollection()->addFieldToFilter('iwd_order_id', array('eq' => $orderId))->getFirstItem();
            try {
                $related->delete();
            } catch (Exception $e) {
                Mage::logException($e);
            }

            //create new
            $orderId = $order->getId();

            $sales = Mage::getModel('salesrep/sales');

            $sales->setData('iwd_order_id', $orderId);
            $sales->setData('iwd_user_id', $user);

            try {
                $sales->save();
                Mage::helper('salesrep')->sendEmailNotification($user, $orderId);
            } catch (Exception $e) {
                Mage::logException($e);
            }

        } elseif ($order->getId() && !$user) {
            $related = Mage::getModel('salesrep/sales')->getCollection()->addFieldToFilter('iwd_order_id', array('eq' => $orderId))->getFirstItem();

            try {
                $related->delete();
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }

        //get user from related
        $related = Mage::getModel('salesrep/sales')->getCollection()->addFieldToFilter('iwd_order_id', array('eq' => $orderId))->getFirstItem();
        $userId = $related->getIwdUserId();

        $userCollection = Mage::getModel('admin/user')
            ->getCollection()
            ->addFieldToFilter('main_table.user_id', $userId);
        $userCollection->getSelect()->joinLeft(array('link_user' => $userCollection->getTable('salesrep/users')), 'link_user.iwd_user_id=main_table.user_id', array("IF(link_user.iwd_name IS NULL or link_user.iwd_name = '', CONCAT(main_table.firstname,' ', main_table.lastname), link_user.iwd_name) as username"));
        $user = $userCollection->getFirstItem();
        if ($user && $user->getUserId()) {
            echo $user->getUsername();
            die();
        } else {
            echo 'N/A';
            die();
        }

    }

}