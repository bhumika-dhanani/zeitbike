<?php
class IWD_AutoRelatedProducts_Model_Block_Options_Blocks
{
    public function getOptionArray()
    {
        $blocks = Mage::getModel("iwd_autorelatedproducts/blocks")->getCollection();
        $opt = array(array('value' => -1, 'label' => Mage::helper('iwd_autorelatedproducts')->__("-- No selected --")));

        foreach ($blocks as $block) {
            $label = $block->getTitle();
            $label = !empty($label) ? $block->getTitle() : "ID: " . $block->getId();
            $opt[] = array('value' => $block->getId(), 'label' => $label);
        }

        return $opt;
    }


    public function toOptionArray()
    {
        return $this->getOptionArray();
    }
}