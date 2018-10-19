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
class Bss_FastOrder_Model_Option extends Mage_Core_Model_Session_Abstract {

	const TYPE_CMS_PAGE = 1;
    const TYPE_POPUP    = 2;

    /**
    * Get possible sharing configuration options
    *
    * @return array
    */
    public function toOptionArray()
    {
        return array(
            self::TYPE_CMS_PAGE  => Mage::helper('fastorder')->__('CMS Page'),
            self::TYPE_POPUP => Mage::helper('fastorder')->__('Popup'),
        );
    }
}