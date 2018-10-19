<?php
class IWD_SalesRepresentative_Model_Source_Roles extends Mage_Eav_Model_Entity_Attribute_Source_Abstract{
	
	
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
		$_collection = Mage::getModel('admin/roles')->getCollection();
	
		$_options = array();
		$options[] = array('value'=>'','label'=>'');
		foreach($_collection as $role){
		
			$options[] = array('label'=>$role->getRoleName(), 'value'=>$role->getId());
		}
	
	
		return $options;
	}

	
}