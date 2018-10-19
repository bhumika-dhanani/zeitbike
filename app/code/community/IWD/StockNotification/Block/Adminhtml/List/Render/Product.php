<?php
class IWD_StockNotification_Block_Adminhtml_List_Render_Product extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{


    public function __construct()
    {
        parent::__construct ();
    }

    public function render(Varien_Object $row)
    {

        $_productId = $row->getProductId ();

        $model = Mage::getModel('catalog/product')->setStoreId($row->getStoreId());
        $_product = $model ->load($_productId);



        return $_product->getName();
    }



}