<?php
class IWD_StockNotification_Adminhtml_Stocknotification_ListController extends Mage_Adminhtml_Controller_Action{


    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('iwdall/stocknotification')
            ->_addBreadcrumb(Mage::helper('stocknotification')->__('Out of stock notification'), Mage::helper('stocknotification')->__('Out of stock notification'))
            ->_addBreadcrumb(Mage::helper('stocknotification')->__('View Subscribers'), Mage::helper('stocknotification')->__('View Subscribers'));
        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('IWD'))
            ->_title($this->__('Stock Notification'))
            ->_title($this->__('View Subscribers'));

        $this->_initAction();
        $this->renderLayout();
    }
    
    protected function _isAllowed()
    {
    	return Mage::getSingleton('admin/session')->isAllowed('system/iwdall/stocknotification');
    }
    
    
    /**
     * Export customer grid to CSV format
     */
    public function exportCsvAction()
    {
    	$fileName   = 'stockrequest.csv';
    	$content    = $this->getLayout()->createBlock('stocknotification/adminhtml_list_grid')
										->getCsvFile();
    
    	$this->_prepareDownloadResponse($fileName, $content);
    }
    
    /**
     * Export customer grid to XML format
     */
    public function exportXmlAction()
    {
    	$fileName   = 'stockrequest.xml';
    	$content    = $this->getLayout()->createBlock('stocknotification/adminhtml_list_grid')
    									->getExcelFile();
    
    	$this->_prepareDownloadResponse($fileName, $content);
    }
    
    /**
     * Prepare file download response
     */
    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
    	$this->_prepareDownloadResponse($fileName, $content, $contentType);
    }
}