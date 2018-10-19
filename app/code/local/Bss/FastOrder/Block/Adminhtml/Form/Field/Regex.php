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
class Bss_FastOrder_Block_Adminhtml_Form_Field_Regex extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    protected $_itemRenderer;
 
    public function _prepareToRender()
    {
        $this->addColumn('customer_group', array(
            'label' => Mage::helper('adminhtml')->__('Customer Groups'),
            'renderer' => $this->_getRenderer(),
            'style' => 'width:230px',
        ));
        $this->addColumn('product_sku', array(
            'label' => Mage::helper('adminhtml')->__('Product Sku'),
            'style' => 'width:230px',
        ));
 
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add');
    }
 
    protected function  _getRenderer() 
    {
        if (!$this->_itemRenderer) {
            $this->_itemRenderer = $this->getLayout()->createBlock(
                'fastorder/adminhtml_form_field_customergroup', '',
                array('is_render_to_js_template' => true)
            );
        }
        return $this->_itemRenderer;
    }
 
    protected function _prepareArrayRow(Varien_Object $row)
    {
        $row->setData(
            'option_extra_attr_' . $this->_getRenderer()
                ->calcOptionHash($row->getData('customer_group')),
            'selected="selected"'
        );
    }
}
