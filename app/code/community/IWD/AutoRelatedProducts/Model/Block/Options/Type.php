<?php
class IWD_AutoRelatedProducts_Model_Block_Options_Type
{
    const FEATURED_PRODUCTS = "featured";
    const ALSO_BOUGHT_PRODUCTS = "alsobouht";

    const RELATED_PRODUCTS_SHOPPING_CART = "related_shopping_cart";
    const RELATED_PRODUCTS_PRODUCT_PAGE = "related_product_page";
    const RELATED_PRODUCTS_CATEGORY_PAGE = "related_category_page";

    const NATIVE_CROSSSELL_PRODUCTS = "native_crosssell";
    const NATIVE_UPSELL_PRODUCTS = "native_upsell";
    const NATIVE_RELATED_PRODUCTS = "native_related";
    const NATIVE_GROUPED_PRODUCTS = "native_grouped";

    public function getOptionArray()
    {
        $helper = Mage::helper('iwd_autorelatedproducts');

        return array(
            array(
                'label' => $helper->__("Auto Related Products"),
                'value' => array(
                    array(
                        'value' => self::RELATED_PRODUCTS_PRODUCT_PAGE,
                        'label' => $helper->__("Product Page"),
                    ),
                    array(
                        'value' => self::RELATED_PRODUCTS_CATEGORY_PAGE,
                        'label' => $helper->__("Category Page"),
                    ),
                    array(
                        'value' => self::RELATED_PRODUCTS_SHOPPING_CART,
                        'label' => $helper->__("Shopping cart"),
                    ),
                )
            ),
            array(
                'label' => $helper->__("Native Product Links"),
                'value' => array(
                    array(
                        'value' => self::NATIVE_CROSSSELL_PRODUCTS,
                        'label' => $helper->__("Cross-Sells"),
                    ),
                    array(
                        'value' => self::NATIVE_UPSELL_PRODUCTS,
                        'label' => $helper->__("Up-Sells"),
                    ),
                    array(
                        'value' => self::NATIVE_RELATED_PRODUCTS,
                        'label' => $helper->__("Related Products"),
                    ),
                    array(
                        'value' => self::NATIVE_GROUPED_PRODUCTS,
                        'label' => $helper->__("Grouped Products"),
                    ),
                )
            ),
            array(
                'value' => self::FEATURED_PRODUCTS,
                'label' => $helper->__("Featured Products"),
            ),
            array(
                'value' => self::ALSO_BOUGHT_PRODUCTS,
                'label' => $helper->__("Who Bought This Also Bought"),
            ),
        );
    }

    public function getFlatOptionArray()
    {
        $helper = Mage::helper('iwd_autorelatedproducts');

        return array(
            self::RELATED_PRODUCTS_PRODUCT_PAGE => $helper->__("Auto Related: Product Page"),
            self::RELATED_PRODUCTS_CATEGORY_PAGE => $helper->__("Auto Related: Category Page"),
            self::RELATED_PRODUCTS_SHOPPING_CART => $helper->__("Auto Related: Shopping cart"),

            self::NATIVE_CROSSSELL_PRODUCTS => $helper->__("Native: Cross-Sells"),
            self::NATIVE_UPSELL_PRODUCTS => $helper->__("Native: Up-Sells"),
            self::NATIVE_RELATED_PRODUCTS => $helper->__("Native: Related Products"),
            self::NATIVE_GROUPED_PRODUCTS => $helper->__("Native: Grouped Products"),

            self::FEATURED_PRODUCTS => $helper->__("Featured Products"),
            self::ALSO_BOUGHT_PRODUCTS => $helper->__("Who Bought This Also Bought"),
        );
    }

    public function getArray()
    {
        return array(
            self::RELATED_PRODUCTS_CATEGORY_PAGE,
            self::RELATED_PRODUCTS_PRODUCT_PAGE,
            self::RELATED_PRODUCTS_SHOPPING_CART,

            self::NATIVE_CROSSSELL_PRODUCTS,
            self::NATIVE_UPSELL_PRODUCTS,
            self::NATIVE_RELATED_PRODUCTS,
            self::NATIVE_GROUPED_PRODUCTS,

            self::FEATURED_PRODUCTS,
            self::ALSO_BOUGHT_PRODUCTS,
        );
    }

    public function toOptionArray()
    {
        return $this->getOptionArray();
    }
}