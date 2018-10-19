<?php

class IWD_B2BStoreName_Model_Observer
{

    function salesInvoiceGridCollectionLoadBefore($observer)
    {
        $collection = $observer->getOrderInvoiceGridCollection();
        $select = $collection->getSelect();
        $select->joinLeft(array('orders' => $collection->getTable('sales/order_grid')), 'orders.entity_id=main_table.order_id', array());

        $customers = $this->getAssignCustomers();

        if (count($customers) > 0) {
            $ids = implode(',', $customers);
        } else {
            $ids = '-1';
        }


        $select->where('orders.customer_id IN (' . $ids . ')');

    }

    private function getAssignCustomers()
    {
        $ids = array();
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $query = 'SELECT customer_id,store_name FROM ' . $resource->getTableName('iwd_b2b_customer_info');
        $results = $readConnection->fetchAll($query);
        foreach($results as $res){
            if($res['customer_id'] != ''){
                $ids[] = $res['customer_id'];
            }
        }

        return $ids;
    }


}