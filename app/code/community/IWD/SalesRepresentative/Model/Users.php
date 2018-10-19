<?php
class IWD_SalesRepresentative_Model_Users extends Mage_Core_Model_Abstract{
	
	/**
	 * Store's Statuses
	 */
	const PERSENT = 1;
	const FIXED = 2;
	
	protected function _construct(){

		$this->_init('salesrep/users');
	}
	
	
	public function getAvailableTypes(){

		$types = new Varien_Object ( array (
				self::PERSENT => Mage::helper ( 'salesrep' )->__ ( 'Percent' ),
				self::FIXED => Mage::helper ( 'salesrep' )->__ ( 'Fee per sale' )
		) );
		
		return $types->getData ();
	}
	
	
	public function getAvailablePeriods(){

		$periods = new Varien_Object ( array (
				'0' => '',
				'1' => Mage::helper ( 'salesrep' )->__ ( 'Daily' ),
				'2' => Mage::helper ( 'salesrep' )->__ ( 'Weekly' ),
				'3' => Mage::helper ( 'salesrep' )->__ ( 'Monthly' ),
		)); 
		
		return $periods->getData ();
	}
	
	
	public function getAvailableLimitTypes(){

		$periods = new Varien_Object ( array (				
				array('value'=>'','label'=>Mage::helper ( 'salesrep' )->__ ( 'None' )),
				array('value'=>'orders','label'=>Mage::helper ( 'salesrep' )->__ ( 'Orders' )),
				array('value'=>'customers','label'=>Mage::helper ( 'salesrep' )->__ ( 'Customers' )),
				array('value'=>'invoices','label'=>Mage::helper ( 'salesrep' )->__ ( 'Invoices' )),
//				array('value'=>'shipments','label'=>Mage::helper ( 'salesrep' )->__ ( 'Shipments' )),
				//array('value'=>'credit','label'=>Mage::helper ( 'salesrep' )->__ ( 'Credit Memos' )),
				//array('value'=>'transactions','label'=>Mage::helper ( 'salesrep' )->__ ( 'Transactions' )),
			
		) );
		
		return $periods->getData ();
	}
}