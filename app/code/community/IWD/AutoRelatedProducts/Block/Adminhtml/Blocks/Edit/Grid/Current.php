<?php
class IWD_AutoRelatedProducts_Block_Adminhtml_Blocks_Edit_Grid_Current extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();

        $this->setId('iwd_autorelatedproducts_current');

        if ($this->_getCountCurrentProducts() > 0)
             $this->setDefaultFilter(array('current_products' => 1));

        //$this->setSaveParametersInSession(true);
        $this->setUseAjax(true);;
    }

    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in category flag
        if ($column->getId() == 'current_products') {
            $product_ids = $this->_getCurrentProducts();

            if (empty($product_ids)) {
                $product_ids = array(0);
            }

            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in' => $product_ids));
            } elseif (!empty($productIds)) {
                $this->getCollection()->addFieldToFilter('entity_id', array('nin' => $product_ids));
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('sku');

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }


    protected function _prepareColumns()
    {
        $this->addColumn('current_products', array(
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            'name' => 'current_products',
            'field_name' => 'current_selected_products_ids[]',
            'values' => $this->_getCurrentProducts(),
            'align' => 'center',
            'index' => 'entity_id'
        ));

        $this->addColumn('entity_id', array(
            'header' => Mage::helper('iwd_autorelatedproducts')->__('ID'),
            'sortable' => true,
            'type' => 'number',
            'width' => '50px',
            'index' => 'entity_id',
        ));

        $this->addColumn('sku', array(
            'header' => Mage::helper('iwd_autorelatedproducts')->__('SKU'),
            'width' => '100px',
            'index' => 'sku'
        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('iwd_autorelatedproducts')->__('Product Name'),
            'index' => 'name',
            'type' => 'text',
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid_current_products', array('_current' => true));
    }

    protected function _getCountCurrentProducts()
    {
        return count($this->_getCurrentProducts());
    }

    protected function _getCurrentProducts()
    {
        $block_id = $this->getRequest()->getParam('id');
        if (empty($block_id))
            return array();

        $block = Mage::getModel('iwd_autorelatedproducts/blocks')->load($block_id);
        if (empty($block))
            return array();

        $current_products_id_serialized = $block->getCurrentProductsIdSerialized();
        if (empty($current_products_id_serialized))
            return array();

        return unserialize($block->getCurrentProductsIdSerialized());
    }
}