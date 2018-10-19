<?php
class IWD_BirthdayCoupon_Model_Coupon_Goodcustomer extends IWD_BirthdayCoupon_Model_Coupons
{
    protected $XML_PATH_EMAIL_TEMPLATE_WITH_COUPON = 'iwd_coupon/good_customer/template_with_coupon';
    protected $XML_PATH_EMAIL_TEMPLATE_COUPON_NONE = 'iwd_coupon/good_customer/template_none_coupon';
    protected $XML_PATH_IS_ENABLED = 'iwd_coupon/good_customer/enabled';
    protected $XML_PATH_RULE_ID = 'iwd_coupon/good_customer/coupon_code';
    protected $XML_PATH_EXPIRE_DAYS = 'iwd_coupon/good_customer/expire';
    protected $XML_PATH_EMAIL_IDENTITY = 'iwd_coupon/good_customer/email_identity';

    protected $XML_PATH_SPENT_MORE_THEN = 'iwd_coupon/good_customer/spend_more_than';
    protected $XML_PATH_PERMANENT_DISCOUNTS = 'iwd_coupon/good_customer/permanent_discounts';

    public function __construct()
    {
        parent::__construct(IWD_BirthdayCoupon_Model_System_Config_Cause::GOOD_CUSTOMER);
    }

    public function sendCoupon($order)
    {
        if ($this->isEnabled()) {
            try {
                if ($this->isGoodCustomer($order)) {
                    $customer_coupon = $this->getCustomerCouponFromOrder($order);
                    $this->sendEmail($customer_coupon);
                }
            } catch (Exception $e) {
                Mage::log(__CLASS__.": ".$e->getMessage());
                return false;
            }
        }

        return true;
    }

    protected function getSpentMoreThen()
    {
        return Mage::getStoreConfig($this->XML_PATH_SPENT_MORE_THEN, Mage::app()->getStore());
    }

    protected function getPermanentDiscounts()
    {
        return Mage::getStoreConfig($this->XML_PATH_PERMANENT_DISCOUNTS, Mage::app()->getStore());
    }

    protected function isSend($email)
    {
        if ($this->getPermanentDiscounts() == 1)
            return true;
        return !$this->isSentAlready($email, IWD_BirthdayCoupon_Model_System_Config_Cause::GOOD_CUSTOMER);
    }

    protected function getCustomerCouponFromOrder($order)
    {
        $customer = Mage::helper('iwd_coupon')->getCustomerFromOrder($order);
        $customer_coupon = Mage::getModel('iwd_coupon/customercoupon');

        $customer_coupon->setCustomerEmail($customer['customer_email'])
            ->setCustomerDob($customer['customer_dob'])
            ->setCustomerId($customer['customer_id'])
            ->setCustomerFirstname($customer['customer_firstname'])
            ->setCustomerLastname($customer['customer_lastname'])
            ->setCustomerGroupId($customer['customer_group_id']);

        return $customer_coupon;
    }

    private function isGoodCustomer($order)
    {
        $customerEmail = $order->getCustomerEmail();
        $storeId = $order->getStoreId();
        $total = 'total_paid';

        $orderTotals = Mage::getModel('sales/order')
            ->getCollection()
            ->addFieldToFilter('store_id', $storeId)
            ->addFieldToFilter('customer_email', $customerEmail)
            ->addFieldToFilter('state', array('neq' => 'canceled'))
            ->getColumnValues($total);

        if (array_sum($orderTotals) >= $this->getSpentMoreThen())
            return $this->isSend($order->getCustomerEmail());

        return false;
    }
}