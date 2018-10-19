<?php
class IWD_BirthdayCoupon_Model_Coupon_Birthday extends IWD_BirthdayCoupon_Model_Coupons
{
    protected $XML_PATH_EMAIL_TEMPLATE_WITH_COUPON = 'iwd_coupon/birthday_coupon/template_with_coupon';
    protected $XML_PATH_EMAIL_TEMPLATE_COUPON_NONE = 'iwd_coupon/birthday_coupon/template_none_coupon';
    protected $XML_PATH_IS_ENABLED = 'iwd_coupon/birthday_coupon/enabled';
    protected $XML_PATH_RULE_ID = 'iwd_coupon/birthday_coupon/coupon_code';
    protected $XML_PATH_EXPIRE_DAYS = 'iwd_coupon/birthday_coupon/expire';
    protected $XML_PATH_EMAIL_IDENTITY = 'iwd_coupon/birthday_coupon/email_identity';

    protected $XML_PATH_BIRTHDAY_COUPON_SENT_BEFORE = 'iwd_coupon/birthday_coupon/sent_before';
    protected $notEarlierDays = 360; // send new coupon after update not earlier than X days

    public function __construct()
    {
        parent::__construct(IWD_BirthdayCoupon_Model_System_Config_Cause::BIRTHDAY);
    }

    public function sendCoupons()
    {
        if ($this->isEnabled()) {
            try {
                $customers_coupon = $this->getCustomerCouponCollection();
                foreach ($customers_coupon as $item) {
                    $this->sendEmail($item);
                }
            } catch (Exception $e) {
                Mage::log(__CLASS__.": ".$e->getMessage());
                return false;
            }
        }

        return true;
    }

    protected function getCustomerCouponCollection()
    {
        $customersCoupon1 = ($this->hasGuestCategory()) ? $this->getCustomersCouponByGuestCustomers() : array();
        $customersCoupon2 = $this->getCustomersCouponByRegisterCustomers();
        return array_merge($customersCoupon1, $customersCoupon2);
    }

    protected function getCustomersCouponByRegisterCustomers()
    {
        $customersCoupon = array();
        $customers = $this->getCustomerCollection();
        foreach ($customers as $customer) {
            $isSentAlready = $this->isSentAlready($customer->getEmail(), IWD_BirthdayCoupon_Model_System_Config_Cause::BIRTHDAY, $this->notEarlierDays);
            if (!$isSentAlready) {
                $customer_coupon = Mage::getModel('iwd_coupon/customercoupon');
                $customer_coupon->setCustomer($customer);
                $customersCoupon[$customer->getEmail()] = $customer_coupon;
            }
        }

        return $customersCoupon;
    }

    protected function getCustomersCouponByGuestCustomers()
    {
        $customersCoupon = array();
        $customers = $this->getGuestCustomerCollection();

        foreach ($customers as $id_email => $customer) {
            $isSentAlready = $this->isSentAlready($customer['customer_email'], IWD_BirthdayCoupon_Model_System_Config_Cause::BIRTHDAY, $this->notEarlierDays);
            if (!$isSentAlready) {
                $customer_coupon = Mage::getModel('iwd_coupon/customercoupon');
                $customer_coupon->setCustomerEmail($customer['customer_email'])
                    ->setCustomerDob($customer['customer_dob'])
                    ->setCustomerId(null)
                    ->setCustomerFirstname($customer['customer_firstname'])
                    ->setCustomerLastname($customer['customer_lastname'])
                    ->setCustomerGroupId(0);
                $customersCoupon[$id_email] = $customer_coupon;
            }
        }

        return $customersCoupon;
    }

    protected function getBirthdayCouponRule()
    {
        $id = $this->getRuleId();
        if ($id == "none")
            return null;
        return Mage::getModel('salesrule/rule')->load($id);
    }


    protected function getBirthdaySentBeforeDay()
    {
        $before = Mage::getStoreConfig($this->XML_PATH_BIRTHDAY_COUPON_SENT_BEFORE, Mage::app()->getStore());
        return (empty($before) || !is_int((int)$before)) ? 0 : (int)$before;
    }

    protected function getLikeDate()
    {
        $before = $this->getBirthdaySentBeforeDay();

        if ($before >= 0) {
            $date = strtotime("+" . $before . " day");
        } elseif ($before < 0) {
            $before *= -1;
            $date = strtotime("-" . $before . " day");
        }
        $month = date("m", $date);
        $day = date("d", $date);

        return "%{$month}-{$day} 00:00:00";
    }

    protected function getCustomerCollection()
    {
        $date = $this->getLikeDate();
        $customerCollection = Mage::getModel("customer/customer")
            ->getCollection()
            ->addFieldToFilter('dob', array('like' => $date))
            ->addNameToSelect()
            ->getItems();
        return $customerCollection;
    }

    protected function getGuestCustomerCollection()
    {
        $date = $this->getLikeDate();
        $orders = Mage::getModel("sales/order")
            ->getCollection()
            ->addFieldToFilter('customer_id', array('null' => true))
            ->addFieldToFilter('customer_dob', array('like' => $date));

        return Mage::helper('iwd_coupon')->getCustomersFromOrders($orders);
    }
}