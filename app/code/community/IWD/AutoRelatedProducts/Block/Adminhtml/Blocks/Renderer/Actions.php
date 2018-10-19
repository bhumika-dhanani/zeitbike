<?php
class IWD_AutoRelatedProducts_Block_Adminhtml_Blocks_Renderer_Actions extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $edit_url = $this->getUrl('*/blocks/edit', array('id' => $row['id']));
        $edit_title = Mage::helper('core')->__('Edit');
        return '<a href="' . $edit_url . '" title="' . $edit_title . '">' . $edit_title . '</a>';
    }
}
