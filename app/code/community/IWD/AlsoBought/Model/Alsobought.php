<?php
class IWD_AlsoBought_Model_Alsobought extends Mage_Core_Model_Abstract
{
    const XML_PATH_GENERAL_ORDER_STATUSES = "iwd_alsobought/main_settings/order_statuses";
    const XML_PATH_GENERAL_ALSO_BOUGHT_SORT_ORDER = "iwd_alsobought/main_settings/sort_order_also_bought";
    const XML_PATH_GENERAL_UNPOPULAR_SORT_ORDER = "iwd_alsobought/main_settings/sort_order_unpopular";
    const XML_PATH_GENERAL_STORE = "iwd_alsobought/main_settings/store";
    const XML_PATH_GENERAL_CATEGORY = "iwd_alsobought/main_settings/category";
    const XML_PATH_GENERAL_CHILD = "iwd_alsobought/main_settings/child";
    const XML_PATH_GENERAL_UNPOPULAR = "iwd_alsobought/main_settings/unpopulars";

    public function _construct()
    {
        parent::_construct();
        $this->_init('iwd_alsobought/alsobought');
    }

    public function getGeneralOrderStatuses()
    {
        return explode(',', Mage::getStoreConfig(self::XML_PATH_GENERAL_ORDER_STATUSES, Mage::app()->getStore()));
    }

    public function getSortOrder($also_bought)
    {
        if ($also_bought)
            return Mage::getStoreConfig(self::XML_PATH_GENERAL_ALSO_BOUGHT_SORT_ORDER, Mage::app()->getStore());
        return Mage::getStoreConfig(self::XML_PATH_GENERAL_UNPOPULAR_SORT_ORDER, Mage::app()->getStore());
    }

    public function getIfUnpopularProduct()
    {
        return explode(',', Mage::getStoreConfig(self::XML_PATH_GENERAL_UNPOPULAR, Mage::app()->getStore()));
    }

    public function getGeneralFromAllStores()
    {
        return Mage::getStoreConfig(self::XML_PATH_GENERAL_STORE, Mage::app()->getStore());
    }

    public function getGeneralFromAllCategory()
    {
        return Mage::getStoreConfig(self::XML_PATH_GENERAL_CATEGORY, Mage::app()->getStore());
    }

    public function getGeneralChildForBundle()
    {
        return Mage::getStoreConfig(self::XML_PATH_GENERAL_CHILD, Mage::app()->getStore());
    }

    /*
     * Filter:
     *  - status
     *  - store
     *  - product_id
     * */
    private function getOrderIds($productId)
    {
        $order_item = Mage::getResourceModel('sales/order_item_collection')
            ->addAttributeToSelect('order_id')
            ->addAttributeToFilter('product_id', $productId)
            ->distinct(true)->load();

        $temp_orderId = '';
        foreach ($order_item as $order)
            $temp_orderId[] = $order->getOrderId();

        $_order = Mage::getModel('sales/order')->getCollection()
            ->addAttributeToSelect('entity_id')
            ->addAttributeToFilter('status', array('in' => $this->getGeneralOrderStatuses()))
            ->addAttributeToFilter('entity_id', array('in' => $temp_orderId));

        if (!$this->getGeneralFromAllStores())
            $_order->addAttributeToFilter('store_id', Mage::app()->getStore()->getId());

        $_order->distinct(true)->load();

        $_orderId = '';
        foreach ($_order as $order)
            $_orderId[] = $order->getEntityId();

        return $_orderId;
    }

    private function getProductBoughtIds($productId)
    {
        $_product = Mage::getResourceModel('sales/order_item_collection')
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('order_id', $this->getOrderIds($productId));

        if (!$this->getGeneralChildForBundle())
            $_product->addAttributeToFilter('parent_item_id', array('is' => new Zend_Db_Expr('null')));

        $_product->distinct(true)->load();

        //var_dump($_product->getSelectSql(true)); die;
        $_productIds = array();
        foreach ($_product as $_productData)
            if (!in_array($_productData->getProductId(), $productId))
                $_productIds[] = $_productData->getProductId();

        $_productIds = array_unique($_productIds);

        return $_productIds;
    }

    public function getBoughtProducts($productId, $maxCountProductInBlock)
    {
        $productId = (!is_array($productId)) ? array($productId) : $productId;
        $productBoughtIds = $this->getProductBoughtIds($productId);
        $type_also_bought = true;

        if (!count($productBoughtIds)) {
            $productBoughtIds = $this->UnpopularProducts($productId, $maxCountProductInBlock);
            $type_also_bought = false;
            if (empty($productBoughtIds))
                return NULL;
        }

        $collection = Mage::getResourceModel('catalog/product_collection');
        $collection->addFieldToFilter('entity_id', array('in' => $productBoughtIds));
        $collection->addAttributeToFilter('status', 1)->addStoreFilter();

        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        $attributes = Mage::getSingleton('catalog/config')->getProductAttributes();

        $collection->addAttributeToSelect($attributes)
            ->addFinalPrice()
            ->addMinimalPrice()
            ->addTaxPercents();

        $sort_order = Mage::getModel("iwd_alsobought/config_sortorder");
        $sort_type = $this->getSortOrder($type_also_bought);
        if ($sort_order->isRandom($sort_type)) {
            $collection->getSelect()->order(new Zend_Db_Expr('RAND()'));
        } else {
            $collection->setOrder($sort_order->getAttribute($sort_type), $sort_order->getDir($sort_type));
        }

        if ($maxCountProductInBlock > 0)
            $collection->setPageSize($maxCountProductInBlock);

        if (!$this->getGeneralFromAllCategory()) {
            $categoryIds = Mage::getModel('catalog/product')->load($productId)->getCategoryIds();
            $collection->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left');
            $collection->addAttributeToFilter('category_id', array('in' => $categoryIds));
        }

        $sortProdArray = array();
        foreach ($collection as $_index => $_item)
            $sortProdArray[$_index] = $_item;

        return $sortProdArray;
    }

    private function UnpopularProducts($productIds)
    {
        $product_ids = array();

        $unpopular = $this->getIfUnpopularProduct();

        if (in_array(IWD_AlsoBought_Model_Config_Unpopulars::UNPOPULAR_NOTHING, $unpopular))
            return $product_ids;

        if (in_array(IWD_AlsoBought_Model_Config_Unpopulars::UNPOPULAR_CATEGORY, $unpopular))
        {
            $_ids = $this->ProductsByCategory($productIds);
            $product_ids = is_array($_ids) ? array_merge($product_ids, $_ids) : $product_ids;
        }

        if (in_array(IWD_AlsoBought_Model_Config_Unpopulars::UNPOPULAR_CROSSSELL, $unpopular))
        {
            $_ids = $this->RelatedProducts($productIds, 5); //LINK_TYPE_CROSSSELL
            $product_ids = is_array($_ids) ? array_merge($product_ids, $_ids) : $product_ids;
        }

        if (in_array(IWD_AlsoBought_Model_Config_Unpopulars::UNPOPULAR_UPSELL, $unpopular))
        {
            $_ids = $this->RelatedProducts($productIds, 4); //LINK_TYPE_UPSELL
            $product_ids = is_array($_ids) ? array_merge($product_ids, $_ids) : $product_ids;
        }

        if (in_array(IWD_AlsoBought_Model_Config_Unpopulars::UNPOPULAR_RELATED, $unpopular))
        {
            $_ids = $this->RelatedProducts($productIds, 1); //LINK_TYPE_RELATED
            $product_ids = is_array($_ids) ? array_merge($product_ids, $_ids) : $product_ids;
        }

        return $product_ids;
    }

    private function ProductsByCategory($productIds)
    {
        $categoryIds = array();
        foreach ($productIds as $_product_id)
            $categoryIds = array_merge(Mage::getModel('catalog/product')->load($_product_id)->getCategoryIds(), $categoryIds);
        $categoryIds = array_unique($categoryIds);

        $sortProdArray = array();
        foreach ($categoryIds as $_category_id) {
            $collection = Mage::getModel('catalog/product')
                ->getCollection()
                ->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
                ->addAttributeToSelect('entity_id')
                ->addAttributeToFilter('category_id', $_category_id)
                ->distinct(true);

            foreach ($collection as $_item)
                if (!in_array($_item->getEntityId(), $productIds))
                    $sortProdArray[] = $_item->getEntityId();
        }
        return !empty($sortProdArray) ? $sortProdArray : NULL;
    }

    private
    function RelatedProducts($productIds, $link_type)
    {
        $collection = Mage::getModel('catalog/product_link')
            ->getCollection()
            ->addFieldToFilter('product_id', $productIds)
            ->addFieldToFilter('link_type_id', $link_type);

        $sortProdArray = array();
        foreach ($collection as $_item)
            if (!in_array($_item->getLinkedProductId(), $productIds))
                $sortProdArray[$_item->getLinkedProductId()] = $_item->getLinkedProductId();

        return !empty($sortProdArray) ? $sortProdArray : NULL;
    }
}