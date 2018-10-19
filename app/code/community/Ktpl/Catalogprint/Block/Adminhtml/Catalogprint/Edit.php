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
class Ktpl_Catalogprint_Block_Adminhtml_Catalogprint_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->_addButton('print', array(
            'label'     => Mage::helper('catalogprint')->__('Print'),
            'onclick'   => "$('edit_form').submit();",
        ));
        $this->_addButton('previous', array(
            'label'     => Mage::helper('catalogprint')->__('Back'),
            'onclick'   => 'setLocation(\'' . $this->getUrl('adminhtml/catalog_product/index') .'\')',
            'class'     => 'back',
        ));

        $this->removeButton('reset')
            ->removeButton('back')
            ->removeButton('save');
    }

    /**
     * Internal constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_objectId   = 'id';
        $this->_blockGroup = 'catalogprint';
        $this->_controller = 'adminhtml_catalogprint';
    }

    /**
     * Get header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('catalogprint')->__('Catalog Print');
    }
}
