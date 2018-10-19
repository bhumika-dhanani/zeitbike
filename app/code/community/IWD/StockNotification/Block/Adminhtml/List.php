<?php

class IWD_StockNotification_Block_Adminhtml_List extends  Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'adminhtml_list';
        $this->_blockGroup = 'stocknotification';
        $this->_headerText = Mage::helper ( 'stocknotification' )->__ ( 'View Subscribers' );
        parent::__construct ();
        $this->removeButton('add');
    }

} 