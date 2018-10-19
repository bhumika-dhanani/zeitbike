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

class Ktpl_Catalogprint_Model_System_Config_Source_Product_Attributes
{
    public function toOptionArray()
    {
        $options = array();
        $eavTable = Mage::getSingleton('core/resource')->getTableName('catalog/eav_attribute');
        $entityTypeId = Mage::getModel('eav/entity_type')->loadByCode('catalog_product')->getEntityTypeId();
	$nameAttribute = 'name';
        $attributes = Mage::getModel('eav/entity_attribute')->getCollection()->addFieldToFilter('attribute_code',array('neq'=>$nameAttribute))->addFilter('entity_type_id', $entityTypeId)->setOrder('attribute_code', 'ASC');
        $attributes->getSelect()->join(
            array(
                'table_alias'   =>  $eavTable
            ),
            'main_table.attribute_id = table_alias.attribute_id',
            array('table_alias.*')
        );
        
        foreach ($attributes as $attribute) {
            if($attribute->getUseInCatalogprint()) {
                $item = array();
                $item['value'] = $attribute->getAttributeCode();
                if ($attribute->getFrontendLabel()) {
                    $item['label'] = $attribute->getFrontendLabel();
                } else {
                    $item['label'] = $attribute->getAttributeCode();
                }
                $options[] = $item;
            }
        }
        return $options;
    }

    public function toOptionArray1($storeId = 0)
    {
        $options = array();
        $entityTypeId = Mage::getModel('eav/entity_type')->loadByCode('catalog_product')->getEntityTypeId();
 $nameAttribute = 'name';
        $attributes = Mage::getModel('eav/entity_attribute')->getCollection()->addStoreLabel($storeId)->addFieldToFilter('attribute_code',array('neq'=>$nameAttribute))->addFilter('entity_type_id', $entityTypeId)->setOrder('attribute_code', 'ASC');
        foreach ($attributes as $attribute) {
            if ($attribute->getFrontendLabel()) {
                $options[$attribute->getAttributeCode()] = ($attribute->getStoreLabel()) ? $attribute->getStoreLabel() : $attribute->getFrontendLabel();
            } else {
                $options[$attribute->getAttributeCode()] = $attribute->getAttributeCode();
            }
        }
        return $options;
    }
}
