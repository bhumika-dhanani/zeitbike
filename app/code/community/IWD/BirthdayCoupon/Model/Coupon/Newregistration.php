<?php
class IWD_BirthdayCoupon_Model_Coupon_Newregistration extends IWD_BirthdayCoupon_Model_Coupons
{
    protected $XML_PATH_EMAIL_TEMPLATE_WITH_COUPON = 'iwd_coupon/new_registration/template_with_coupon';
    protected $XML_PATH_EMAIL_TEMPLATE_COUPON_NONE = 'iwd_coupon/new_registration/template_none_coupon';
    protected $XML_PATH_IS_ENABLED = 'iwd_coupon/new_registration/enabled';
    protected $XML_PATH_RULE_ID = 'iwd_coupon/new_registration/coupon_code';
    protected $XML_PATH_EXPIRE_DAYS = 'iwd_coupon/new_registration/expire';
    protected $XML_PATH_EMAIL_IDENTITY = 'iwd_coupon/new_registration/email_identity';

    public function __construct()
    {
        parent::__construct(IWD_BirthdayCoupon_Model_System_Config_Cause::NEW_REGISTRATION);
    }

    public function sendCoupon($customer)
    {
        if ($this->isEnabled()) {
            try {
                $isSentAlready = $this->isSentAlready($customer->getEmail(), IWD_BirthdayCoupon_Model_System_Config_Cause::NEW_REGISTRATION);
                if (!$isSentAlready) {
                    $customer_coupon = Mage::getModel('iwd_coupon/customercoupon')->setCustomer($customer);
                    if ($this->sendEmail($customer_coupon))
                        return $customer_coupon->getId();
                }
            } catch (Exception $e) {
                Mage::log(__CLASS__ . ": " . $e->getMessage());
                return false;
            }
        }
        return false;
    }
}