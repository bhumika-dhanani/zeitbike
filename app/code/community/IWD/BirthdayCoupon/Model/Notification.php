<?php
class IWD_BirthdayCoupon_Model_Notification extends Mage_Core_Model_Abstract
{
    public function sendTransactionalEmail($customer_coupon, $templateId, $sender = null)
    {
        try {
            if ($sender === null)
            {
                $senderName = Mage::getStoreConfig('trans_email/ident_sales/name');
                $senderEmail = Mage::getStoreConfig('trans_email/ident_sales/email');

                $sender = array(
                    'name' => $senderName,
                    'email' => $senderEmail
                );
            }

            $recipientEmail = $customer_coupon->getCustomerEmail();

            $recipientName = $customer_coupon->getCustomerFirstname() . " " . $customer_coupon->getCustomerLastname();
            $storeId = Mage::app()->getStore()->getId();
            $dateExpire = $customer_coupon->getDateExpire();
            $vars = array(
                'item' => $customer_coupon,
                'expiration_date' => $dateExpire,
                'sender'=>$sender
            );
            $translate = Mage::getSingleton('core/translate');

            Mage::getModel('core/email_template')->sendTransactional(
                $templateId,
                $sender,
                $recipientEmail,
                $recipientName,
                $vars,
                $storeId
            );

            $translate->setTranslateInline(true);
        } catch (Exception $e) {
            Mage::log(__CLASS__.": ".$e->getMessage());
            return false;
        }

        return true;
    }
}