<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Stockstatus
 */


/**
 * Class Amasty_Stockstatus_Model_Type
 *
 * Save extension for load option
 */
class Amasty_Stockstatus_Model_Type extends Mage_Core_Model_Abstract
{
    const DEFAULT_TYPE = '.jpg';

    protected function _construct()
    {
        $this->_init('amstockstatus/type');
    }

    public function getExtenshion()
    {
        $type = $this->getData('type');
        return ($type) ? $type : self::DEFAULT_TYPE;
    }
}
