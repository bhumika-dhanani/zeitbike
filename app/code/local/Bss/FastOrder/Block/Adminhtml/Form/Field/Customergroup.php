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
class Bss_FastOrder_Block_Adminhtml_Form_Field_Customergroup
    extends Mage_Core_Block_Html_Select
{
    public function _toHtml()
    {
        $options = Mage::getModel('customer/group')->getCollection();
        
        foreach ($options->getData() as $option) {
            $this->addOption($option['customer_group_id'], $option['customer_group_code']);
        }
 
        return parent::_toHtml();
    }
 
    public function setInputName($value)
    {
        return $this->setName($value);
    }
}