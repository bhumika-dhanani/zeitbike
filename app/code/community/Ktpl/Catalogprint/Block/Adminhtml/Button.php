<?php
/**
 * Magedelight
 * Copyright (C) 2014  Magedelight <info@magedelight.com>
 *
 * NOTICE OF LICENSE
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://opensource.org/licenses/gpl-3.0.html.
 *
 * @category   Ktpl
 * @package    Ktpl_Catalogprint
 * @copyright  Copyright (c) 2014 Mage Delight (http://www.magedelight.com/)
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 * @author     Magedelight <info@magedelight.com>
 */
class Ktpl_Catalogprint_Block_Adminhtml_Button extends Mage_Adminhtml_Block_System_Config_Form_Field 
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
        $this->setElement($element);
        $output = '<div style="float: left; display: inline-block; width: 100%;"><div style="float:left;">'.$this->getCategoriesIndexButtonHtml().'</div><div style="float:right;">'.$this->getQrCodeIndexButtonHtml().'</div><p class="note" style="clear:both;"><span>'.Mage::helper('catalogprint')->__('For the best performance of PDF Catalog Print, Keep Updated Cache and QR Code Index.').'</span></p></div>';
        return $output;
    }
    
    public function getCategoriesIndexButtonHtml()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
            'label'     => $this->helper('adminhtml')->__('Cache Catalog Data'),
            'onclick'   => "setLocation('".$this->getCategoryIndexUrl()."')",
                'class'=>'scalable save'
        ));
 
        return $button->toHtml();
    }
    
    public function getQrCodeIndexButtonHtml(){
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
            'label'     => $this->helper('adminhtml')->__('Index QR Codes'),
            'onclick'   => "setLocation('".$this->getQrCodesIndexUrl()."')",
                'class'=>'scalable save'
        ));
 
        return $button->toHtml();
    }
    
    protected function getCategoryIndexUrl()
    {
        return Mage::helper('adminhtml')->getUrl('catalogprint/adminhtml_index/indexCategory');
    }
    
    protected function getQrCodesIndexUrl()
    {
        return Mage::helper('adminhtml')->getUrl('catalogprint/adminhtml_index/indexQrCodes');
    }
}

