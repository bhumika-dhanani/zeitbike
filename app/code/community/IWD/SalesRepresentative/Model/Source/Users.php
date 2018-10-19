<?php
class IWD_SalesRepresentative_Model_Source_Users extends Mage_Eav_Model_Entity_Attribute_Source_Abstract{
	
	
	/* (non-PHPdoc)
	 * @see Mage_Eav_Model_Entity_Attribute_Source_Interface::getAllOptions()
	 */
	public function getAllOptions() {
		$_collection = Mage::getModel('admin/user')->getCollection();
		
		$_options = array();
		
		
		foreach($_collection as $user){
			$options[] = array('label'=>$user->getName(), 'value'=>$user->getId());
		}
		
		return $options;
	}
	
	public function toOptionArray() {
		$_collection = Mage::getModel('admin/user')->getCollection();
	
		$_options = array();
		$options[] = array('value'=>'','label'=>Mage::helper ( 'salesrep' )->__ ( 'None' ));
		foreach($_collection as $user){
			$options[] = array('label'=>$user->getName(), 'value'=>$user->getId());
		}
	
	
		return $options;
	}

	
}