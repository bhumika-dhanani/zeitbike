<?php
class IWD_AutoRelatedProducts_Model_Blocks extends Mage_Core_Model_Abstract
{
    const CURRENT_CONDITIONS = "current_conditions";
    const RELATED_CONDITIONS = "related_conditions";
    const CART_CONDITIONS = "conditions";

    private $current;
    private $related;
    private $cart_conditions;

    public function __construct()
    {
        parent::__construct();

        $this->_initConditions();

        $this->current->getConditions();
        $this->related->getConditions();
        $this->cart_conditions->getConditions();
    }

    public function setPostData($params)
    {
        /** store view **/
        $params['store_view'] = (isset($params['store_view_single'])) ? serialize(array(0, 1)) : serialize($params['store_view']);

        /** customer groups **/
        if (isset($params['customer_group_ids']))
            $params['customer_group_ids'] = serialize($params['customer_group_ids']);

        /** Buttons Add to... **/
        if (isset($params['link_buttons']))
            $params['link_buttons'] = serialize($params['link_buttons']);

        /** conditions **/
        if (isset($params['rule'])) {
            if (isset($params['rule'][self::RELATED_CONDITIONS])) {
                $params[self::RELATED_CONDITIONS] = $params['rule'][self::RELATED_CONDITIONS];
            }

            if (isset($params['rule'][self::CURRENT_CONDITIONS])) {
                $params[self::CURRENT_CONDITIONS] = $params['rule'][self::CURRENT_CONDITIONS];
            }

            if (isset($params['rule'][self::CART_CONDITIONS])) {
                $params[self::CART_CONDITIONS] = $params['rule'][self::CART_CONDITIONS];
            }

            unset($params['rule']);
        }

        /** order statuses **/
        if (isset($params['order_statuses'])) {
            $params['order_statuses_serialized'] = serialize($params['order_statuses']);
            unset($params['order_statuses']);
        }

        if (isset($params['category_id'])) {
            $params['category_id_serialized'] = serialize($params['category_id']);
            unset($params['category_id']);
        }

        $params = Mage::helper('iwd_autorelatedproducts')->_filterDates($params, array('from_date', 'to_date'));

        $this->setData($params);

        $this->loadPost($params);

        /** grids: products ids **/
        if (isset($params['current_selected_products_ids'])) {
            $this->setData("current_products_id_serialized", serialize($params['current_selected_products_ids']));
        }
        if (isset($params['related_selected_products_ids'])) {
            $this->setData("related_products_id_serialized", serialize($params['related_selected_products_ids']));
        }

        $from_date = Mage::getModel('core/date')->timestamp(strtotime($params['from_date'] . ' + 1 days'));
        $this->setData('from_date', date('Y-m-d h:i:s', $from_date));
        $to_date = Mage::getModel('core/date')->timestamp(strtotime($params['to_date'] . ' + 1 days'));
        $this->setData('to_date', date('Y-m-d h:i:s', $to_date));
    }

    public function getCurrentConditions()
    {
        return $this->current;
    }

    public function getRelatedConditions()
    {
        return $this->related;
    }

    public function getShoppingCartConditions()
    {
        return $this->cart_conditions;
    }

    protected function _construct()
    {
        $this->_init('iwd_autorelatedproducts/blocks');

        $this->current = Mage::getModel("iwd_autorelatedproducts/rules", array(
            "conditions_key" => self::CURRENT_CONDITIONS
        ));

        $this->related = Mage::getModel("iwd_autorelatedproducts/rules", array(
            "conditions_key" => self::RELATED_CONDITIONS
        ));

        //$this->cart_conditions = Mage::getModel('salesrule/rule', array(
        $this->cart_conditions = Mage::getModel('iwd_autorelatedproducts/rule_salesrule', array(
            "conditions_key" => self::CART_CONDITIONS
        ));
    }

    public function load($id, $field = null)
    {
        parent::load($id, $field = null);

        $this->_initConditions();

        return $this;
    }

    public function getStoreViewIds(){
        $stores = unserialize($this->getStoreView());
        if(empty($stores))
            return array();

        if(in_array(0, $stores))
        {
            return Mage::getModel('iwd_autorelatedproducts/block_options_storeview')->getAllStores();
        }

        return $stores;
    }

    private function _initConditions(){

        $this->current->setConditionsSerialized($this->getCurrentConditionsSerialized());
        $this->current->setStoreView($this->getStoreViewIds());

        $this->related->setConditionsSerialized($this->getRelatedConditionsSerialized());
        $this->related->setStoreView($this->getStoreViewIds());

        $this->cart_conditions->setConditionsSerialized($this->getShoppingCartConditionsSerialized());
        $this->cart_conditions->setStoreView($this->getStoreViewIds());
    }

    protected function _convertFlatToRecursive(array $data)
    {
        $arr = array();
        foreach ($data as $key => $value) {
            if (in_array($key, array('from_date', 'to_date')) && $value) {
                $value = Mage::app()->getLocale()->date(
                    $value,
                    Varien_Date::DATE_INTERNAL_FORMAT,
                    null,
                    false
                );
            }
            $this->setData($key, $value);
        }

        return $arr;
    }

    public function loadPost(array $data)
    {
        $this->related->loadPost($data);
        $this->current->loadPost($data);
        $this->cart_conditions->loadPost($data);

        $related_conditions_serialize = serialize($this->asArray($this->related->getConditions()));
        $this->setData(self::RELATED_CONDITIONS . "_serialized", $related_conditions_serialize);

        $current_conditions_serialize = serialize($this->asArray($this->current->getConditions()));
        $this->setData(self::CURRENT_CONDITIONS . "_serialized", $current_conditions_serialize);

        $shopping_cart_conditions_serialize = serialize($this->asArray($this->cart_conditions->getConditions()));
        $this->setData("shopping_cart_conditions_serialized", $shopping_cart_conditions_serialize);

        //$this->_convertFlatToRecursive($data);
    }

    public function asArray($conditions)
    {
        $out = array(
            'type' => $conditions->getType(),
            'attribute' => $conditions->getAttribute(),
            'operator' => $conditions->getOperator(),
            'value' => $conditions->getValue(),
            'is_value_processed' => $conditions->getIsValueParsed(),
        );

        $out['aggregator'] = $conditions->getAggregator();

        if (!empty($conditions)) {
            $conditions = $conditions->getConditions();
            if (!empty($conditions)) {
                foreach ($conditions as $_condition) {
                    $out['conditions'][] = $_condition->asArray();
                }
            }
        }

        return $out;
    }

    public function hasButtonAddToCompare()
    {
        return in_array(IWD_AutoRelatedProducts_Model_Block_Options_Links::BUTTON_ADD_TO_COMPARE, unserialize($this->getLinkButtons()));
    }

    public function hasButtonAddToWishlist()
    {
        return in_array(IWD_AutoRelatedProducts_Model_Block_Options_Links::BUTTON_ADD_TO_WISHLIST, unserialize($this->getLinkButtons()));
    }

    public function hasButtonAddToCart()
    {
        return in_array(IWD_AutoRelatedProducts_Model_Block_Options_Links::BUTTON_ADD_TO_CART, unserialize($this->getLinkButtons()));
    }

    public function getProducts()
    {
        
        $this->_initConditions();

        switch ($this->getBlockType()) {
            case IWD_AutoRelatedProducts_Model_Block_Options_Type::RELATED_PRODUCTS_PRODUCT_PAGE:
                return $this->getRelatedProductsByProductPage();
            case IWD_AutoRelatedProducts_Model_Block_Options_Type::RELATED_PRODUCTS_CATEGORY_PAGE:
                return $this->getRelatedProductsByCategoryPage();
            case IWD_AutoRelatedProducts_Model_Block_Options_Type::RELATED_PRODUCTS_SHOPPING_CART:
                return $this->getRelatedProductsByShoppingPage();
            case IWD_AutoRelatedProducts_Model_Block_Options_Type::FEATURED_PRODUCTS:
                return $this->getFeaturedProducts();
            case IWD_AutoRelatedProducts_Model_Block_Options_Type::ALSO_BOUGHT_PRODUCTS:
                return $this->getAlsoBoughtProducts();
            case IWD_AutoRelatedProducts_Model_Block_Options_Type::NATIVE_CROSSSELL_PRODUCTS:
                return $this->getNativeLinkProducts(Mage_Catalog_Model_Product_Link::LINK_TYPE_CROSSSELL);
            case IWD_AutoRelatedProducts_Model_Block_Options_Type::NATIVE_UPSELL_PRODUCTS:
                return $this->getNativeLinkProducts(Mage_Catalog_Model_Product_Link::LINK_TYPE_UPSELL);
            case IWD_AutoRelatedProducts_Model_Block_Options_Type::NATIVE_RELATED_PRODUCTS:
                return $this->getNativeLinkProducts(Mage_Catalog_Model_Product_Link::LINK_TYPE_RELATED);
            case IWD_AutoRelatedProducts_Model_Block_Options_Type::NATIVE_GROUPED_PRODUCTS:
                return $this->getNativeLinkProducts(Mage_Catalog_Model_Product_Link::LINK_TYPE_GROUPED);
        }

        return array();
    }

    protected function getRelatedProductsByProductPage()
    {
        /* check product page */
        if (!Mage::registry('current_product'))
            return array();

        $product_id = Mage::registry('current_product')->getId();
        
        if ($this->getMatchingCurrentProduct($product_id) == false)
            return array();

        /* load related product's ids*/
        $related_products = $this->getMatchingRelatedProduct();

        /* load products */
        return $this->loadProducts($related_products);
    }

    protected function getRelatedProductsByCategoryPage()
    {
        if (!$this->getMatchingCurrentCategory())
            return array();

        /* load related product's ids*/
        $related_products = $this->getMatchingRelatedProduct();

        /* load products */
        return $this->loadProducts($related_products);
    }

    protected function getRelatedProductsByShoppingPage()
    {
        $checkout_cart = Mage::getModel('checkout/cart');
        if (empty($checkout_cart))
            return array();

        $this->cart_conditions->setConditionsSerialized($this->getShoppingCartConditionsSerialized());

        $is_valid = $this->cart_conditions->validate($checkout_cart);

        if ($is_valid == false)
            return array();

        /* load related product's ids*/
        $related_products = $this->getMatchingRelatedProduct();

        /* load products */
        return $this->loadProducts($related_products);
    }

    protected function getAlsoBoughtProducts()
    {
        $product_id = array();

        /* check product page */
        if (Mage::registry('current_product')) {
            $product_id = Mage::registry('current_product')->getId();
        } else {
            /* check cart */
            $cart_items = Mage::getModel('checkout/cart')->getQuote()->getAllItems();
            if (!empty($cart_items)) {
                $product_id = array();
                foreach ($cart_items as $item)
                    $product_id[] = $item->getProduct()->getId();
            }
        }

        if (empty($product_id))
            return array();

        $related_product_ids = Mage::getModel('iwd_autorelatedproducts/alsobought')
            ->setOrderStatuses($this->getOrderStatusesSerialized())
            ->getAlsoBoughtProductIds($product_id);

        return $this->loadProducts($related_product_ids);
    }

    protected function getFeaturedProducts()
    {
        /* load related product's ids*/
        $related_products = $this->getMatchingRelatedProduct();

        /* load products */
        return $this->loadProducts($related_products);
    }

    protected function getNativeLinkProducts($link_type_id)
    {
        /* link type id */
        /* Mage_Catalog_Model_Product_Link::LINK_TYPE_RELATED */
        /* Mage_Catalog_Model_Product_Link::LINK_TYPE_GROUPED */
        /* Mage_Catalog_Model_Product_Link::LINK_TYPE_UPSELL */
        /* Mage_Catalog_Model_Product_Link::LINK_TYPE_CROSSSELL */

        /* check product page */
        if (!Mage::registry('current_product'))
            return array();

        $product_id = Mage::registry('current_product')->getId();

        $product_link = Mage::getModel('catalog/product_link')
            ->getCollection()
            ->addFieldToFilter('product_id', $product_id)
            ->addFieldToFilter('link_type_id', $link_type_id);

        $products_ids = array();
        foreach ($product_link as $prod) {
            $products_ids[] = $prod->getLinkedProductId();
        }

        /* load products */
        return $this->loadProducts($products_ids);
    }


    protected function loadProducts($product_ids)
    {
        $collection = Mage::getResourceModel('catalog/product_collection');
        $collection->addFieldToFilter('entity_id', array('in' => $product_ids));

        $store_id = Mage::app()->getStore()->getStoreId();
        $collection->addStoreFilter($store_id);

        if (!$this->getShowOutOfStock())
            Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($collection);

        $collection->addAttributeToFilter('status', 1)->addStoreFilter();

        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        $attributes = Mage::getSingleton('catalog/config')->getProductAttributes();

        $collection->addAttributeToSelect($attributes);
//            ->addFinalPrice()
//            ->addMinimalPrice()
//            ->addTaxPercents();

        $sort_order = Mage::getModel("iwd_autorelatedproducts/block_options_sortorder");
        $sort_type = $this->getSortOrder();
        if ($sort_order->isRandom($sort_type)) {
            $collection->getSelect()->order(new Zend_Db_Expr('RAND()'));
        } else {
            $collection->setOrder($sort_order->getAttribute($sort_type), $sort_order->getDir($sort_type));
        }

        if ($this->getMaxCountElements() > 0)
            $collection->setPageSize($this->getMaxCountElements());

        $sortProdArray = array();
        foreach ($collection as $_index => $_item)
            $sortProdArray[$_index] = $_item;

        return $sortProdArray;
    }

    protected function getMatchingCurrentCategory()
    {
        $category_id = Mage::getModel('catalog/layer')->getCurrentCategory()->getId();

        $category_ids = unserialize($this->getCategoryIdSerialized());
        if (is_array($category_ids))
            if (in_array($category_id, $category_ids))
                return true;

        return false;
    }

    protected function getMatchingCurrentProduct($product_id)
    {
        $current_product_ids = unserialize($this->getCurrentProductsIdSerialized());
        if (is_array($current_product_ids))
            if (in_array($product_id, $current_product_ids))
                return true;

        $this->current->setConditionsSerialized($this->getCurrentConditionsSerialized());
        $current_product_ids = $this->current->getMatchingProductIds();
        if (is_array($current_product_ids))
            if (in_array($product_id, $current_product_ids))
                return true;

        return false;
    }

    protected function getMatchingRelatedProduct()
    {
        $product_ids = unserialize($this->getRelatedProductsIdSerialized());
        if (!is_array($product_ids) || empty($product_ids))
            $product_ids = array();

        $this->related->setConditionsSerialized($this->getRelatedConditionsSerialized());
        $related_product_ids = $this->related->getMatchingProductIds();

        return array_merge($product_ids, $related_product_ids);
    }
}
