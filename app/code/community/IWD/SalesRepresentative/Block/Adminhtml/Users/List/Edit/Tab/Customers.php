<?php
class IWD_SalesRepresentative_Block_Adminhtml_Users_List_Edit_Tab_Customers extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set grid params
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('related_customers_grid');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
        if ($this->_getUser()->getId()) {
            $this->setDefaultFilter(array('in_customers' => 1));
        }
        if ($this->isReadonly()) {
            $this->setFilterVisibility(false);
        }
    }

 
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
        if ($column->getId() == 'in_customers') {
            $customersIds = $this->_getSelectedCustomers();
            if (empty($customersIds)) {
                $customersIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in' => $customersIds));
            } else {
                if($customersIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin' => $customersIds));
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
    	
     	 $collection = Mage::getResourceModel('customer/customer_collection')
			            ->addNameToSelect()
			            ->addAttributeToSelect('email')
			            ->addAttributeToSelect('created_at')
			            ->addAttributeToSelect('group_id')
			            ->joinAttribute('billing_postcode', 'customer_address/postcode', 'default_billing', null, 'left')
			            ->joinAttribute('billing_city', 'customer_address/city', 'default_billing', null, 'left')
			            ->joinAttribute('billing_telephone', 'customer_address/telephone', 'default_billing', null, 'left')
			            ->joinAttribute('billing_region', 'customer_address/region', 'default_billing', null, 'left')
			            ->joinAttribute('billing_country_id', 'customer_address/country_id', 'default_billing', null, 'left');

       
     
 
        if ($this->isReadonly()) {
            $customersIds = $this->_getSelectedCustomers();
            if (empty($customersIds)) {
                $customersIds = array(0);
            }
            $collection->addFieldToFilter('entity_id', array('in' => $customersIds));
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
            $this->addColumn('in_customers', array(
                'header_css_class'  => 'a-center',
                'type'              => 'checkbox',
                'name'              => 'in_customers',
                'values'            => $this->_getSelectedCustomers(),
                'align'             => 'center',
                'index'             => 'entity_id'
            ));
        }

        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('salesrep')->__('ID'),
            'sortable'  => true,
            'width'     => 60,
            'index'     => 'entity_id'
        ));

        $this->addColumn('name', array(
            'header'    => Mage::helper('customer')->__('Name'),
            'index'     => 'name'
        ));
        $this->addColumn('email', array(
            'header'    => Mage::helper('customer')->__('Email'),
            'width'     => '150',
            'index'     => 'email'
        ));

        $groups = Mage::getResourceModel('customer/group_collection')
            ->addFieldToFilter('customer_group_id', array('gt'=> 0))
            ->load()
            ->toOptionHash();

        $this->addColumn('group', array(
            'header'    =>  Mage::helper('customer')->__('Group'),
            'width'     =>  '100',
            'index'     =>  'group_id',
            'type'      =>  'options',
            'options'   =>  $groups,
        ));

        $this->addColumn('Telephone', array(
            'header'    => Mage::helper('customer')->__('Telephone'),
            'width'     => '100',
            'index'     => 'billing_telephone'
        ));
        
        
        $this->addColumn('billing_postcode', array(
        		'header'    => Mage::helper('customer')->__('ZIP'),
        		'width'     => '90',
        		'index'     => 'billing_postcode',
        ));
        
        $this->addColumn('billing_country_id', array(
        		'header'    => Mage::helper('customer')->__('Country'),
        		'width'     => '100',
        		'type'      => 'country',
        		'index'     => 'billing_country_id',
        ));
        
        $this->addColumn('billing_region', array(
        		'header'    => Mage::helper('customer')->__('State/Province'),
        		'width'     => '100',
        		'index'     => 'billing_region',
        ));
        
        $this->addColumn('customer_since', array(
        		'header'    => Mage::helper('customer')->__('Customer Since'),
        		'type'      => 'datetime',
        		'align'     => 'center',
        		'index'     => 'created_at',
        		'gmtoffset' => true
        ));
        
        if (!Mage::app()->isSingleStoreMode()) {
        	$this->addColumn('website_id', array(
        			'header'    => Mage::helper('customer')->__('Website'),
        			'align'     => 'center',
        			'width'     => '80px',
        			'type'      => 'options',
        			'options'   => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(true),
        			'index'     => 'website_id',
        	));
        }

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
            : $this->getUrl('*/*/customersGrid', array('_current' => true));
    }

    /**
     * Retrieve selected related products
     *
     * @return array
     */
    protected function _getSelectedCustomers()
    {
    	
        $customers = $this->getCustomersRelated();
        if (!is_array($customers)) {
            $customers = array_keys($this->getSelectedRelatedCustomers());
        }
        return $customers;
    }

    /**
     * Retrieve related products
     *
     * @return array
     */
    public function getSelectedRelatedCustomers()
    {
        $customers = array();
        foreach (Mage::registry('current_user')->getRelatedCustomers() as $customer) {
            $customers[$customer->getId()] = array('position' => $customer->getPosition());
        }
        return $customers;
    }
    
    public function getRowUrl($item){
    	return '';
    }
}
