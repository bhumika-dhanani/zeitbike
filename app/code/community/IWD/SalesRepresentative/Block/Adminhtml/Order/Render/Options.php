<?php
class IWD_SalesRepresentative_Block_Adminhtml_Order_Render_Options extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
	
	protected static $_statuses;
	
	protected $_options = false;
	

	public function render(Varien_Object $row) {		
	
		return Mage::helper ( 'salesrep' )->__ ( $this->getUsername ( $row ) );
	}
	
	public static function getUsername($row) {

		$name = $row->getUsername();
		$name = trim($name);
		if (empty($name)){
			return 'N/A';
		}
		
		$id = $row->getIwdUserId();
		
		if (! empty ( $id )) {
			
			$user = Mage::getModel('salesrep/users')->getCollection()->addFieldToFilter('iwd_user_id', array('eq'=>$id))->getFirstItem();
			$color = $user->getIwdColor();
			
			if ($user->getId() && !empty($color)){
				
				return '<span class="salesrep-notice" style="background-color:#' . $color . '"><span>' . $name. '</span></span>';
			}else{
				return '<span class="grid-severity-notice"><span>' . $name. '</span></span>';
			}
			
			
			
		} else {
			return 'N/A';
		}
	}
}