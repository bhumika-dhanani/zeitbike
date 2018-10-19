<?php
class IWD_BirthdayCoupon_Model_Customercoupon extends Mage_Core_Model_Abstract
{
    private $customer;
    private $coupon;
    private $rule;

    protected function _construct()
    {
        parent::_construct();
        $this->_init('iwd_coupon/customercoupon');
    }

    public function  getCustomer()
    {
        return $this->customer;
    }

    public function  getCoupon()
    {
        return $this->coupon;
    }

    public function  getRule()
    {
        return $this->rule;
    }

    public function setCustomer($customer)
    {
        if (!empty($customer)) {
            $this->customer = $customer;
            $dob = $customer->getDob();
            $dob = empty($dob) ? null : $dob;

            $this->setCustomerEmail($customer->getEmail())
                ->setCustomerDob($dob)
                ->setCustomerId($customer->getId())
                ->setCustomerFirstname($customer->getFirstname())
                ->setCustomerLastname($customer->getLastname())
                ->setCustomerGroupId($customer->getGroupId());
        }

        return $this;
    }

    public function setCoupon($coupon)
    {
        if (!empty($coupon)) {
            $this->coupon = $coupon;
            $this->setCouponCode($coupon->getCode())
                ->setDateExpire($coupon->getExpirationDate());
        }
        return $this;
    }

    public function setRule($rule)
    {
        if (!empty($rule)) {
            $this->rule = $rule;
            $this->setCardId($rule->getId());
        }
        return $this;
    }

    public function SaveItem()
    {
        try {
            $this->save();
        } catch (Exception $e) {
            Mage::log(__CLASS__.": ".$e->getMessage());
        }
    }
}
