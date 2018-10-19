<?php
class IWD_SalesRepresentative_Block_Adminhtml_Report_Orders_Orders_Grid extends Mage_Adminhtml_Block_Report_Grid_Abstract{
	
    protected $_columnGroupBy = 'created_at';

    public function __construct(){
        parent::__construct();
        $this->setCountTotals(true);
    }

    public function getResourceCollectionName(){
        return 'salesrep/report_order_collection';
    }


    
 protected function _prepareColumns()
    {
           $this->addColumn('created_at', array(
            'header'        => Mage::helper('salesrep')->__('Period'),
            'index'         => 'created_at',
            'width'         => 100,
            'sortable'      => false,
           	'total'     	=> false,
           	'renderer'      => 'adminhtml/report_sales_grid_column_renderer_date',          
            'totals_label'  => Mage::helper('adminhtml')->__('Total'),
            'html_decorators' => array('nobr'),
           	'width'         => 100,
        ));
 
        $this->addColumn('increment_id', array(
            'header'    =>Mage::helper('salesrep')->__('Order #'),
            'index'     =>'increment_id',
            'sortable'  => false,
        	'totals'     => false,
        	'type'      => 'number',
        	'align'=>'center',
        	'totals_label'  => Mage::helper('adminhtml')->__(''),
        ));
 
        $currencyCode = $this->getCurrentCurrencyCode();
 
        $this->addColumn('base_subtotal', array(
            'header'    => Mage::helper('salesrep')->__('Sales Total'),
            'currency_code' => $currencyCode,
            'index'     =>'base_subtotal',
            'type'      => 'currency',
            'totals'     => 'sum',
            'sortable'  => false
        ));

        $this->addColumn('cost', array(
            'header'    => Mage::helper('salesrep')->__('Cost'),
            'currency_code' => $currencyCode,
            'index'     =>'cost',
            'type'      => 'currency',
            'total'     => 'sum',
            'sortable'  => false,
        ));

        $this->addColumn('profit', array(
            'header'    => Mage::helper('salesrep')->__('Profit'),
            'currency_code' => $currencyCode,
            'index'     =>'profit',
            'type'      => 'currency',
            'totals'     => 'sum',
            'sortable'  => false,
        ));

        $this->addColumn('base_total_invoiced', array(
        		'header'    => Mage::helper('salesrep')->__('Invoiced'),
        		'currency_code' => $currencyCode,
        		'index'     =>'base_total_invoiced',
        		'type'      => 'currency',
        		'totals'     => 'sum',
        		'sortable'  => false
        ));
        
        $this->addColumn('base_total_refunded', array(
        		'header'    => Mage::helper('salesrep')->__('Refunded'),
        		'currency_code' => $currencyCode,
        		'index'     =>'base_total_refunded',
        		'type'      => 'currency',
        		'totals'     => 'sum',
        		'sortable'  => false
        ));
        
        $this->addColumn('base_tax_amount', array(
        		'header'    => Mage::helper('salesrep')->__('Sales Tax'),
        		'currency_code' => $currencyCode,
        		'index'     =>'base_tax_amount',
        		'type'      => 'currency',
        		'totals'     => 'sum',
        		'sortable'  => false
        ));
        
        $this->addColumn('base_shipping_amount', array(
        		'header'    => Mage::helper('salesrep')->__('Sales Shipping'),
        		'currency_code' => $currencyCode,
        		'index'     =>'base_shipping_amount',
        		'type'      => 'currency',
        		'totals'     => 'sum',
        		'sortable'  => false
        ));
        
        $this->addColumn('base_discount_amount', array(
        		'header'    => Mage::helper('salesrep')->__('Sales Discount'),
        		'currency_code' => $currencyCode,
        		'index'     =>'base_discount_amount',
        		'type'      => 'currency',
        		'totals'     => 'sum',
        		'sortable'  => false
        ));
        $this->addColumn('base_subtotal_canceled', array(
        		'header'    => Mage::helper('salesrep')->__('Canceled'),
        		'currency_code' => $currencyCode,
        		'index'     =>'base_subtotal_canceled',
        		'type'      => 'currency',
        		'totals'     => 'sum',
        		'sortable'  => false
        ));
        
        $this->addColumn('iwd_username', array(
        		'header'    => Mage::helper('salesrep')->__('Representative'),
        		'currency_code' => false,
        		'index'     =>'iwd_username',
        		'sortable'  => false,
        		'totals'    => false,
        		'totals_label'  => Mage::helper('adminhtml')->__(''),
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
        		'renderer'      => 'salesrep/adminhtml_report_orders_orders_column_rate',
        ));
        
        
        $this->addExportType('*/*/exportSalesCsv', Mage::helper('adminhtml')->__('CSV'));
        $this->addExportType('*/*/exportSalesExcel', Mage::helper('adminhtml')->__('Excel XML'));
 
        return parent::_prepareColumns();
    }
}
