<?php
class IWD_NewsPopup_Block_Popup extends Mage_Core_Block_Template
{

	public function _construct(){
		$_helper = Mage::helper('iwdpopup');
		if ($_helper->isAvailableVersion()){
			$this->setTemplate("newsletter_popup/overlay.phtml");
		}
		else
		{
			$this->setTemplate(null);
			
		}
	}
	
}