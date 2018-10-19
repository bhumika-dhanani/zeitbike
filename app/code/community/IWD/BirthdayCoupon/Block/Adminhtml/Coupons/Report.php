<?php
class IWD_BirthdayCoupon_Block_Adminhtml_Coupons_Report extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'iwd_coupon';
        $this->_controller = 'adminhtml_coupons_report';
        $this->_headerText = Mage::helper('iwd_coupon')->__('IWD Automatic Coupon Emailer - Sent Coupons');

        parent::__construct();
        $this->_removeButton('add');
    }
}