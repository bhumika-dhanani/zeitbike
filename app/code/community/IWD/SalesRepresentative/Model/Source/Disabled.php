<?php
class IWD_SalesRepresentative_Model_Source_Disabled extends Mage_Core_Model_Abstract{
	


	public function getAllOptions() {
		$_collection = Mage::getModel('admin/user')->getCollection();
	
		$_options = array();		
		$options[] = array('label' => 'No', 'value'=>'0');
		$options[] = array('label' => 'Once Assigned to Order', 'value'=>'1');
		$options[] = array('label' => 'Once Order Status is', 'value'=>'2');
		
	
	
		return $options;
	}
	
	public function toOptionArray() {
		$_collection = Mage::getModel('admin/user')->getCollection();
	
		$_options = array();
	
		$options[] = array('label' => 'No', 'value'=>'0');
		$options[] = array('label' => 'Once Assigned to Order', 'value'=>'1');
		$options[] = array('label' => 'Once Order Status is', 'value'=>'2');
	
	
		return $options;
	}
}