<?php

/**
 * WDCA - Sweet Tooth
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the WDCA SWEET TOOTH POINTS AND REWARDS 
 * License, which extends the Open Software License (OSL 3.0).

 * The Open Software License is available at this URL: 
 * http://opensource.org/licenses/osl-3.0.php
 * 
 * DISCLAIMER
 * 
 * By adding to, editing, or in any way modifying this code, WDCA is 
 * not held liable for any inconsistencies or abnormalities in the 
 * behaviour of this code. 
 * By adding to, editing, or in any way modifying this code, the Licensee
 * terminates any agreement of support offered by WDCA, outlined in the 
 * provided Sweet Tooth License. 
 * Upon discovery of modified code in the process of support, the Licensee 
 * is still held accountable for any and all billable time WDCA spent 
 * during the support process.
 * WDCA does not guarantee compatibility with any other framework extension. 
 * WDCA is not responsbile for any inconsistencies or abnormalities in the
 * behaviour of this code if caused by other framework extension.
 * If you did not receive a copy of the license, please send an email to 
 * support@sweettoothrewards.com or call 1.855.699.9322, so we can send you a copy 
 * immediately.
 * 
 * @category   [TBT]
 * @package    [TBT_Rewards]
 * @copyright  Copyright (c) 2014 Sweet Tooth Inc. (http://www.sweettoothrewards.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test Controller used for testing purposes ONLY!
 *
 * @category   TBT
 * @package    TBT_Rewards
 * * @author     Sweet Tooth Inc. <support@sweettoothrewards.com>
 *
 */
class TBT_Rewards_Manage_Debug_SessionController extends Mage_Adminhtml_Controller_Action {
	
	public function indexAction() {
		die ( "This is the test controller that should be used for test purposes only!" );
	}
	
	public function isAdminModeAction() {
		if ($this->_getSess ()->isAdminMode ()) {
			echo "Is admin";
		} else {
			echo "not admin mode";
		}
	}
	
	public function isCustomerLoggedInAction() {
		if ($this->_getSess ()->isCustomerLoggedIn ()) {
			echo "Customer is logged in";
		} else {
			echo "Customer is not logged in";
		}
	}
	
	/**
	 * gets a product
	 *
	 * @param integer $id
	 * @return TBT_Rewards_Model_Catalog_Product
	 */
	public function _getProduct($id) {
		return Mage::getModel ( 'rewards/catalog_product' )->load ( $id );
	}
	
	/**
	 * Fetches the Jay rewards customer model.
	 *
	 * @return TBT_Rewards_Model_Customer
	 */
	public function _getJay() {
		return Mage::getModel ( 'rewards/customer' )->load ( 1 );
	}
	
	/**
	 * Fetches the rewards session
	 *
	 * @return TBT_Rewards_Model_Session
	 */
	public function _getSess() {
		return Mage::getSingleton ( 'rewards/session' );
	}
	
	/**
	 * Gets the default rewards helper
	 *
	 * @return TBT_Rewards_Helper_Data
	 */
	public function _getHelp() {
		return Mage::helper ( 'rewards' );
	}

}