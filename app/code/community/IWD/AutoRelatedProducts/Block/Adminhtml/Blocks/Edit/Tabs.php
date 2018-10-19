<?php
class IWD_AutoRelatedProducts_Block_Adminhtml_Blocks_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('iwd_autorelatedproducts_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('iwd_autorelatedproducts')->__('Block Settings'));
    }

    private function getBlockType()
    {
        $id = Mage::app()->getRequest()->getParam('id');
        $type = Mage::app()->getRequest()->getParam('block_type');

        if (!empty($id)) {
            $block = Mage::getModel('iwd_autorelatedproducts/blocks')->load($id);
            return $block->getBlockType();
        } elseif (!empty($type)) {
            $types = Mage::getModel('iwd_autorelatedproducts/block_options_type')->getArray();
            if (in_array($type, $types))
                return $type;
        }

        return null;
    }

    protected function _beforeToHtml()
    {
        $block_type = $this->getBlockType();
        if (!empty($block_type)) {
            /*** STEP 2 ***/
            /*** MAIN SETTINGS ***/
            $this->addTab('form_tab_general', array(
                'label' => Mage::helper('iwd_autorelatedproducts')->__('General Block Settings'),
                'title' => Mage::helper('iwd_autorelatedproducts')->__('General Block Settings'),
                'content' => $this->getLayout()->createBlock('iwd_autorelatedproducts/adminhtml_blocks_edit_tab_general')->toHtml(),
            ));

            switch ($block_type) {
                /*** AUTO RELATED PRODUCTS: PRODUCT PAGE ***/
                case IWD_AutoRelatedProducts_Model_Block_Options_Type::RELATED_PRODUCTS_PRODUCT_PAGE:
                    $this->tabsAutoRelatedProductsProductPage();
                    break;

                /*** AUTO RELATED PRODUCTS: SHOPPING CART ***/
                case IWD_AutoRelatedProducts_Model_Block_Options_Type::RELATED_PRODUCTS_SHOPPING_CART:
                    $this->tabsAutoRelatedProductsShoppingCart();
                    break;

                /*** AUTO RELATED PRODUCTS: CATEGORY PAGE ***/
                case IWD_AutoRelatedProducts_Model_Block_Options_Type::RELATED_PRODUCTS_CATEGORY_PAGE:
                    $this->tabsAutoRelatedProductsCategoryPage();
                    break;

                /*** FEATURED PRODUCTS ***/
                case IWD_AutoRelatedProducts_Model_Block_Options_Type::FEATURED_PRODUCTS:
                    $this->tabsFeaturedProducts();
                    break;

                /*** WHO BOUGHT THIS ALSO BOUGHT PRODUCTS ***/
                case IWD_AutoRelatedProducts_Model_Block_Options_Type::ALSO_BOUGHT_PRODUCTS:
                    $this->tabsAlsoBoughtProducts();
                    break;

                /*** NATIVE: CROSSSELL PRODUCTS ***/
                case IWD_AutoRelatedProducts_Model_Block_Options_Type::NATIVE_CROSSSELL_PRODUCTS:
                    $this->tabsNativeCrosssellProducts();
                    break;

                /*** NATIVE: UPSELL PRODUCTS ***/
                case IWD_AutoRelatedProducts_Model_Block_Options_Type::NATIVE_UPSELL_PRODUCTS:
                    $this->tabsNativeUpsellProducts();
                    break;

                /*** NATIVE: RELATED PRODUCTS ***/
                case IWD_AutoRelatedProducts_Model_Block_Options_Type::NATIVE_RELATED_PRODUCTS:
                    $this->tabsNativeRelatedProducts();
                    break;

                /*** NATIVE: GROUPED PRODUCTS ***/
                case IWD_AutoRelatedProducts_Model_Block_Options_Type::NATIVE_GROUPED_PRODUCTS:
                    $this->tabsNativeGropedProducts();
                    break;
            }

        } else {
            /*** STEP 1: Select block type ***/
            $this->addTab('form_tab_type', array(
                'label' => Mage::helper('iwd_autorelatedproducts')->__('Select Block Type'),
                'title' => Mage::helper('iwd_autorelatedproducts')->__('Select Block Type'),
                'content' => $this->getLayout()->createBlock('iwd_autorelatedproducts/adminhtml_blocks_edit_tab_type')->toHtml(),
            ));
        }

        return parent::_beforeToHtml();
    }

    protected function tabsAutoRelatedProductsProductPage()
    {
        $current_conditions = $this->getLayout()->createBlock('iwd_autorelatedproducts/adminhtml_blocks_edit_tab_current')->toHtml();
        $this->addTab('form_tab_current', array(
            'label' => Mage::helper('iwd_autorelatedproducts')->__('Current Product'),
            'title' => Mage::helper('iwd_autorelatedproducts')->__('Current Product'),
            'content' => $current_conditions,
        ));

        $related_conditions = $this->getLayout()->createBlock('iwd_autorelatedproducts/adminhtml_blocks_edit_tab_related')->toHtml();
        $this->addTab('form_tab_related', array(
            'label' => Mage::helper('iwd_autorelatedproducts')->__('Related Products'),
            'title' => Mage::helper('iwd_autorelatedproducts')->__('Related Products'),
            'content' => $related_conditions,
        ));
    }

    protected function tabsAutoRelatedProductsCategoryPage()
    {
        $current_conditions = $this->getLayout()->createBlock('iwd_autorelatedproducts/adminhtml_blocks_edit_tab_category')->toHtml();
        $this->addTab('form_tab_current', array(
            'label' => Mage::helper('iwd_autorelatedproducts')->__('Category'),
            'title' => Mage::helper('iwd_autorelatedproducts')->__('Category'),
            'content' => $current_conditions,
        ));

        $related_conditions = $this->getLayout()->createBlock('iwd_autorelatedproducts/adminhtml_blocks_edit_tab_related')->toHtml();
        $this->addTab('form_tab_related', array(
            'label' => Mage::helper('iwd_autorelatedproducts')->__('Related Products'),
            'title' => Mage::helper('iwd_autorelatedproducts')->__('Related Products'),
            'content' => $related_conditions ,
        ));
    }

    protected function tabsAutoRelatedProductsShoppingCart()
    {
        $current_conditions = $this->getLayout()->createBlock('iwd_autorelatedproducts/adminhtml_blocks_edit_tab_Cart')->toHtml();
        $this->addTab('form_tab_current', array(
            'label' => Mage::helper('iwd_autorelatedproducts')->__('Shopping Cart'),
            'title' => Mage::helper('iwd_autorelatedproducts')->__('Shopping Cart'),
            'content' => $current_conditions
        ));

        $related_conditions = $this->getLayout()->createBlock('iwd_autorelatedproducts/adminhtml_blocks_edit_tab_related')->toHtml();
        $this->addTab('form_tab_related', array(
            'label' => Mage::helper('iwd_autorelatedproducts')->__('Related Products'),
            'title' => Mage::helper('iwd_autorelatedproducts')->__('Related Products'),
            'content' => $related_conditions,
        ));
    }

    protected function tabsFeaturedProducts(){
        $featured_conditions = $this->getLayout()->createBlock('iwd_autorelatedproducts/adminhtml_blocks_edit_tab_related')->toHtml();

        $this->addTab('form_tab_featured', array(
            'label' => Mage::helper('iwd_autorelatedproducts')->__('Featured Products'),
            'title' => Mage::helper('iwd_autorelatedproducts')->__('Featured Products'),
            'content' => $featured_conditions,
        ));
    }

    protected function tabsAlsoBoughtProducts(){
        $this->addTab('form_tab_also_bought', array(
            'label' => Mage::helper('iwd_autorelatedproducts')->__('Also Bought'),
            'title' => Mage::helper('iwd_autorelatedproducts')->__('Also Bought'),
            'content' => $this->getLayout()->createBlock('iwd_autorelatedproducts/adminhtml_blocks_edit_tab_alsobought')->toHtml(),
        ));
    }

    protected function tabsNativeCrosssellProducts(){

    }

    protected function tabsNativeUpsellProducts(){

    }

    protected function tabsNativeRelatedProducts(){

    }

    protected function tabsNativeGropedProducts(){

    }
}