<?php
class IWD_SalesRepresentative_Model_Source_Order extends Mage_Eav_Model_Entity_Attribute_Source_Abstract{
	
	
	/* (non-PHPdoc)
	 * @see Mage_Eav_Model_Entity_Attribute_Source_Interface::getAllOptions()
	 */
	public function getAllOptions() {
		$_collection = Mage::getModel('sales/order_status')->getCollection();
		
		$_options = array();
		$options[] = array('label'=>'', 'value'=>'');
		
		foreach($_collection as $item){
			$options[] = array('label'=>$item->getLabel(), 'value'=>$item->getId());
		}
		
		
		return $options;
	}
	
	public function toOptionArray() {
		$_collection = Mage::getModel('sales/order_status')->getCollection();
	
		$_options = array();
		$options[] = array('label'=>'', 'value'=>'');
		foreach($_collection as $item){
			$options[] = array('label'=>$item->getLabel(), 'value'=>$item->getId());
		}
	
	
		return $options;
	}

	
}