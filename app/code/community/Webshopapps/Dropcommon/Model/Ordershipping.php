<?php

 /**
 * WebShopApps Shipping Module
 *
 * @category    WebShopApps
 * @package     WebShopApps_dropship2
 * User         Joshua Stewart
 * Date         30/05/2014
 * Time         14:48
 * @copyright   Copyright (c) 2014 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2014, Zowta, LLC - US license
 * @license     http://www.WebShopApps.com/license/license.txt - Commercial license
 *
 */
class Webshopapps_Dropcommon_Model_Ordershipping extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();

        $this->_init('dropcommon/ordershipping');
        $this->setIdFieldName('id');
    }
}