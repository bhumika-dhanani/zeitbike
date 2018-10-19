<?php
class IWD_SalesRepresentative_Adminhtml_Iwd_Salesrep_Reports_ProductsController extends Mage_Adminhtml_Controller_Action{

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/report/salesrep/product');
    }
	public function _initAction(){
		$this->loadLayout()
		->_addBreadcrumb(Mage::helper('reports')->__('Reports'), Mage::helper('reports')->__('Reports'))
		->_addBreadcrumb(Mage::helper('reports')->__('Sales'), Mage::helper('reports')->__('Sales'));
		return $this;
	}
	
	
	
	public function _initReportAction($blocks)
	{
		if (!is_array($blocks)) {
			$blocks = array($blocks);
		}
	
		$requestData = Mage::helper('adminhtml')->prepareFilterString($this->getRequest()->getParam('filter'));
	
		$requestData = $this->_filterDates($requestData, array('from', 'to'));
		$requestData['store_ids'] = $this->getRequest()->getParam('store_ids');
		
		$params = new Varien_Object();
	
		foreach ($requestData as $key => $value) {
			if (!empty($value)) {
				$params->setData($key, $value);
			}
		}
	
		foreach ($blocks as $block) {
			if ($block) {
				$block->setPeriodType($params->getData('period_type'));
				$block->setFilterData($params);
			}
		}
	
		return $this;
	}
	
 	public function indexAction(){

        $this->_title($this->__('Reports'))->_title($this->__('Sales Representative'))->_title($this->__('Orders'));

        $this->_initAction()
            ->_setActiveMenu('report/sales/sales')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Sales Report'), Mage::helper('adminhtml')->__('Sales Report'));
        
        $gridBlock = $this->getLayout()->getBlock('adminhtml_report_products_products.grid');
      
        
        
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');
        
        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));
        
        $this->renderLayout();
        
    }
    
    
    public function exportSalesCsvAction(){
    	$fileName   = 'sales.csv';
    	$grid       = $this->getLayout()->createBlock('salesrep/adminhtml_report_products_products_grid');
    	$this->_initReportAction($grid);
    	$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }
    
    public function exportSalesExcelAction()
    {
    	$fileName   = 'sales.xml';
    	$grid       = $this->getLayout()->createBlock('salesrep/adminhtml_report_products_products_grid');
    	$this->_initReportAction($grid);
    	$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
	
}