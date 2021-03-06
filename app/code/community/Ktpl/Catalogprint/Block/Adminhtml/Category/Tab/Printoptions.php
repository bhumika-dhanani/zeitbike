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
class Ktpl_Catalogprint_Block_Adminhtml_Category_Tab_Printoptions extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('catalogprint/category/tab/printoptions.phtml');
    }
    
    protected function _prepareLayout() {
        $this->setChild('catalog.print.button',$this->getLayout()->createBlock('adminhtml/widget_button')
                            ->setData(array(
                                'label'=>Mage::helper('catalogprint')->__('Print Catalog'),
                                'onclick'=>'sendPrintRequest('.Mage::app()->getRequest()->getParam('id').');',
                                'class' =>'save'
                            ))
        );
        return parent::_prepareLayout();
    }
    
    public function getPrintButtonHtml()
    {
        return $this->getChildHtml('catalog.print.button');
    }
}

