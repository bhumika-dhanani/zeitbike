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
class Ktpl_Catalogprint_Block_Adminhtml_Catalogprint_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare form before rendering HTML.
     *
     * @return Mage_ImportExport_Block_Adminhtml_Export_Edit_Form
     */
    protected function _prepareForm()
    {
        $helper = Mage::helper('catalogprint');
        $form = new Varien_Data_Form(array(
            'id'     => 'edit_form',
            'action' => $this->getUrl('catalogprint/adminhtml_index/index'),
            'method' => 'post'
        ));
        $fieldset = $form->addFieldset('base_fieldset', array('legend' => $helper->__('Catalog Print Options')));

        $fieldset->addField('layout', 'select', array(
            'name'     => 'layout',
            'title'    => $helper->__('List mode'),
            'label'    => $helper->__('List mode'),
            'required' => false,
            'values'   => array(
                array(
                    'value'     => 'list',
                    'label'     => Mage::helper('catalogprint')->__('List'),
                ),
                array(
                    'value'     => 'grid',
                    'label'     => Mage::helper('catalogprint')->__('Grid'),
                ),
            ),
        ));
        $fieldset->addField('heading', 'checkbox', array(
            'label'     => $helper->__('Product Categories/Sub Categories Headings'),
            'value'  => 1,
            'checked'  => true,
            'name'      => 'heading',
        ));
        $fieldset->addField('cover', 'checkbox', array(
            'label'     => $helper->__('Title/cover page'),
            'value'  => 1,
            'checked'  => true,
            'name'      => 'cover',
        ));
        $fieldset->addField('contents', 'checkbox', array(
            'label'     => $helper->__('Table of Contents with page numbers '),
            'value'  => 1,
            'checked'  => true,
            'name'      => 'contents',
        ));
        $fieldset->addField('mfg_asc', 'checkbox', array(
            'label'     => $helper->__('Document index by Product SKU in ascending order.'),
            'value'  => 1,
            'checked'  => true,
            'name'      => 'mfg_asc',
        ));
        $fieldset->addField('part_asc', 'checkbox', array(
            'label'     => $helper->__('Document index by Product Name in alphabetical sorted ascending order.'),
            'value'  => 1,
            'checked'  => true,
            'name'      => 'part_asc',
        ));
        $categoryId=Mage::app()->getRequest()->getParam('category');
        $fieldset->addField('category', 'hidden', array(
            'value'  => $categoryId,
            'name' => 'category',
            ));
//        $productsArr=Mage::app()->getRequest()->getParam('product');
//        for($i=0;$i<count($productsArr);$i++){
//            $fieldset->addField('product['.$i.']', 'hidden', array(
//            'value'  => $productsArr[$i],
//            'name' => 'product['.$i.']',
//            ));
//        }
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
