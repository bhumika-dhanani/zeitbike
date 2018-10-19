<?php

trait IWD_DynamicSKURewrite_Model_Sales_Order_ApiTrait
{
    protected function _initOrder($orderIncrementId)
    {
        $order = parent::_initOrder($orderIncrementId);

        foreach ($order->getAllItems() as $item) {
            if ($item->getData('product_type') == 'simple') {
                $productId = $item->getData('product_id');
                $productOriginalSku = Mage::getModel('catalog/product')->load($productId)->getSku();
                $item->setData('sku', $productOriginalSku);
            }
        }

        return $order;
    }
}