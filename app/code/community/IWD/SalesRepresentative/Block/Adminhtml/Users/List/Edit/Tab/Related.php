<?php
class IWD_SalesRepresentative_Block_Adminhtml_Users_List_Edit_Tab_Related extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set grid params
     *
     */
    public function __construct()
    {

        parent::__construct();
        $this->setId('related_product_grid');
        $this->setDefaultSort('iwd_entity_id');
        $this->setUseAjax(true);
        if ($this->_getUser()->getId()) {
            $this->setDefaultFilter(array('in_products' => 1));
        }
        if ($this->isReadonly()) {
            $this->setFilterVisibility(false);
        }
    }

    /**
     * Retirve currently edited product model
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _getUser()
    {
        return Mage::registry('current_user');

    }

    /**
     * Add filter
     *
     * @param object $column
     * @return Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Related
     */
    protected function _addColumnFilterToCollection($column)
    {

        // Set custom filter for in product flag
        if ($column->getId() == 'in_products') {
            $productIds = $this->_getSelectedProducts();

            if (empty($productIds)) {
                $productIds = 0;
            }

            if ($column->getFilter()->getValue()) {

                $this->getCollection()->addFieldToFilter('entity_id', array('in' => $productIds));
            } else {
                if($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin' => $productIds));
                }
            }

        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

    /**
     * Prepare collection
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection(){
     	$collection = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('*');
     	$collection->addAttributeToFilter('type_id', array('in'=>array('simple', 'virtual','downloadable')));
 
        if ($this->isReadonly()) {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = array(0);
            }
            $collection->addFieldToFilter('iwd_entity_id', array('in' => $productIds));
        }
    	
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Checks when this block is readonly
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return $this->_getUser()->getRelatedReadonly();
    }

    /**
     * Add columns to grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        if (!$this->isReadonly()) {
            $this->addColumn('in_products', array(
                'header_css_class'  => 'a-center',
                'type'              => 'checkbox',
                'name'              => 'in_products',
                'values'            => $this->_getSelectedProducts(),
                'align'             => 'center',
                'index'             => 'entity_id'
            ));
        }

        $this->addColumn('iwd_entity_id', array(
            'header'    => Mage::helper('salesrep')->__('ID'),
            'sortable'  => true,
            'width'     => 60,
            'index'     => 'entity_id'
        ));

        $this->addColumn('iwd_name', array(
            'header'    => Mage::helper('salesrep')->__('Name'),
            'index'     => 'name'
        ));

        $this->addColumn('iwd_status', array(
            'header'    => Mage::helper('salesrep')->__('Status'),
            'width'     => 90,
            'index'     => 'status',
            'type'      => 'options',
            'options'   => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ));

        $this->addColumn('iwd_visibility', array(
            'header'    => Mage::helper('salesrep')->__('Visibility'),
            'width'     => 90,
            'index'     => 'visibility',
            'type'      => 'options',
            'options'   => Mage::getSingleton('catalog/product_visibility')->getOptionArray(),
        ));

        $this->addColumn('iwd_sku', array(
            'header'    => Mage::helper('salesrep')->__('SKU'),
            'width'     => 80,
            'index'     => 'sku'
        ));

        $this->addColumn('price', array(
            'header'        => Mage::helper('salesrep')->__('Price'),
            'type'          => 'currency',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            'index'         => 'price'
        ));

        $this->addColumn('iwd_rate', array(
        		'header'        => Mage::helper('salesrep')->__('Rate'),
        		'index'         => 'iwd_rate',
        		'renderer'  	=> 'IWD_SalesRepresentative_Block_Adminhtml_Users_List_Render_Rate'        		        		
        ));

        return parent::_prepareColumns();
    }

    /**
     * Rerieve grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getData('grid_url')
            ? $this->getData('grid_url')
            : $this->getUrl('*/*/relatedGrid', array('_current' => true));
    }

    /**
     * Retrieve selected related products
     *
     * @return array
     */
    protected function _getSelectedProducts()
    {
    	
        $products = $this->getProductsRelated();
        if (!is_array($products)) {
            $products = array_keys($this->getSelectedRelatedProducts());
        }
        return $products;
    }

    /**
     * Retrieve related products
     *
     * @return array
     */
    public function getSelectedRelatedProducts()
    {

        $products = array();
        foreach (Mage::registry('current_user')->getRelatedProducts() as $product) {
            $products[$product->getId()] = array('position' => $product->getPosition());
        }

        return $products;
    }
    
    public function getRowUrl($item){
    	return '';
    }
}
