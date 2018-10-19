<?php
class IWD_FeaturedProducts_Block_Adminhtml_List extends Mage_Adminhtml_Block_Widget_Grid_Container {
	public function __construct() {
		$this->_controller = 'adminhtml_list';
		$this->_blockGroup = 'featuredproduct';
		$this->_headerText = Mage::helper ( 'iwdfeaturedproduct' )->__ ( 'Products' );
		parent::__construct ();
		
		$this->_headerText = Mage::helper ( 'adminhtml' )->__ ( 'Featured products' );
		
		parent::__construct ();
		
		$this->_removeButton ( 'add' );
		
		$this->_addButton ( 'save', array ('label' => Mage::helper ( 'iwdfeaturedproduct' )->__ ( 'Save' ), 'onclick' => 'categorySubmit(\'' . $this->getSaveUrl () . '\')', 'class' => 'Save' ) );
	}
	
	protected function _afterToHtml($html) {
		return $this->_prependHtml () . parent::_afterToHtml ( $html );
	}
	private function _prependHtml() {
		$html = '
		<form id="featured_edit_form" action="' . $this->getSaveUrl () . '" method="post" enctype="multipart/form-data">
		<input name="form_key" type="hidden" value="' . $this->getFormKey () . '" />
		<div class="no-display">
		<input type="hidden" name="featured_products" id="in_featured_products" value="" />
		</div>
		</form>';
		
		return $html;
	}
	
	public function getSaveUrl() {
		return $this->getUrl ( '*/*/save', array ('store' => $this->getRequest ()->getParam ( 'store' ) ) );
	}
	protected function _prepareLayout() {
		$this->setChild ( 'store_switcher', $this->getLayout ()->createBlock ( 'adminhtml/store_switcher', 'store_switcher' )->setUseConfirm ( false ) );
		return parent::_prepareLayout ();
	}
	
	public function getGridHtml() {
		
		return $this->getChildHtml ( 'store_switcher' ) . $this->getChildHtml ( 'grid' );
	}
}