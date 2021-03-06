<?php


/**
 * @category   Webshopapps
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license)
 * @license    http://www.webshopapps.com/license/license.txt - Commercial license
 */
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Shipping
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Webshopapps_Dropship_Model_Shipping_Carrier_Source_ShipStatus
{
	
	const DROPSHIP_SHIPSTATUS_PENDING = 0;
	const DROPSHIP_SHIPSTATUS_SHIPPED = 1;
	
    public function toOptionArray()
    {
        return array(
            '0' => Mage::helper('dropship')->__('Pending'),
            '1'  => Mage::helper('dropship')->__('Shipped'),
        );
    }
    
    public static function getOption($option) {
    	switch ($option) {
	    	case '0':
	    		return 'Pending';
	    		break;
	    	case '1':
	    		return 'Shipped';
	    		break;
	    	default:
	    		return 'Unknown';
	    		break;
    	}
    }
}