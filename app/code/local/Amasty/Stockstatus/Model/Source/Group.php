<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Stockstatus
 */


class Amasty_Stockstatus_Model_Source_Group
{
    protected $_options;

    const ALL_GROUPS = -1;

    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = Mage::getResourceModel('customer/group_collection')
                ->loadData()->toOptionArray();

            array_unshift(
                $this->_options,
                array(
                    'value'=> self::ALL_GROUPS,
                    'label'=> Mage::helper('amstockstatus')->__('ALL GROUPS')
                )
            );
        }
        return $this->_options;
    }
}