<?php

class IWD_SalesRepresentative_Helper_Data extends Mage_Core_Helper_Abstract
{

    const XML_PATH_EMAIL_AUTOASSIGN = 'salesrep/email/autoassign_template';

    protected $_version = 'EE';


    public function getJsonUrl()
    {
        $formKey = Mage::getSingleton('core/session')->getFormKey();
        return $this->_getUrl('adminhtml/iwd_salesrep_json/users', array('_secure' => true, 'order_id' => Mage::app()->getRequest()->getParam('order_id')));
    }


    public function getSaveUrl()
    {
        $formKey = Mage::getSingleton('core/session')->getFormKey();
        return $this->_getUrl('adminhtml/iwd_salesrep_json/save', array('_secure' => true, 'order_id' => Mage::app()->getRequest()->getParam('order_id')));
    }

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
            $this->updateConfig();
            return false;
        }
        return true;

    }

    private function updateConfig()
    {

        // Disable its output as well (which was already loaded)
        $message = 'IWD Sales Representative module is available for Magento CE only.<br />You are using Enterprise version of Magento.
					<br />Please obtain Enteprise copy of the modul at <a href="www.iwdextensions.com" target="_blank">www.iwdextensions.com</a></span>';

        Mage::getSingleton('adminhtml/session')->addNotice($message);


        $nodePath = "modules/IWD_SalesRepresentative/active";
        Mage::getConfig()->setNode($nodePath, 'false', true);

    }

    public function getGridCellRate($userId, $productId)
    {

        $row = Mage::getModel('catalog/product')->load($productId);
        $collection = Mage::getModel('salesrep/link')->getCollection()
            ->addFieldToFilter('iwd_user_id', array('eq' => $userId))
            ->addFieldToFilter('iwd_linked_product_id', array('eq' => $row->getId()));
        $item = $collection->getFirstItem();

        if (!$item->getId()) {
            // if current item not assigned to user return empty data
            return '';
        } else {
            // if current item assign to user
            $user = Mage::registry('current_user');
            $rateType = $item->getProductRateType();

            if (($rateType != 1 && $rateType != 2) || $user->getGlobal()) {

                //Percent
                if ($user->getRateType() == 1) {
                    return $user->getPercentRate() . '% per item <small class="notice">(GLOBAL)</small><a data-user="' . $user->getId() . '" data-product="' . $row->getId() . '" class="update-product-rate" href=""><i class="fa fa-pencil-square-o"></i> ' . Mage::helper('salesrep')->__('Update') . '</a>';
                }
                //Fixed
                if ($user->getRateType() == 2) {
                    $currency_code = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
                    return $user->getFixedRate() . $currency_code . ' per item <small class="notice">(GLOBAL)</small><a data-user="' . $user->getId() . '" data-product="' . $row->getId() . '" class="update-product-rate" href=""><i class="fa fa-pencil-square-o"></i> ' . Mage::helper('salesrep')->__('Update') . '</a>';
                }

            } else {

                //Percent
                if ($rateType == 1) {
                    return $item->getProductPercentRate() . '% per item <a data-user="' . $user->getId() . '" data-product="' . $row->getId() . '" class="update-product-rate" href=""><i class="fa fa-pencil-square-o"></i> ' . Mage::helper('salesrep')->__('Update') . '</a>';
                }

                //Fixed
                if ($rateType == 2) {
                    $currency_code = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
                    return $item->getProductFixedRate() . $currency_code . ' per item <a data-user="' . $user->getId() . '" data-product="' . $row->getId() . '" class="update-product-rate" href=""><i class="fa fa-pencil-square-o"></i> ' . Mage::helper('salesrep')->__('Update') . '</a>';
                }

            }
        }
    }


    public function sendEmailNotification($userId, $orderId)
    {
        if (!$userId) {
            return;
        }
        $user = Mage::getModel('salesrep/users')->load($userId, 'iwd_user_id');
        $order = Mage::getModel('sales/order')->load($orderId);
        if (!$user->getId()) {
            return;
        }

        if (!$user->getNotify()) {
            return;
        }

        $user = Mage::getModel('admin/user')->load($user->getUserId());


        $storeId = Mage::app()->getStore()->getId();


        // Start store emulation process
        $appEmulation = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId);

        try {
            // Retrieve specified view block from appropriate design package (depends on emulated store)
            $paymentBlock = Mage::helper('payment')->getInfoBlock($order->getPayment())->setIsSecureMode(true);
            $paymentBlock->getMethod()->setStore($storeId);
            $paymentBlockHtml = $paymentBlock->toHtml();
        } catch (Exception $exception) {
            // Stop store emulation process
            $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
            throw $exception;
        }

        // Stop store emulation process
        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        // Retrieve corresponding email template id and customer name
        $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_AUTOASSIGN);
        $customerName = $user->getUsername();


        $mailer = Mage::getModel('core/email_template_mailer');
        $emailInfo = Mage::getModel('core/email_info');
        $emailInfo->addTo($user->getEmail(), $customerName);

        $mailer->addEmailInfo($emailInfo);


        // Set all required params and send emails
        $mailer->setSender('sales');
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId($templateId);
        $mailer->setTemplateParams(array(
                'order' => $order,
                'payment_html' => $paymentBlockHtml
            )
        );
        $mailer->send();

        return $this;
    }

    public function checkByRoles()
    {
        $user = Mage::getSingleton('admin/session')->getUser();
        $skipRoleId = Mage::getStoreConfig('salesrep/order/skip_restrict');

        if (empty($skipRoleId)) {
            return false;
        }

//		$model = Mage::getModel('admin/roles')->load($skipRoleId);
        $roles = $user->getRoles();

        if (in_array($skipRoleId, $roles)) {
            return true;
        }

        return false;
    }


    public function isEdit($state)
    {
        $statusCancelRestrict = $this->checkByRoles();

        $orderId = Mage::app()->getRequest()->getParam('order_id', false);
        if (!$orderId) {
            return true;
        }
        if ($statusCancelRestrict == false) {
            return false;
        }
        if ($state == 'N/A') {
            return true;
        }


        $status = Mage::getStoreConfig('salesrep/order/disable_edit');
        if (!$status) {
            return true;
        }


        if ($status == 1) {
            return $status;
        }

        if ($status == 2) {
            $orderStatusRestrict = Mage::getStoreConfig('salesrep/order/order_status');

            $order = Mage::getModel('sales/order')->load($orderId);
            $orderStatus = $order->getStatus();


            if ($orderStatus == $orderStatusRestrict) {
                return false;
            }

            return true;
        }
    }

}