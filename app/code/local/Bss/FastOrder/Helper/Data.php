<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * BSS Commerce does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BSS Commerce does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   BSS
 * @package    Bss_FastOrder
 * @author     Extension Team
 * @copyright  Copyright (c) 2014-2015 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
class Bss_FastOrder_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function isEnable()
    {
        return Mage::getStoreConfig('bss_fastorder/general_settings/enabled');
    }
    public function getCustomerGroup()
    {
        $groupId = 0;
        $login = Mage::getSingleton( 'customer/session' )->isLoggedIn();
        if($login){
            $groupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
        }
        return $groupId;
    }

    public function getConfigGroup()
    {
        return Mage::getStoreConfig('bss_fastorder/general_settings/sku_customer_group');
    }

    public function isEnableShortcut()
    {
        if(Mage::getStoreConfig('bss_fastorder/general_settings/form_popup_style') == 2){
            return true;
        }
        return false;
    }

    public function getCmsLink()
    {
        if (Mage::getStoreConfig('bss_fastorder/general_settings/form_popup_style') == 1 && Mage::getStoreConfig('bss_fastorder/general_settings/cms_page_fastorder')) {
            return Mage::getBaseUrl() . Mage::getStoreConfig('bss_fastorder/general_settings/cms_page_fastorder');
        }
        return false;
    }

    public function getShowLine()
    {
        return Mage::getStoreConfig('bss_fastorder/general_settings/lines');
    }
    public function getCustomerGroupShow()
    {
        $show  = false;
        $groupIds =  Mage::getStoreConfig('bss_fastorder/general_settings/customer_group');
        $groups = explode(',', $groupIds);
        $login = Mage::getSingleton( 'customer/session' )->isLoggedIn();
        if($login){
            $groupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
            if(in_array($groupId,$groups)) $show = true;
        }else {
            if(in_array(0,$groups)) $show = true;
        }
        return $show;
    }
    public function getMaxResults(){
        return Mage::getStoreConfig('bss_fastorder/general_settings/max_results');
    }
    public function getCustomerSession(){
        return Mage::getStoreConfig('bss_fastorder/general_settings/enabled_customer_session');
    }
    public function getConfigurable()
    {
        $template = '';
        if(Mage::app()->getRequest()->getParam('fastorder')){
            $template = 'bss/fastorder/catalog/product/view/type/options/configurable.phtml';
        }else{
            $template = 'catalog/product/view/type/options/configurable.phtml';
        }
        return $template;
    }
    public function checkVersion()
    {
        $checkVersion = false;
        $magentoVersion = Mage::getVersion();
        if (version_compare($magentoVersion, '1.9', '<')){
            $checkVersion = true;
        }
        return $checkVersion;
    }
}
