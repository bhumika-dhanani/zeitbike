<?php
class IWD_AutoRelatedProducts_Block_Adminhtml_Blocks_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'video_id';
        $this->_blockGroup = 'iwd_autorelatedproducts';
        $this->_controller = 'adminhtml_blocks';

        if (Mage::registry('iwd_related_products_data') || Mage::app()->getRequest()->getParam("block_type")) {
            $this->_updateButton('save', 'label', Mage::helper('iwd_autorelatedproducts')->__('Save Block'));
            $this->_addButton('saveandcontinue', array(
                'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
                'onclick' => "editForm.submit($('edit_form').action+'back/edit/')",
                'class' => 'save',
            ), -100, 100);
        } else {
            $this->_removeButton('save');
        }

    }

    public function getHeaderText()
    {
        if (Mage::registry('iwd_related_products_data') && Mage::registry('iwd_related_products_data')->getId()) {
            $title = Mage::registry('iwd_related_products_data')->getTitle();
            $title = !empty($title) ? $title : "ID:".Mage::registry('iwd_related_products_data')->getId();
            return Mage::helper('iwd_autorelatedproducts')->__("Edit Block '%s'", $title);
        }

        return Mage::helper('iwd_autorelatedproducts')->__('Add New Block');
    }
}