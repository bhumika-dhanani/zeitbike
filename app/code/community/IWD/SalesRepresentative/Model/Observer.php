<?php

class IWD_SalesRepresentative_Model_Observer
{

    const XML_PATH_AUTOASSING = 'salesrep/create_order_front/autoassign';
    const XML_PATH_AUTOASSING_CUSTOMER = 'salesrep/create_order_front/autoassign_customer';
    const XML_PATH_AUTOASSING_SPECIAL = 'salesrep/create_order_front/autoassign_special';

    public function checkRequiredModules($observer)
    {
        $cache = Mage::app()->getCache();

        if (Mage::getSingleton('admin/session')->isLoggedIn()) {
            if (!Mage::getConfig()->getModuleConfig('IWD_All')->is('active', 'true')) {
                if ($cache->load("iwd_salesrep") === false) {
                    $message = 'Important: Please setup IWD_ALL in order to finish <strong>IWD Sales Representative</strong> installation.<br />
						Please download <a href="http://iwdextensions.com/media/modules/iwd_all.tgz" target="_blank">IWD_ALL</a> and setup it via Magento Connect.<br />';
                    Mage::getSingleton('adminhtml/session')->addNotice($message);
                    $cache->save('true', 'iwd_salesrep', array("iwd_salesrep"), $lifeTime = 5);
                }
            }
        }

        $available = Mage::helper('salesrep')->isAvailableVersion();
    }


    public function createRelatedUser($observer)
    {
        $this->createRelatedUserBackend($observer);

        $order = $observer->getOrder();

        $status = Mage::getStoreConfig('salesrep/create_order/allow');

        if (!$status) {
            return false;
        }

        if (!$this->isManual()) {
            $user = Mage::getSingleton('admin/session')->getUser();
        } else {
            $data = Mage::app()->getRequest()->getParam('order');

            if (isset($data['salesrep_user'])) {
                $user = Mage::getModel('admin/user')->load((int)$data['salesrep_user']);
            }
        }
        if(empty($user)) {
            return false;
        }

        if ($order && $order->getId()) {

            $orderId = $order->getId();

            $sales = Mage::getModel('salesrep/sales');
            if (!$this->isOrderExist($order->getId(), $user->getId())) {
                $sales->setData('iwd_order_id', $orderId);
                $sales->setData('iwd_user_id', $user->getId());

                try {
                    $sales->save();
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }
        }
        return true;
    }

    public function createByProduct($observer)
    {
        try {

            $orderIds = $observer->getEvent()->getOrderIds();
            if (empty($orderIds) || !is_array($orderIds)) {
                return;
            }
            $order = Mage::getModel('sales/order')->load($orderIds[0]);

            $quote = Mage::getModel('sales/quote')->load($order->getQuoteId());

            $collection = Mage::getModel('salesrep/users')->getCollection();

            $products = $quote->getAllVisibleItems();

            $list = array();

            foreach ($collection as $user) {
                $count = 0;
                foreach ($products as $product) {
                    ++$count;
                    $collection = Mage::getModel('salesrep/link')->getCollection()
                        ->addFieldToFilter('iwd_user_id', array('eq' => $user->getId()))
                        ->addFieldToFilter('iwd_linked_product_id', array('eq' => $product->getProductId()));

                    $itemAssign = $collection->getFirstItem();

                    if ($itemAssign->getId()) {
                        $list[$user->getIwdUserId()][] = $itemAssign->getIwdLinkedProductId();
                    }
                }
            }

            foreach ($list as $user => $products) {
                if (count($products) == $count) {
                    //assign order to backend user
                    if (!$this->isOrderExist($order->getId(), $user)) {
                        $model = Mage::getModel('salesrep/sales');
                        $model->setData('iwd_order_id', $order->getId());
                        $model->setData('iwd_user_id', $user);
                        $model->save();
                    }
                    Mage::helper('salesrep')->sendEmailNotification($user, $order->getId());
                    return;
                }
            }

        } catch (Exception $e) {
            Mage::logException($e);
        }
        return;
    }


    public function createByCustomers($observer)
    {
        try {
            $orderIds = $observer->getEvent()->getOrderIds();
            if (empty($orderIds) || !is_array($orderIds)) {
                return;
            }

            $order = Mage::getModel('sales/order')->load($orderIds[0]);
            $collection = Mage::getModel('salesrep/users')->getCollection();

            $customerId = $order->getCustomerId();
            $list = array();
            $userId = false;
            foreach ($collection as $user) {
                $count = 0;
                ++$count;
                $collection = Mage::getModel('salesrep/customers')->getCollection()
                    ->addFieldToFilter('iwd_user_id', array('eq' => $user->getId()))
                    ->addFieldToFilter('iwd_linked_customer_id', array('eq' => $customerId));

                $itemAssign = $collection->getFirstItem();
                if ($itemAssign->getId()) {
                    $userId = $user->getIwdUserId();
                }
            }
            if ($userId != false) {
                //assign order to backend user
                $model = Mage::getModel('salesrep/sales');
                if (!$this->isOrderExist($order->getId(), $userId)) {
                    $model->setData('iwd_order_id', $order->getId());
                    $model->setData('iwd_user_id', $userId);
                    $model->save();
                }
                Mage::helper('salesrep')->sendEmailNotification($userId, $order->getId());
                return;
            }


        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    public function isOrderExist($orderID, $userId)
    {
        $model = Mage::getModel('salesrep/sales')->getCollection()->addFieldToFilter('iwd_order_id', array('eq' => $orderID))->getFirstItem();
        if (isset($model) && !empty($model) && $model->getId()) {
            try {
                $model->setIwdUserId($userId)->save();
                return true;
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
        return false;

    }

    protected function createToSpecial($observer)
    {
        $special = Mage::getStoreConfig(self::XML_PATH_AUTOASSING_SPECIAL);
        $userSalesRep = false;
        if (!empty($special) && $special) {

            $orderIds = $observer->getEvent()->getOrderIds();
            if (empty($orderIds) || !is_array($orderIds)) {
                return;
            }
            $order = Mage::getModel('sales/order')->load($orderIds[0]);


            try {

                $collection = Mage::getModel('salesrep/users')->getCollection();
                foreach ($collection as $user) {
                    if ($user->getUserId() == $special) {
                        $userSalesRep = $user;
                    }
                }
                if ($userSalesRep === false) {
                    return;
                }
                if (!$this->isOrderExist($order->getId(), $special)) {
                    $model = Mage::getModel('salesrep/sales');
                    $model->setData('iwd_order_id', $order->getId());
                    $model->setData('iwd_user_id', $special);
                    $model->save();
                }

                Mage::helper('salesrep')->sendEmailNotification($userSalesRep->getUserId(), $order->getId());

            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
    }


    /** ASSIGN ORDER TO USER **/
    public function createRelatedUserFront($observer)
    {
        //per customers
        if (Mage::getStoreConfig(self::XML_PATH_AUTOASSING_CUSTOMER)) {
            $this->createByCustomers($observer);
        }
        // per product
        if (Mage::getStoreConfig(self::XML_PATH_AUTOASSING)) {

            $this->createByProduct($observer);
        }
        if (!Mage::getStoreConfig(self::XML_PATH_AUTOASSING_CUSTOMER) && !Mage::getStoreConfig(self::XML_PATH_AUTOASSING)) {
            $this->createToSpecial($observer);
        }
    }

    public function isManual()
    {
        $type = Mage::getStoreConfig('salesrep/create_order/type');
        if ($type == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function isBaseOnCustomer()
    {
        $type = Mage::getStoreConfig('salesrep/create_order/type');
        if ($type == 3) {
            return true;
        } else {
            return false;
        }
    }


    /** UPDATE ORDER GRID **/
    public function salesOrderGridCollectionLoadBefore($observer)
    {
        $collection = $observer->getOrderGridCollection();
        $select = $collection->getSelect();
        $from = $select->getPart('from');
        if (isset($from['related']) || isset($from['user']) || isset($from['link_user'])) {
            return;
        }
        $select->joinLeft(array('related' => $collection->getTable('salesrep/sales')), 'related.iwd_order_id=main_table.entity_id', array('iwd_user_id' => 'iwd_user_id'))
            ->joinLeft(array('user' => $collection->getTable('admin/user')), 'related.iwd_user_id=user.user_id', array())
            ->joinLeft(array('link_user' => $collection->getTable('salesrep/users')), 'link_user.iwd_user_id=user.user_id', array("IF(link_user.iwd_name IS NULL or link_user.iwd_name = '', CONCAT(user.firstname,' ', user.lastname), link_user.iwd_name) as username"));;

        $settings = $this->getUserSettings();
        if (!$settings) {
            return;
        }

        if ($settings->getIwdShowUserOrdersOnly()) {
            $user = Mage::getSingleton('admin/session')->getUser();
            if ($user) {
                if ($user->getRole()->getId() != Mage::getStoreConfig('salesrep/order/skip_restrict')) {
                    $collection->addFieldToFilter('user.user_id', $user->getId());
                }
            }
        }

        if (!$settings->getIwdLimitUsers()) {
            return;
        }

        $items = $settings->getIwdLimitItems();
        $items = explode(',', $items);

        if (!in_array('orders', $items)) {
            return;
        }

        $orders = $this->getOrders();

        if (count($orders) > 0) {
            $ids = implode(',', $orders);
        } else {
            $ids = '-1';
        }

        $select->where('main_table.entity_id IN (' . $ids . ')');

    }

    /** UPDATE CUSTOMER GRID **/
    public function customerGridCollectionLoadBefore($observer)
    {
        $searchTableName = Mage::getSingleton("core/resource")->getTableName('customer_entity');
        $collection = $observer->getCollection();
        $entity = $collection->getEntity();

        if (empty($entity)) {
            return;
        }

        if (!method_exists($entity, 'getEntityTable')) {
            return;
        }

        $entityTable = $entity->getEntityTable();

        /** return if not customer table **/
        if ($searchTableName != $entityTable) {
            return;
        }

        /** return if sales rep extension **/
        $module = Mage::app()->getRequest()->getModuleName();
        if ($module == 'salesrep') {
            return;
        }


        $settings = $this->getUserSettings();
        if (!$settings) {
            return;
        }


        if (!$settings->getIwdLimitUsers()) {
            return;
        }

        $items = $settings->getIwdLimitItems();
        $items = explode(',', $items);

        if (!in_array('customers', $items)) {
            return;
        }

        $customers = $this->getAssignCustomers();

        /**
         *  set ids to -1 for remove all customers from list
         * use this logic if enable customer limitation
         */
        if (count($customers) > 0) {
            $ids = implode(',', $customers);
        } else {
            $ids = '-1';
        }

        $select = $collection->getSelect();
        $select->where('e.entity_id IN (' . $ids . ')');
    }

    /** UPDATE INVOICE GRID **/
    function salesInvoiceGridCollectionLoadBefore($observer)
    {
        $collection = $observer->getOrderInvoiceGridCollection();
        $select = $collection->getSelect();


        $controller = Mage::app()->getRequest()->getControllerName();
        if ($controller != 'sales_order' && $controller != 'adminhtml_sales_order') {
            $select->joinLeft(array('related' => $collection->getTable('salesrep/sales')), 'related.iwd_order_id=main_table.order_id', array('iwd_user_id' => 'iwd_user_id'))
                ->joinLeft(array('user' => $collection->getTable('admin/user')), 'related.iwd_user_id=user.user_id', "CONCAT(user.firstname,' ', user.lastname) as username");
        }

        $settings = $this->getUserSettings();
        if (!$settings) {
            return;
        }

        if (!$settings->getIwdLimitUsers()) {
            return;
        }

        $items = $settings->getIwdLimitItems();
        $items = explode(',', $items);

        if (!in_array('invoices', $items)) {
            return;
        }

        $orders = $this->getOrders();

        /**
         *  set ids to -1 for remove all customers from list
         * use this logic if enable customer limitation
         */

        if (count($orders) > 0) {
            $ids = implode(',', $orders);
        } else {
            $ids = '-1';
        }

        $select->where('main_table.order_id IN (' . $ids . ')');
    }

    public function getOrders()
    {
        $userId = Mage::getModel('admin/session')->getUser()->getId();
        $collection = Mage::getModel('salesrep/sales')->getCollection()->addFieldToFilter('iwd_user_id', array('eq' => $userId));
        $ids = array();
        foreach ($collection as $item) {
            $ids[] = $item->getIwdOrderId();
        }
        return $ids;
    }

    /** UPDATE SHIPMENT GRID **/
//    public function salesShipmentGridCollectionLoadBefore($observer)
//    {
//        $collection = $observer->getOrderShipmentGridCollection();
//        $select = $collection->getSelect();
//        $select->joinLeft(array('orders' => $collection->getTable('sales/order_grid')), 'orders.entity_id=main_table.order_id', array());
////        if ($where = $select->getPart('where')) {
////            $standardFields = array(
////                'iwd_order_increment_id',
////                'iwd_increment_id',
////                'iwd_store_id',
////                'iwd_created_at',
////                'iwd_order_created_at',
////                'iwd_shipment_status',
////                'iwd_shipping_name',
////                'iwd_entity_id',
////                'iwd_total_qty',
////                'iwd_status',
////            );
////            foreach ($where as $key => $condition) {
////                $matches = array();
////                if (preg_match('/(' . implode('|', $standardFields) . ')/', $condition, $matches)) {
////                    if (isset($matches[1]) && !strpos($condition, 'main_table.' . $matches[1])) {
////                        $where[$key] = str_replace($matches[1], 'main_table`.`' . $matches[1], $condition);
////                    }
////                }
////            }
////            $select->setPart('where', $where);
////        }
//        $settings = $this->getUserSettings();
//        if (!$settings) {
//            return;
//        }
//
//        if (!$settings->getLimitUsers()) {
//            return;
//        }
//
//        $items = $settings->getLimitItems();
//        $items = explode(',', $items);
//
//        if (!in_array('shipments', $items)) {
//            return;
//        }
//
//        $customers = $this->getAssignCustomers();
//
//        /**
//         *  set ids to -1 for remove all customers from list
//         * use this logic if enable customer limitation
//         */
//
//        if (count($customers) > 0) {
//            $ids = implode(',', $customers);
//        } else {
//            $ids = '-1';
//        }
//
//
//        $select->where('orders.customer_id IN (' . $ids . ')');
//    }

    public function getUserSettings()
    {
        $user = Mage::getModel('admin/session')->getUser();

        if (!$user) { // is case of magento API request
            return false;
        }
        $userId = $user->getId();
        try {
            $item = Mage::getModel('salesrep/users')->load($userId, 'iwd_user_id');
            if ($item->getId()) {
                return $item;
            }
        } catch (Exception $e) {

        }
        return false;
    }

    private function getAssignCustomers()
    {
        $userId = Mage::getModel('admin/session')->getUser()->getId();
        $ids = array();

        $userEntity = Mage::getModel('salesrep/users')->load($userId, 'iwd_user_id');

        $collection = Mage::getModel('salesrep/customers')->getCollection()->addFieldToFilter('iwd_user_id', array('eq' => $userEntity->getId()));


        foreach ($collection as $item) {
            $id = $item->getIwdLinkedCustomerId();
            if (!empty($id)) {
                $ids[] = $item->getIwdLinkedCustomerId();
            }
        }
        return $ids;
    }

    public function invoiceSaveAfter(Varien_Event_Observer $observer)
    {
        $_event = $observer->getEvent();
        $_invoice = $_event->getInvoice();
        $_order = $_invoice->getOrder();
    }

    public function newCustomerCheck($observer)
    {
        $customer = $observer->getCustomer();
        $isNewCustomer = $customer->isObjectNew();
        if ($isNewCustomer) {
            Mage::getSingleton('admin/session')->setIsNewCustomer(true);
        } else {
            Mage::getSingleton('admin/session')->setIsNewCustomer(false);
        }

    }

    public function applyNewCustomer($observer)
    {
        $customer = $observer->getCustomer();
        $isNew = Mage::getSingleton('admin/session')->getIsNewCustomer(true);
        Mage::getSingleton('admin/session')->setIsNewCustomer(false);
        if ($isNew) {
            //assign customer to current admin
            $userId = Mage::getModel('admin/session')->getUser()->getId();
            $userEntity = Mage::getModel('salesrep/users')->load($userId, 'iwd_user_id');
            if ($userEntity->getData('iwd_autoasssign_user')) {
                $model = Mage::getModel('salesrep/customers');
                $model->setData('iwd_user_id', $userEntity->getId());
                $model->setData('iwd_linked_customer_id', $customer->getId());
                try {
                    $model->save();
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }
        }
    }


    public function createRelatedUserBackend($observer)
    {
        $order = $observer->getOrder();
        $customerId = $order->getCustomerId();
        $collection = Mage::getModel('salesrep/customers')->getCollection()
            ->addFieldToFilter('iwd_linked_customer_id', array('eq' => $customerId));

        $customerRep = $collection->getFirstItem();
        $userId = $customerRep['iwd_user_id'];
        //per customers
        if ($this->isBaseOnCustomer()) {
            if (isset($userId) && !empty($userId) && $userId != false) {
                //assign order to backend user
                $model = Mage::getModel('salesrep/sales');
                if (!$this->isOrderExist($order->getId(), $customerRep['iwd_user_id'])) {
                    $model->setData('iwd_order_id', $order->getId());
                    $model->setData('iwd_user_id', $customerRep['iwd_user_id']);
                    $model->save();
                }
                Mage::helper('salesrep')->sendEmailNotification($userId, $order->getId());
                return;
            }
        } 


        $params = Mage::app()->getRequest()->getParam('order');
        if (!isset($params['salesrep_customer'])) {
            return;
        }

        $customer = (int)$params['salesrep_customer'];
        if ($customer) {
            return;
        }

        $customer = $order->getCustomerId();
        //assign customer to current admin
        $userId = Mage::getModel('admin/session')->getUser()->getId();
        $userEntity = Mage::getModel('salesrep/users')->load($userId, 'iwd_user_id');
        if ($userEntity->getData('iwd_autoasssign_user')) {
            $model = Mage::getModel('salesrep/customers');
            $model->setData('iwd_user_id', $userEntity->getId());
            $model->setData('iwd_linked_customer_id', $customer);
            try {
                $model->save();
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
    }
}