<?php

class IWD_StockNotification_Block_Adminhtml_Catalog_Product_Edit_Tab_Stocknotification_Items extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set grid params
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('stocknotification_grid');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
        $this->setFilterVisibility(true);
    }

   


    /**
     * Prepare collection
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        /* @var $collection Mage_Catalog_Model_Resource_Product_Link_Product_Collection */
        $collection = Mage::getModel('stocknotification/notice')->getCollection();
        $collection->addFieldToFilter('product_id', array('eq'=>Mage::registry('current_product')->getId()));

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    

    /**
     * Add columns to grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
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
        
        
        return parent::_prepareColumns();
    }

    /**
     * Rerieve grid URL
     *
     * @return string
     */
  	public function getGridUrl()
    {
    	$id = Mage::app()->getRequest()->getParam('id');
        return $this->_getData('grid_url') ? $this->_getData('grid_url') : $this->getUrl('adminhtml/stocknotification_product/grid', array('_current'=>true,'id'=>$id));
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
