<?php
class IWD_SalesRepresentative_Block_Adminhtml_Users_List extends Mage_Adminhtml_Block_Widget_Grid_Container {
	
	public function __construct() {
		$this->_controller = 'adminhtml_users_list';
		$this->_blockGroup = 'salesrep';
		$this->_headerText = Mage::helper ( 'salesrep' )->__ ( '<i class="fa fa-usd"></i>Sales Representative' );
		parent::__construct ();
		$this->removeButton('add');
	}
	
}