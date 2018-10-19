<?php
class IWD_AutoRelatedProducts_Block_Adminhtml_Blocks_Edit_Grid_Related extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();

        $this->setId('iwd_autorelatedproducts_related');

        if ($this->_getCountRelatedProducts() > 0)
            $this->setDefaultFilter(array('related_products' => 1));

        //$this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }


    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in category flag
        if ($column->getId() == 'related_products') {
            $product_ids = $this->_getRelatedProducts();

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
        $this->addColumn('related_products', array(
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            'name' => 'related_products',
            'field_name' => 'related_selected_products_ids[]',
            'values' => $this->_getRelatedProducts(),
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
        return $this->getUrl('*/*/grid_related_products', array('_current' => true));
    }

    protected function _getCountRelatedProducts()
    {
        return count($this->_getRelatedProducts());
    }

    protected function _getRelatedProducts()
    {
        $block_id = $this->getRequest()->getParam('id');
        if (empty($block_id))
            return array();

        $block = Mage::getModel('iwd_autorelatedproducts/blocks')->load($block_id);
        if (empty($block))
            return array();

        $related_products_id_serialized = $block->getRelatedProductsIdSerialized();
        if (empty($related_products_id_serialized))
            return array();

        return unserialize($block->getRelatedProductsIdSerialized());
    }
}