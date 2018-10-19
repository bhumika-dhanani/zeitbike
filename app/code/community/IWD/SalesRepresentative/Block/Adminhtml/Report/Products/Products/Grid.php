<?php
class IWD_SalesRepresentative_Block_Adminhtml_Report_Products_Products_Grid extends Mage_Adminhtml_Block_Report_Grid_Abstract{
	
    protected $_columnGroupBy = 'created_at';

    public function __construct(){
        parent::__construct();
        $this->setCountTotals(true);
    }

    public function getResourceCollectionName(){
        return 'salesrep/report_product_collection';
    }
    
  
    
 protected function _prepareColumns()
    {
 
        
        
       
       $this->addColumn('created_at', array(
               'header'            => Mage::helper('salesrep')->__('Created At'),
               'index'             => 'created_at',
               'sortable'      => false,
               'total'     	=> false,
               'width'             => 1,
               'type'              => 'datetime',
             
               'html_decorators'   => array('nobr'),
             //  'format'		=> Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM),
               
               'totals_label'  => Mage::helper('adminhtml')->__('Total'),
        ));
       
 
        $currencyCode = $this->getCurrentCurrencyCode();
 
        $this->addColumn('name', array(
        		'header'    => Mage::helper('salesrep')->__('Product'),
        		'currency_code' => $currencyCode,
        		'index'     =>'name',
        		'totals'    => false,
        		'totals_label'  => Mage::helper('adminhtml')->__(''),
        		'sortable'  => false,
        		'width'		=>'350px'
        ));
        
        $this->addColumn('base_price', array(
            'header'    => Mage::helper('salesrep')->__('Price'),
            'currency_code' => $currencyCode,
            'index'     =>'base_price',
            'type'      => 'currency',
            'totals'     => 'sum',
            'sortable'  => false,
        	'totals_label'  => Mage::helper('adminhtml')->__(''),
        ));
        
        $this->addColumn('qty_invoiced', array(
        		'header'    => Mage::helper('salesrep')->__('Qty'),
        		'index'     =>'qty_invoiced',
        		'totals'     => 'sum',
        		'sortable'  => false,
        		'width'		=> "70px"        		
        ));
        $this->addColumn('base_row_invoiced', array(
        		'header'    => Mage::helper('salesrep')->__('Total Invoiced'),
        		'currency_code' => $currencyCode,
        		'index'     =>'base_row_invoiced',
        		'type'      => 'currency',
        		'totals'     => 'sum',
        		'sortable'  => false
        ));
        $this->addColumn('amount_refunded', array(
        		'header'    => Mage::helper('salesrep')->__('Amount Refunded'),
        		'currency_code' => $currencyCode,
        		'index'     =>'amount_refunded',
        		'type'      => 'currency',
        		'totals'     => 'sum',
        		'sortable'  => false
        ));
        
       
        
        $this->addColumn('earned', array(
        		'header'    => Mage::helper('salesrep')->__('Earned'),
        		'currency_code' => $currencyCode,
        		'index'     =>'earned',
        		'sortable'  => false,
        		'type'      => 'currency',      
        		
        ));
        
        $this->addColumn('rate', array(
        		'header'    => Mage::helper('salesrep')->__('Rate'),
        		'currency_code' => false,
        		'sortable'  => false,
        		'totals'    => false,
        		'totals_label'  => Mage::helper('adminhtml')->__(''),
        		'renderer'      => 'salesrep/adminhtml_report_products_products_column_rate',
        ));
   
        $this->addExportType('*/*/exportSalesCsv', Mage::helper('adminhtml')->__('CSV'));
        $this->addExportType('*/*/exportSalesExcel', Mage::helper('adminhtml')->__('Excel XML'));
 
        return parent::_prepareColumns();
    }
}
