<?php
class IWD_AutoRelatedProducts_Block_Adminhtml_Blocks_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();

        $this->setId('iwd_autorelatedproducts_blocks_grid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('iwd_autorelatedproducts/blocks')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $helper = Mage::helper('iwd_autorelatedproducts');

        $this->addColumn('id', array(
            'header' => $helper->__('ID'),
            'align' => 'left',
            'width' => '50px',
            'index' => 'id',
            'filter_index' => 'id',
            'type' => 'number',
            'sortable' => true
        ));

        $this->addColumn('title', array(
            'header' => $helper->__('Title'),
            'align' => 'right',
            'width' => '150px',
            'index' => 'title',
            'filter_index' => 'title',
            'type' => 'text',
            'sortable' => true
        ));

        $this->addColumn('block_type', array(
            'header' => $helper->__('Type'),
            'type' => 'options',
            'align' => 'right',
            'width' => '100px',
            'index' => 'block_type',
            'filter_index' => 'block_type',
            'sortable' => true,
            'options' => Mage::getSingleton('iwd_autorelatedproducts/block_options_type')->getFlatOptionArray(),
        ));

        $this->addColumn('store_view', array(
            'header' => $helper->__('Store View'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'store_view',
            'filter_index' => 'store_view',
            'type' => 'options',
            'sortable' => true,
            'renderer' => 'iwd_autorelatedproducts/adminhtml_blocks_renderer_store',
            'filter_condition_callback' => array($this, '_customStoreViewFilter'),
            'options' => Mage::getSingleton('iwd_autorelatedproducts/block_options_storeview')->getOptionArray(),
        ));

        $this->addColumn('sort_order', array(
            'header' => $helper->__('Sort order'),
            'type' => 'options',
            'align' => 'right',
            'width' => '50px',
            'index' => 'sort_order',
            'filter_index' => 'sort_order',
            'sortable' => true,
            'options' => Mage::getSingleton('iwd_autorelatedproducts/block_options_sortorder')->getOptionArray(),
        ));

        $this->addColumn('show_out_of_stock', array(
            'header' => $helper->__('Out of stock'),
            'type' => 'options',
            'align' => 'right',
            'width' => '50px',
            'index' => 'show_out_of_stock',
            'filter_index' => 'show_out_of_stock',
            'sortable' => true,
            'options' => Mage::getModel("iwd_autorelatedproducts/block_options_yesno")->toArray(),
        ));

        $this->addColumn('from_date', array(
            'header' => $helper->__('From date'),
            'type' => 'date',
            'align' => 'right',
            'width' => '50px',
            'index' => 'from_date',
            'filter_index' => 'from_date',
            'sortable' => true
        ));

        $this->addColumn('to_date', array(
            'header' => $helper->__('To Date'),
            'type' => 'date',
            'align' => 'right',
            'width' => '50px',
            'index' => 'to_date',
            'filter_index' => 'to_date',
            'sortable' => true
        ));

        $this->addColumn('status', array(
            'header' => $helper->__('Status'),
            'type' => 'options',
            'align' => 'right',
            'width' => '50px',
            'index' => 'status',
            'filter_index' => 'status',
            'sortable' => true,
            'options' => Mage::getSingleton('iwd_autorelatedproducts/block_options_status')->toOptionArray(),
        ));

        $this->addColumn('action', array(
            'header' => $helper->__('Action'),
            'align' => 'left',
            'index' => 'action',
            'width' => '50px',
            'type' => 'text',
            'filter' => false,
            'sortable' => false,
            'is_system' => true,
            'renderer' => new IWD_AutoRelatedProducts_Block_Adminhtml_Blocks_Renderer_Actions(),
        ));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('blocks');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('iwd_autorelatedproducts')->__('Delete block'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('iwd_autorelatedproducts')->__('Are you sure?')
        ));
        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('catalog')->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('catalog')->__('Status'),
                    'values' => Mage::getSingleton('iwd_autorelatedproducts/block_options_status')->toOptionArray()
                )
            )
        ));

        return $this;
    }

    public function getRowUrl($row)
    {
        return false;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    protected function _customStoreViewFilter($collection, $column)
    {
        $value = $column->getFilter()->getValue();
        if ($value === null)
            return null;

        $_collection = Mage::getModel('iwd_autorelatedproducts/blocks')->getCollection();

        $ids = array();
        foreach ($_collection as $block) {
            $store_views = unserialize($block->getStoreView());
            if (in_array($value, $store_views) && !in_array(0, $store_views))
                $ids[$block->getId()] = $block->getId();
            if ($value == 0 && in_array(0, $store_views))
                $ids[$block->getId()] = $block->getId();
        }

        return $this->getCollection()->addFieldToFilter('id', array('in' => $ids))->load();
    }
}