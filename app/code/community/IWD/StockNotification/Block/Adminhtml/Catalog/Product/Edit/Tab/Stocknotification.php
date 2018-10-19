<?php
class IWD_StockNotification_Block_Adminhtml_Catalog_Product_Edit_Tab_Stocknotification extends Mage_Adminhtml_Block_Widget 
				implements Mage_Adminhtml_Block_Widget_Tab_Interface 
{
	
	public function _construct()
	{
		parent::_construct();
		 
		
	}

	public function isHidden() 
	{
		return false;
	}
	
	public function getTabTitle()
	{
		return Mage::helper('downloadable')->__('Stock Notification');
	}
	
	public function canShowTab()
	{
		return true;
	}
	
	public function getTabLabel()
	{
		return Mage::helper('downloadable')->__('Stock Notification');
	}
	
	public function getCurrentUrl($params = array())
	{
		if (!isset($params['_current'])) {
			$params['_current'] = true;
		}
		return $this->getUrl('*/*/*', $params);
	}
	
	
	public function getTabUrl()
	{
		$id = Mage::app()->getRequest()->getParam('id');
		$params = array('id'=>$id);
		return $this->getUrl('adminhtml/stocknotification_product/list', $params);;
	}
	
	public function getTabClass()
	{
		return 'ajax notloaded';
	}
}