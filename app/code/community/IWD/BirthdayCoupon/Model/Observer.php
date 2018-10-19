<?php
class IWD_BirthdayCoupon_Model_Observer
{
    /*
     * Check install IWD_ALL
     * Check last running cron
     **/
    public function checkRequiredModules($observer)
    {
        if (Mage::getSingleton('admin/session')->isLoggedIn()) {
            $cache = Mage::app()->getCache();

            /* check IWD ALL*/
            if (!Mage::getConfig()->getModuleConfig('IWD_All')->is('active', 'true')) {
                if ($cache->load("iwd_coupon") === false) {
                    $message = 'Important: Please setup IWD_ALL in order to finish <strong>IWD Automatic Coupon Emailer</strong>  installation.<br />
					Please download <a href="http://iwdextensions.com/media/modules/iwd_all.tgz" target="_blank">IWD_ALL</a> and setup it via Magento Connect.<br />
					Please refer to <a href="https://docs.google.com/document/d/1KfurftCH8rxZZ4PezVSc7YE9NM9PPZ8L_-WHsmi37j8/" target="_blank">installation guide</a>';

                    Mage::getSingleton('adminhtml/session')->addNotice($message);
                    $cache->save('true', 'iwd_coupon', array("iwd_coupon"), $lifeTime = 5);
                }
            }

            /* check cron */
            if ($cache->load("iwd_coupon_cron") === false) {
                if (Mage::getModel("iwd_coupon/coupon_birthday")->isEnabled()) {
                    Mage::helper('iwd_coupon/cron')->cronCheck();
                    $cache->save('true', 'iwd_coupon_cron', array("iwd_coupon_cron"), $lifeTime = 10);
                }
            }
        }
    }

    /*
     * Change newsletter subscribe:
     *      - Newsletter subscription
     **/
    public function newsletterSubscriberChange(Varien_Event_Observer $observer)
    {
        $subscriber = $observer->getEvent()->getSubscriber();

        if ($subscriber->getIsStatusChanged()) {
            if ($subscriber->getStatus() == Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED) {
                Mage::getModel("iwd_coupon/coupon_newslettersubscribe")->sendCoupon($subscriber);
            } elseif ($subscriber->getStatus() == Mage_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED) {
                //TODO: add logic for delete coupon after unsubscribing
                //Mage::getModel("iwd_coupon/coupon_newslettersubscribe")->deleteCoupon($subscriber);
            }
        }
    }

    /*
     * Send coupons by cron:
     *      - Birthday
     **/
    public function sendCoupons()
    {
        try {
            Mage::getModel("iwd_coupon/coupon_birthday")->sendCoupons();
            Mage::helper('iwd_coupon/cron')->cronRun();
        } catch (Exception $e) {
            Mage::log(__CLASS__ . ": " . $e->getMessage());
        }
    }

    /*
     * Order create:
     *      - First Purchase
     *      - Good Customer
    **/
    public function orderPlace(Varien_Event_Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        Mage::getModel("iwd_coupon/coupon_firstpurchase")->sendCoupon($order);
        Mage::getModel("iwd_coupon/coupon_goodcustomer")->sendCoupon($order);
    }


    /* Perversion for magento 1.4-1.5. In this versions absent event 'customer_register_success'*/
    /*
     * Customer save before
    **/
    public function customerSaveBefore(Varien_Event_Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        $id = $customer->getEntityId();

        if (empty($id))
            $customer->setData('iwd_is_new_customer', true);
    }

    /*
     * Customer save after
    **/
    public function customerSaveAfter(Varien_Event_Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        $is_new_customer = $customer->getData('iwd_is_new_customer');
        if (isset($is_new_customer) && $is_new_customer===true) {
            Mage::getModel("iwd_coupon/coupon_newregistration")->sendCoupon($customer);
        }
    }
}