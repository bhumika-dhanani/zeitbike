<?php

class IWD_StockNotification_Block_Adminhtml_List_Grid extends Mage_Adminhtml_Block_Widget_Grid
{


    public function __construct()
    {
        parent::__construct ();
        $this->setId ( 'grid' );
        $this->setDefaultSort ( 'id' );
        $this->setDefaultDir ( 'desc' );
        $this->setSaveParametersInSession ( true );
    }



    protected function _prepareCollection()
    {
        $collection = Mage::getModel('stocknotification/notice')->getCollection();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }


    protected function _prepareColumns() 
    {

        $this->addColumn ( 'id',
            array (
                'header' => Mage::helper ( 'stocknotification' )->__ ( 'ID' ),
                'align' => 'center',
                'index' => 'entity_id',
                'width' => '60'
            )
        );

        $this->addColumn ( 'product_name',
            array (
                'header' => Mage::helper ( 'stocknotification' )->__ ( 'Product Name' ),
                'align' => 'left',
                'index' => 'product_id',
                'renderer'  =>  'IWD_StockNotification_Block_Adminhtml_List_Render_Product',
                'filter'=>false
            )
        );

        $this->addColumn ( 'type',
            array (
                'header' => Mage::helper ( 'stocknotification' )->__ ( 'Type' ),
                'align' => 'left',
                'index' => 'parent_id',
                'renderer'  =>  'IWD_StockNotification_Block_Adminhtml_List_Render_Type',
                'filter'=>false
            )
        );

        $this->addColumn ( 'customer',
            array (
                'header' => Mage::helper ( 'stocknotification' )->__ ( 'Customer Name' ),
                'align' => 'left',
                'index' => 'customer_id',
                'renderer'  =>  'IWD_StockNotification_Block_Adminhtml_List_Render_Customer',
                'filter'=>false
            )
        );


        $this->addColumn ( 'email',
            array (
                'header' => Mage::helper ( 'stocknotification' )->__ ( 'Email' ),
                'align' => 'left',
                'index' => 'email'
            )
        );


        $this->addColumn ( 'created',
            array (
                'header' => Mage::helper ( 'stocknotification' )->__ ( 'Created' ),
                'align' => 'left',
                'index' => 'created_at',
                'type' => 'datetime'
            )
        );

        $this->addColumn ( 'is_notified',
            array (
                'header' => Mage::helper ( 'stocknotification' )->__ ( 'Status' ),
                'align' => 'left',
                'index' => 'is_notified',
                'renderer'  =>  'IWD_StockNotification_Block_Adminhtml_List_Render_Status',
                'type'      => 'options',
                'options'   => array(1 => $this->__('Notified'), 0 => $this->__('Waiting')),
            	'frame_callback' => array($this, 'decorateStatus')
            )
        );

        
        $this->addExportType('*/*/exportCsv', Mage::helper('customer')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('customer')->__('Excel XML'));
        
        return parent::_prepareColumns ();
    }
    
    
    public function decorateStatus($value, $row, $column, $isExport)
    {
    	$class = '';
    	
    		if ($row->getIsNotified()) {
    			$cell = '<span class="grid-severity-notice"><span>'.$value.'</span></span>';
    		} else {
    			$cell = '<span class="grid-severity-critical"><span>'.$value.'</span></span>';
    		}
    	
    	return $cell;
    }
}