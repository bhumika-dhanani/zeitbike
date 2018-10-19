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

class Ktpl_Catalogprint_Block_Adminhtml_Attribute extends Mage_Adminhtml_Block_System_Config_Form_Field {

    protected  function _prepareLayout() {
        $this->getLayout()->getBlock('head')->addJs('extjs/attribute-sort.js');
        $this->getLayout()->getBlock('head')->addJs('catalog-print/jscolor.js');
        /*$this->getLayout()->getBlock('head')->addJs('jqueryui/minified/jquery.ui.core.min.js');
        $this->getLayout()->getBlock('head')->addJs('jqueryui/minified/jquery.ui.widget.min.js');
        $this->getLayout()->getBlock('head')->addJs('jqueryui/minified/jquery.ui.mouse.min.js');
        $this->getLayout()->getBlock('head')->addJs('jqueryui/minified/jquery.ui.sortable.min.js');*/

    }
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
        $this->setElement($element);
        $this->setCanBeEmpty(true);
		$selected=array();
                $output = '';
		$output .= '<table>';
        $output .= $this->_getHeaderHtml();
		$attributes = Mage::getModel('catalogprint/system_config_source_product_attributes')->toOptionArray();
        $json = Mage::getModel('catalogprint/system_config_source_product_attributes')->toOptionArray1();
		$already_selected = explode(',',$this->getElement()->getData('value'));
		$output .= "<tr><td>";
		$output .= '<select multiple="multiple" size="10" class=" select multiselect" name="container1[]" id="container1" style="min-height:215px;">';
		foreach($attributes as $index=>$attribute) {
			$selected[$attribute['value']]=$attribute['label'];
			if(!in_array($attribute['value'], $already_selected))
			$output .= '<option value="'.$attribute['value'].'">'.$attribute['label'].'</option>';
		}
		$output .= '</select>';
		$output .= '</td><td style="vertical-align:middle;"><table><tr><td><input type="button" id="shift-right" value=">" onclick="addElementRight()" /></td></tr><tr><td><input type="button" id="shift-left" value="<" onclick="addElementLeft()" /></td></tr></table></td>';
		$output .= '<td><select multiple="multiple" size="10" class=" select multiselect" name="container2" id="container2" style="min-height:215px;">';
		foreach($already_selected as $attribute) {
                        if(strlen($attribute) > 0){
                            $output .= '<option value="'.$attribute.'">'.$selected[$attribute].'</option>';
                        }
		}
		$output .= '</select></td>';
		$output .= '<select style="display:none;" multiple="multiple" size="10" class=" select multiselect" name="'.$this->getElement()->getName().'" id="catalogprint_general_product_attributes">';
		foreach($already_selected as $index=>$attribute) {
                        if(strlen($attribute) > 0){
			$output .= '<option value="'.$attribute.'">'.$selected[$attribute].'</option>';
                        }
		}
		$output .= '</select>';
		$output .= '<td style="vertical-align:middle;"><table><tr><td><input type="button" id="shift-up" value="&and;" onclick="addElementUp()" /></td></tr><tr><td><input type="button" id="shift-down" value="&or;" onclick="addElementDown()" /></td></tr></table></td>';
		$output .= '</tr></table>';
                
                $output .= '<style type="text/css">#container1element{min-height: 215px !important;}#row_catalogprint_general_product_attributes .select.multiselect{min-height: 222px !important;}</style>';
	
        return $output;
    }

    protected function _getHeaderHtml() {
        $output = '<tr>';
        $output .= '<th style="padding: 2px; text-align: center;">';
        $output .= Mage::helper('catalogprint')->__('All Attributes');
        $output .= '</th><th>&nbsp;</th>';
        $output .= '<th style="padding: 2px; text-align: center;">';
        $output .= Mage::helper('catalogprint')->__('Included Attributes');
        $output .= '</th>';
        $output .= '</tr>';
        return $output;
    }
    protected function _getPlainRowHtml($index = 0, $json) {
        $output = "<li id='".$this->getElement()->getData('value/' . $index)."' class='ui-state-highlight'>".$json[$this->getElement()->getData('value/' . $index)]."</li>";
        return $output;
    }
    protected function _getRowHtmlWithInputs($index = 0) {
        $output = '<input type="hidden" class="required-entry input-text" style="margin-right:10px" name="' . $this->getElement()->getName() . '[]" value="' . $this->getElement()->getData('value/' . $index) . '" />';
        return $output;
    }
}
