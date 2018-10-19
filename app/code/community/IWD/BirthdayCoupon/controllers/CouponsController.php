<?php
class IWD_BirthdayCoupon_CouponsController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('system')
            ->_title($this->__('IWD - Sent Coupons'));

        $this->_addBreadcrumb(
            Mage::helper('iwd_coupon')->__('Sent Coupons'),
            Mage::helper('iwd_coupon')->__('Sent Coupons')
        );

        return $this;
    }

    public function indexAction()
    {
        $this->reportAction();
    }

    public function reportAction()
    {
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('iwd_coupon/adminhtml_coupons_report'));
        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('iwd_coupon/adminhtml_coupons_report_grid')->toHtml()
        );
    }
    /*iwd fix*/
    protected function _isAllowed()
    {
        return true;
    }
}