<?php
class IWD_StockNotification_Block_Adminhtml_List_Render_Type extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{


    public function __construct()
    {
        parent::__construct ();
    }

    public function render(Varien_Object $row)
    {

        $_productId = $row->getProductId ();
        $_parentId = $row->getParentId ();

        if (empty($_parentId)){
            $model = Mage::getModel('catalog/product')->setStoreId($row->getStoreId());
            $_product = $model ->load($_productId);
            $type = $_product->getTypeId();
            $type = uc_words($type);
            return $this->helper('stocknotification')->__($type);
        }

        $model = Mage::getModel('catalog/product')->setStoreId($row->getStoreId());
        $_product = $model ->load($_parentId);
        $type = $_product->getTypeId();
        $type = uc_words($type);
        return $this->helper('stocknotification')->__($type);
    }



}