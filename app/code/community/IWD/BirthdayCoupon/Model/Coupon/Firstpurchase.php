<?php
class IWD_BirthdayCoupon_Model_Coupon_Firstpurchase extends IWD_BirthdayCoupon_Model_Coupons
{
    protected $XML_PATH_EMAIL_TEMPLATE_WITH_COUPON = 'iwd_coupon/first_purchase/template_with_coupon';
    protected $XML_PATH_EMAIL_TEMPLATE_COUPON_NONE = 'iwd_coupon/first_purchase/template_none_coupon';
    protected $XML_PATH_IS_ENABLED = 'iwd_coupon/first_purchase/enabled';
    protected $XML_PATH_RULE_ID = 'iwd_coupon/first_purchase/coupon_code';
    protected $XML_PATH_EXPIRE_DAYS = 'iwd_coupon/first_purchase/expire';
    protected $XML_PATH_EMAIL_IDENTITY = 'iwd_coupon/first_purchase/email_identity';

    public function __construct()
    {
        parent::__construct(IWD_BirthdayCoupon_Model_System_Config_Cause::FIRST_PURCHASE);
    }

    public function sendCoupon($order)
    {
        if ($this->isEnabled()) {
            try {
                if ($this->isFirstPurchase($order)) {
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

    private function isFirstPurchase($order)
    {
        $customerEmail = $order->getCustomerEmail();
        $storeId = $order->getStoreId();
        $count = Mage::getModel('sales/order')
            ->getCollection()
            ->addFieldToFilter('store_id', $storeId)
            ->addFieldToFilter('customer_email', $customerEmail)
            ->count();
        return ($count == 1);
    }
}