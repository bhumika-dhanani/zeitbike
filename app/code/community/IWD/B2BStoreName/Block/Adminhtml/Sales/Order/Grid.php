<?php
class IWD_B2BStoreName_Block_Adminhtml_Sales_Order_Grid extends IWD_OrderManager_Block_Adminhtml_Sales_Order_Grid
{
    protected $_options = false;

    protected function _prepareColumns()
    {
        $parent = parent::_prepareColumns();

        $this->addColumnAfter('b2b_store_name', array(
            'header' => Mage::helper('IWD_B2BStoreName')->__('B2B Store Name'),
            'index' => 'b2b_store_name',
            'type' => 'options',
            'filter_index' => 'store_name',
            'sortable' => false,
            'width' => '100px',
            'options' => $this->_getOptions(),
            'renderer' => 'IWD_B2BStoreName_Block_Adminhtml_Sales_Order_Render_Options',
            'filter_condition_callback' => array($this, '_roleOrderFilter'),
        ),'shipping_name');

        $this->sortColumnsByOrder();

        return $parent;
    }

    protected function _getOptions()
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

        $this->_options = $methods;
        return $this->_options;
    }
    protected function _roleOrderFilter($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue())
        {
            return $this;
        }

        $storename = Mage::getModel('b2b/customer')->load($value)->getData('store_name');
        $this->getCollection()->getSelect()
            ->joinLeft( array('b2b_store' => 'iwd_b2b_customer_info'), 'b2b_store.customer_id = `main_table`.customer_id', array('b2b_store.store_name' => 'b2b_store.store_name'))
            ->where("b2b_store.store_name like ?", "%$storename%");

        return $this;
    }
}