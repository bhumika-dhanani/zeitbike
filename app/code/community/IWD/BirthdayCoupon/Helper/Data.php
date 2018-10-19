<?php
class IWD_BirthdayCoupon_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getCustomersFromOrders($orders)
    {
        $customers = array();
        foreach ($orders as $order) {
            $customers[$order->getCustomerEmail()] = $this->getCustomerFromOrder($order);
        }

        return $customers;
    }

    public function getCustomerFromOrder($order)
    {
        return array(
            'customer_email' => $order->getCustomerEmail(),
            'customer_firstname' => $order->getCustomerFirstname(),
            'customer_lastname' => $order->getCustomerLastname(),
            'customer_middlename' => $order->getCustomerMiddlename(),
            'customer_prefix' => $order->getCustomerPrefix(),
            'customer_suffix' => $order->getCustomerSuffix(),
            'customer_taxvat' => $order->getCustomerTaxvat(),
            'customer_dob' => $order->getCustomerDob(),
            'customer_group_id' => $order->getCustomerGroupId(),
            'customer_id' => $order->getCustomerId(),
        );
    }

    protected $_version = 'CE';

    public function isAvailableVersion()
    {
        $mage = new Mage();
        if (!is_callable(array($mage, 'getEdition'))) {
            $edition = 'Community';
        } else {
            $edition = Mage::getEdition();
        }
        unset($mage);

        if ($edition == 'Enterprise' && $this->_version == 'CE') {
            return false;
        }
        return true;
    }
}
