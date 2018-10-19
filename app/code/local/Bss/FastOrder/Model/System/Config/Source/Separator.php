<?php
/**
* BssCommerce Co.
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
* BssCommerce does not guarantee correct work of this extension
* on any other Magento edition except Magento COMMUNITY edition.
* BssCommerce does not provide extension support in case of
* incorrect edition usage.
* =================================================================
*
* @category   BSS
* @package    Bss_ConfigurableProductTabTable
* @author     Extension Team
* @copyright  Copyright (c) 2014-2016 BssCommerce Co. (http://bsscommerce.com)
* @license    http://bsscommerce.com/Bss-Commerce-License.txt
*/
class Bss_FastOrder_Model_System_Config_Source_Separator {
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {
        return array(
            array('value' => '.', 'label'=>Mage::helper('fastorder')->__('.')),
            array('value' => ',', 'label'=>Mage::helper('fastorder')->__(',')),
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            '.' => Mage::helper('fastorder')->__('.'),
            ',' => Mage::helper('fastorder')->__(','),
        );
    }
}