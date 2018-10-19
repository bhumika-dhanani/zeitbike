<?php
class IWD_AutoRelatedProducts_Model_Alsobought extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('iwd_alsobought/alsobought');
    }

    private $statuses = array();

    public function setOrderStatuses($statuses_serialized)
    {
        $this->statuses = unserialize($statuses_serialized);
        return $this;
    }

    public function getOrderStatuses()
    {
        return $this->statuses;
    }

    public function getIncludeBundleChild()
    {
        return false;
    }

    protected function getOrderIdsItems($product_id)
    {
        $order_items = Mage::getResourceModel('sales/order_item_collection')
            ->addAttributeToSelect('order_id')
            ->addAttributeToFilter('product_id', $product_id)
            ->distinct(true)->load();

        $temp_order_id = array();
        foreach ($order_items as $item)
            $temp_order_id[] = $item->getOrderId();

        return $temp_order_id;
    }

    protected function getOrderIds($product_id)
    {
        if (empty($this->statuses)) {
            return array();
        }

        $temp_order_id = $this->getOrderIdsItems($product_id);
        $_orders = Mage::getModel('sales/order')->getCollection()
            ->addAttributeToSelect('entity_id')
            ->addAttributeToFilter('status', array('in' => $this->getOrderStatuses()))
            ->addAttributeToFilter('entity_id', array('in' => $temp_order_id));

        $_orders->addAttributeToFilter('store_id', Mage::app()->getStore()->getId());
        $_orders->distinct(true)->load();

        $_order_ids = array();
        foreach ($_orders as $order)
            $_order_ids[] = $order->getEntityId();

        return $_order_ids;
    }

    public function getAlsoBoughtProductIds($product_id)
    {
        $order_ids = $this->getOrderIds($product_id);

        if (empty($order_ids)) {
            return array();
        }

        $_products = Mage::getResourceModel('sales/order_item_collection')
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('order_id', $order_ids);

        if (!$this->getIncludeBundleChild())
            $_products->addAttributeToFilter('parent_item_id', array('is' => new Zend_Db_Expr('null')));

        $_products->distinct(true)->load();

        $_product_ids = array();
        foreach ($_products as $_product)
            if (!in_array($_product->getProductId(), $_product_ids))
                $_product_ids[] = $_product->getProductId();

        $_product_ids = array_unique($_product_ids);

        return $_product_ids;
    }
}