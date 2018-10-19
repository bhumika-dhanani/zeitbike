<?php
class IWD_BirthdayCoupon_Model_Coupon_Newslettersubscribe extends IWD_BirthdayCoupon_Model_Coupons
{
    protected $XML_PATH_EMAIL_TEMPLATE_WITH_COUPON = 'iwd_coupon/newsletter_subscribe/template_with_coupon';
    protected $XML_PATH_EMAIL_TEMPLATE_COUPON_NONE = 'iwd_coupon/newsletter_subscribe/template_none_coupon';
    protected $XML_PATH_IS_ENABLED = 'iwd_coupon/newsletter_subscribe/enabled';
    protected $XML_PATH_RULE_ID = 'iwd_coupon/newsletter_subscribe/coupon_code';
    protected $XML_PATH_EXPIRE_DAYS = 'iwd_coupon/newsletter_subscribe/expire';
    protected $XML_PATH_EMAIL_IDENTITY = 'iwd_coupon/newsletter_subscribe/email_identity';

    public function __construct()
    {
        parent::__construct(IWD_BirthdayCoupon_Model_System_Config_Cause::SUBSCRIBE);
    }

    public function sendCoupon($subscriber)
    {
        if ($this->isEnabled()) {
            try {
                $isSentAlready = $this->isSentAlready($subscriber->getSubscriberEmail(), IWD_BirthdayCoupon_Model_System_Config_Cause::SUBSCRIBE);
                if (!$isSentAlready) {
                    $customer_id = $subscriber->getCustomerId();
                    $item = $this->toCustomerCouponObject($customer_id, $subscriber->getSubscriberEmail());
                    $this->sendEmail($item);
                }
            } catch (Exception $e) {
                Mage::log(__CLASS__ . ": " . $e->getMessage());
                return false;
            }
        }

        return true;
    }

    //TODO: add logic
    public function deleteCoupon($subscriber)
    {
    }

    private function toCustomerCouponObject($customer_id, $customer_email="")
    {
        $customer_coupon = Mage::getModel('iwd_coupon/customercoupon');
        $customer = Mage::getModel('customer/customer');

        if($customer_id == 0) {
            $customer->setEmail($customer_email)
                ->setId(0)
                ->setFirstname(Mage::helper('iwd_coupon')->__("Guest"))
                ->setLastname("")
                ->setGroupId(0);
        } else {
            $customer = $customer->load($customer_id);
        }

        $customer_coupon->setCustomer($customer);

        return $customer_coupon;
    }
}