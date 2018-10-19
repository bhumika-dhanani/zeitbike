<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * BSS Commerce does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BSS Commerce does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   BSS
 * @package    Bss_FastOrder
 * @author     Extension Team
 * @copyright  Copyright (c) 2014-2015 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
class Bss_FastOrder_Model_Source_Customergroup
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
    	$data = array();
    	$groups = Mage::getModel('customer/group')->getCollection();
    	
    	foreach ($groups->getData() as $key => $group) {
    		$data[$key]['value'] = $group['customer_group_id'];
    		$data[$key]['label'] = $group['customer_group_code'];
    	}
        return $data;
    }

}
