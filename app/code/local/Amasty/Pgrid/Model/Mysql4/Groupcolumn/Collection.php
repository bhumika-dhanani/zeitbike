<?php
 /**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Pgrid
 */

class Amasty_Pgrid_Model_Mysql4_Groupcolumn_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('ampgrid/groupcolumn');
    }
}