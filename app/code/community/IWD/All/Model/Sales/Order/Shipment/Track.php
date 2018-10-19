<?php
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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright  Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sales order shippment API
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class IWD_All_Model_Sales_Order_Shipment_Track extends Mage_Sales_Model_Order_Shipment_Track
{


    public function fillEmptyTitle()
    {
        $carrierCode = $this->getCarrierCode();
        if(!$this->getTitle() && !empty($carrierCode)){
            $this->setTitle($this->_getCarrierTitle($this->getShipment(),$carrierCode));
        }
        return $this;
    }

    /**
     * Retrieve shipping carriers for specified order
     *
     * @param Mage_Eav_Model_Entity_Abstract $object
     * @return array
     */
    protected function _getCarrierTitle($object,$carrierCode = '')
    {
        $title = 'Invalid carrier specified.';
        $carrierInstances = Mage::getSingleton('shipping/config')->getCarrierInstance(
            $carrierCode,
            $object->getStoreId()
        );
        if ($carrierInstances && $carrierInstances->isTrackingAvailable()) {
            $title =  $carrierInstances->getConfigData('title');
        }
        return $title;
    }

} // Class Mage_Sales_Model_Order_Shipment_Api End
