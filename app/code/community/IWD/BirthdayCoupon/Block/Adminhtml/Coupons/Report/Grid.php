<?php
class IWD_BirthdayCoupon_Block_Adminhtml_Coupons_Report_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();

        $this->setId('iwd_coupon_coupons_report_grid');

        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('iwd_coupon/customercoupon')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $groups = Mage::getResourceModel('customer/group_collection')->load()->toOptionHash();
        $causes = Mage::getModel('iwd_coupon/system_config_cause')->GetCauses();

        $this->addColumn('id', array(
            'header' => Mage::helper('iwd_coupon')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'id',
            'filter_index' => 'id',
            'type' => 'number',
            'sortable' => true
        ));
        $this->addColumn('cause', array(
            'header' => Mage::helper('iwd_coupon')->__('Cause'),
            'align' => 'left',
            'index' => 'cause',
            'filter_index' => 'cause',
            'width' => '70px',
            'sortable' => true,
            'type' => 'options',
            'options' => $causes,
        ));
        $this->addColumn('customer_email', array(
            'header' => Mage::helper('iwd_coupon')->__('Email'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'customer_email',
            'filter_index' => 'customer_email',
            'type' => 'text',
            'sortable' => true
        ));
        $this->addColumn('customer_id', array(
            'header' => Mage::helper('iwd_coupon')->__('Customer Id'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'customer_id',
            'filter_index' => 'customer_id',
            'type' => 'text',
            'sortable' => true
        ));
        $this->addColumn('customer_firstname', array(
            'header' => Mage::helper('iwd_coupon')->__('Customer Name'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'customer_firstname',
            'filter_index' => 'customer_firstname',
            'type' => 'text',
            'sortable' => true,
            'filter_condition_callback' => array($this, '_customerNameFilter'),
            'renderer' => new IWD_BirthdayCoupon_Block_Adminhtml_Coupons_Report_Renderer_Customername(),
        ));
        $this->addColumn('customer_group_id', array(
            'header' => Mage::helper('iwd_coupon')->__('Group'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'customer_group_id',
            'filter_index' => 'customer_group_id',
            'sortable' => true,
            'type' => 'options',
            'options' => $groups,
        ));
        $this->addColumn('customer_dob', array(
            'header' => Mage::helper('iwd_coupon')->__('Birthday'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'customer_dob',
            'filter_index' => 'customer_dob',
            'type' => 'date',
            'sortable' => true
        ));
        $this->addColumn('coupon_code', array(
            'header' => Mage::helper('iwd_coupon')->__('Coupon Code'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'coupon_code',
            'filter_index' => 'coupon_code',
            'type' => 'text',
            'sortable' => true
        ));
        $this->addColumn('date_receive', array(
            'header' => Mage::helper('iwd_coupon')->__('Receive at'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'date_receive',
            'filter_index' => 'date_receive',
            'type' => 'datetime',
            'sortable' => true
        ));
        $this->addColumn('date_expire', array(
            'header' => Mage::helper('iwd_coupon')->__('Expire to'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'date_expire',
            'filter_index' => 'date_expire',
            'type' => 'datetime',
            'sortable' => true
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return false;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    protected function _customerNameFilter($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }

        $this->getCollection()->getSelect()->where(
            "customer_lastname like ?
             OR customer_firstname like ?"
            , "%$value%");

        return $this;
    }
}