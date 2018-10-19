<?php
class IWD_BirthdayCoupon_Helper_Cron extends Mage_Core_Helper_Abstract
{
    const XML_PATH_IWD_CRON_LAST = 'iwd_cron/birthday_coupon/last';
    protected $noticeMessage = "IWD Automatic Coupon Emailer: we have a suspicion that your cron doesn't work correct!";
    protected $errorWhenMoreHours = 25;

    public function cronRun()
    {
        $config = new Mage_Core_Model_Config();
        $time = time();
        $config->saveConfig(self::XML_PATH_IWD_CRON_LAST, $time);
    }

    public function cronCheck()
    {
        $lastRun = Mage::getStoreConfig(self::XML_PATH_IWD_CRON_LAST);

        if (empty($lastRun)) {
            $this->cronRun();
        } else {
            $passed = time() - $lastRun;

            if ($passed > $this->errorWhenMoreHours * 3600) {
                $message = $this->__($this->noticeMessage);
                Mage::getSingleton('adminhtml/session')->addNotice($message);
            }
        }
    }
}