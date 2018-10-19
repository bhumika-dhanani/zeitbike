<?php
class IWD_AlsoBought_Block_Frontend_Block extends Mage_Catalog_Block_Product_Abstract implements Mage_Widget_Block_Interface
{
    /**
     * A model to serialize attributes
     * @var Varien_Object
     */
    protected $_serializer = null;

    private $block_type = "product_page";

    /**
     * Initialization
     */
    protected function _construct()
    {
        $this->_serializer = new Varien_Object();
        parent::_construct();
    }

    protected function _toHtml()
    {
        $this->block_type = $this->getData('block_type');

        $products = $this->getAlsoBoughtProducts();

        $block_title = $this->getTitle();

        $this->assign('products', $products);
        $this->assign('block_title', $block_title);

        return parent::_toHtml();
    }

    const XML_PATH_PRODUCT_PAGE_ENABLE = "iwd_alsobought/product_page/enable";
    const XML_PATH_PRODUCT_PAGE_TITLE = "iwd_alsobought/product_page/title";
    const XML_PATH_PRODUCT_PAGE_MAX_COUNT = "iwd_alsobought/product_page/max_count";

    const XML_PATH_CART_PAGE_ENABLE = "iwd_alsobought/cart_page/enable";
    const XML_PATH_CART_PAGE_TITLE = "iwd_alsobought/cart_page/title";
    const XML_PATH_CART_PAGE_MAX_COUNT = "iwd_alsobought/cart_page/max_count";

    public function getMaxCount()
    {
        if ($this->block_type == "product_page")
            return Mage::getStoreConfig(self::XML_PATH_PRODUCT_PAGE_MAX_COUNT, Mage::app()->getStore());
        return Mage::getStoreConfig(self::XML_PATH_CART_PAGE_MAX_COUNT, Mage::app()->getStore());
    }

    public function getTitle()
    {
        if ($this->block_type == "product_page")
            return Mage::getStoreConfig(self::XML_PATH_PRODUCT_PAGE_TITLE, Mage::app()->getStore());
        return Mage::getStoreConfig(self::XML_PATH_CART_PAGE_TITLE, Mage::app()->getStore());
    }

    public function isEnable()
    {
        if ($this->block_type == "product_page")
            return Mage::getStoreConfig(self::XML_PATH_PRODUCT_PAGE_ENABLE, Mage::app()->getStore());
        return Mage::getStoreConfig(self::XML_PATH_CART_PAGE_ENABLE, Mage::app()->getStore());
    }

    public function getBoughtProducts_ByCartPage()
    {
        $cartItems = Mage::getModel('checkout/cart')->getQuote()->getAllItems();

        if (empty($cartItems))
            return array();

        $productIds = array();
        foreach ($cartItems as $item)
            $productIds[] = $item->getProduct()->getId();

        return Mage::getModel('iwd_alsobought/alsobought')
            ->getBoughtProducts($productIds, $this->getMaxCount());
    }

    public function getBoughtProducts_ByProductPage()
    {
        $request = $this->getRequest()->getParams();

        return Mage::getModel('iwd_alsobought/alsobought')
            ->getBoughtProducts($request['id'], $this->getMaxCount());
    }

    public function getAlsoBoughtProducts()
    {

        if (!$this->isEnable())
            return array();

        if ($this->block_type == "product_page")
            return $this->getBoughtProducts_ByProductPage();
        if ($this->block_type == "cart_page")
            return $this->getBoughtProducts_ByCartPage();

        return array();
    }

    public function customAddToCartButton()
    {
        return false;
    }
    public function hasButtonAddToCart()
    {
        return true;
    }
    public function hasButtonAddToWishlist()
    {
        return true;
    }
    public function hasButtonAddToCompare()
    {
        return true;
    }
}
