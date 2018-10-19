<?php
class IWD_SalesRepresentative_Block_Adminhtml_Users_List_Grid extends Mage_Adminhtml_Block_Widget_Grid{
	
	public function __construct() {
		parent::__construct ();
		$this->setId ( 'grid' );
		$this->setDefaultSort ( 'id' );
		$this->setDefaultDir ( 'desc' );
		$this->setSaveParametersInSession ( true );
	}
	
	protected function _prepareCollection(){
		$userTable =  Mage::getSingleton('core/resource')->getTableName('admin_user');
		
		$collection = Mage::getModel('salesrep/users')->getCollection()->addFieldToFilter('iwd_status', array('eq'=>1));
		$collection->getSelect()
			->joinLeft(array("user_table" => $userTable), "main_table.iwd_user_id = user_table.user_id", array(
						"iwd_firstname" => "user_table.firstname",
						"iwd_lastname" => "user_table.lastname"
			));
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	
	protected function _prepareColumns() {
		$this->addColumn ( 'iwd_entity_id',
				array (
						'header' => Mage::helper ( 'salesrep' )->__ ( 'ID' ),
						'align' => 'left', 
						'index' => 'iwd_entity_id',
						'width' => '60px'
				)
		);
		
		$this->addColumn ( 'iwd_firstname',
				array (
						'header' => Mage::helper ( 'salesrep' )->__ ( 'Firstname' ),
						'align' => 'left', 
						'index' => 'iwd_firstname',
						
				)
		);
		
		$this->addColumn ( 'iwd_lastname',
				array (
						'header' => Mage::helper ( 'salesrep' )->__ ( 'Lastname' ),
						'align' => 'left',
						'index' => 'iwd_lastname',
		
				)
		);
		
		$this->addColumn ( 'iwd_name',
				array (
						'header' => Mage::helper ( 'salesrep' )->__ ( 'Display Name' ),
						'align' => 'left',
						'index' => 'iwd_name',
		
				)
		);
		

		return parent::_prepareColumns ();
	}
	
	/**
	 * Row click url
	 *
	 * @return string
	 */
	public function getRowUrl($row){
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}
	
	protected function _afterLoadCollection(){
		$this->getCollection()->walk('afterLoad');
		parent::_afterLoadCollection();
	}
	
	protected function _filterStoreCondition($collection, $column){
		if (!$value = $column->getFilter()->getValue()) {
			return;
		}
	
		$this->getCollection()->addStoreFilter($value);
	}
}