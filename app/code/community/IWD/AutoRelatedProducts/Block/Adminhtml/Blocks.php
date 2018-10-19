<?php
class IWD_AutoRelatedProducts_Block_Adminhtml_Blocks extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'iwd_autorelatedproducts';
        $this->_controller = 'adminhtml_blocks';
        $this->_headerText = Mage::helper('iwd_autorelatedproducts')->__('Auto Related Products');

        parent::__construct();
    }
}