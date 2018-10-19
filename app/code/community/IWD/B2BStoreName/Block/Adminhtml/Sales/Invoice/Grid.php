<?php

class IWD_B2BStoreName_Block_Adminhtml_Sales_Invoice_Grid extends IWD_SalesRepresentative_Block_Adminhtml_Sales_Invoice_Grid
{

    protected $_options = false;

    protected function _prepareColumns()
    {
        $parent = parent::_prepareColumns();

        $this->addColumn('created_at', array(
            'header'    => Mage::helper('sales')->__('Invoice Date'),
            'index'     => 'created_at',
            'filter_index' => 'main_table.created_at',
            'type'      => 'datetime',
        ));

        $this->addColumn('order_created_at', array(
            'header'    => Mage::helper('sales')->__('Order Date'),
            'index'     => 'order_created_at',
            'filter_index' => 'main_table.order_created_at',
            'type'      => 'datetime',
        ));

        $this->addColumn('grand_total', array(
            'header'    => Mage::helper('customer')->__('Amount'),
            'index'     => 'grand_total',
            'filter_index' => 'main_table.grand_total',
            'type'      => 'currency',
            'align'     => 'right',
            'currency'  => 'order_currency_code',
        ));

        $this->addColumn('state', array(
            'header'    => Mage::helper('sales')->__('Status'),
            'index'     => 'state',
            'filter_index' => 'main_table.state',
            'type'      => 'options',
            'options'   => Mage::getModel('sales/order_invoice')->getStates(),
        ));

        $this->addColumn('increment_id', array(
            'header'    => Mage::helper('sales')->__('Invoice #'),
            'index'     => 'increment_id',
            'filter_index' => 'main_table.increment_id',
            'type'      => 'text',
        ));

        $this->addColumnAfter('b2b_store_name', array(
            'header' => Mage::helper('IWD_B2BStoreName')->__('B2B Store Name'),
            'index' => 'b2b_store_name2',
            'type' => 'options',
            'filter_index' => 'store_name',
            'sortable' => false,
            'width' => '150px',
            'options' => $this->_getB2BOptions(),
            'renderer' => 'IWD_B2BStoreName_Block_Adminhtml_Sales_Invoice_Render_Options',
            'filter_condition_callback' => array($this, '_roleInvoiceFilter'),
        ), 'iwd_username');

        $this->sortColumnsByOrder();

        return $parent;
    }

    protected function _getB2BOptions()
    {

        $methods = array();

        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $query = 'SELECT entity_id,store_name FROM ' . $resource->getTableName('iwd_b2b_customer_info');
        $results = $readConnection->fetchAll($query);
        foreach($results as $res){
            if($res['store_name'] != '' && $res['entity_id'] != ''){
                $methods[$res['entity_id']] = $res['store_name'];
            }
        }

        $methods = array_unique($methods);
        natsort($methods);

        //echo "<pre>".print_r($methods,true)."</pre>"; die();
        $this->_options = $methods;

        return $this->_options;

    }

    protected function _roleInvoiceFilter($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue())
        {
            return $this;
        }

        $storename = Mage::getModel('b2b/customer')->load($value)->getData('store_name');

        $this->getCollection()->getSelect()
            ->joinLeft( array('order' => 'sales_flat_order'), 'order.increment_id = `main_table`.order_increment_id', array('order.customer_id'))
            ->joinLeft( array('b2b_store' => 'iwd_b2b_customer_info'), 'b2b_store.customer_id = order.customer_id', array('b2b_store.store_name' => 'b2b_store.store_name'))
            ->where("b2b_store.store_name like ?", "%$storename%");
        return $this;
    }

}