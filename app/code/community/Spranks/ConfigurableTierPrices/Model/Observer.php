<?php

class Spranks_ConfigurableTierPrices_Model_Observer
{

    /**
     * Applies the tier pricing structure across different variants of configurable products.
     *
     * @param Varien_Event_Observer $observer
     *
     * @return Spranks_ConfigurableTierPrices_Model_Observer
     */
//    public function checkStockStatus($observer)
//    {
//        // return if disabled or observer already executed on this request
//        if (!Mage::helper('stockcheck')->isEnabled() || Mage::registry('stockcheck_observer_executed')) {
//            return $this;
//        }
//
//        $quote = $observer->getEvent()->getQuote();
//        $outOfStockCount = 0;
//
//        foreach ($quote->getAllItems() as $item) {
//            $product = Mage::getModel('catalog/product')->load($item->getProductId());
//            $stockItem = $product->getStockItem();
//            if ($stockItem->getIsInStock()) {
//                // in stock - for testing only
//                $this->_getSession()->addSuccess(Mage::helper('stockcheck')->__('in stock'));
//                $item->setData('calculation_price', null);
//                $item->setData('original_price', null);
//            }
//            else {
//                //remove item
//                $this->_getCart()->removeItem($item->getId());
//                $outOfStockCount++;
//                $this->_getSession()->addError(Mage::helper('stockcheck')->__('Out of Stock'));
//            }
//        }
//
//        if ($outOfStockCount > 0) {
//        $quote->setTotalsCollectedFlag(false)->collectTotals();
//        }
//
//        Mage::register('stockcheck_observer_executed', true);
//
//        return $this;
//    }
    public function catalogProductGetFinalPrice(Varien_Event_Observer $observer)
    {
        $product = $observer->getProduct();
        if (!Mage::helper('spranks_configurabletierprices')->isExtensionEnabled()
            || Mage::helper('spranks_configurabletierprices')->isProductInDisabledCategory($product)
            || Mage::helper('spranks_configurabletierprices')->isExtensionDisabledForProduct($product)) {
            return $this;
        }



//        $this->checkStockStatus($observer);
        // do not calculate tier prices based on cart items on product page
        // see https://github.com/sprankhub/Spranks_ConfigurableTierPrices/issues/14

//        if (Mage::registry('current_product') || ! $product->isConfigurable()) {
//
//            return $this;
//        }
        // if tier prices are defined, also adapt them to configurable products
        if ($product->getTierPriceCount() > 0) {
            $tierPrice = $this->_calcConfigProductTierPricing($product);
            if ($tierPrice < $product->getData('final_price')) {
//                if (Mage::registry('current_product') || ! $product->isConfigurable()) {
////                    $cartItems = Mage::getSingleton('checkout/session')->getQuote();
//                    return $this;
//                } else {
                    $product->setData('final_price', $tierPrice);
//                }
            }
        }

        return $this;
    }

    /**
     * Get product final price via configurable product's tier pricing structure.
     * Uses qty of parent item to determine price.
     *
     * @param   Mage_Catalog_Model_Product $product
     *
     * @return  float
     */
    private function _calcConfigProductTierPricing($product)
    {
        $tierPrice = PHP_INT_MAX;

        if ($items = $this->_getAllVisibleItems()) {
            // map mapping the IDs of the parent products with the quantities of the corresponding simple products
            $idQuantities = array();
            // go through all products in the quote
            foreach ($items as $item) {
                /** @var Mage_Sales_Model_Quote_Item $item */
                if ($item->getParentItem()) {
                    continue;
                }
                // this is the product ID of the parent!
                $id = $item->getProductId();
                // map the parent ID with the quantity of the simple product
                $idQuantities[$id][] = $item->getQty();
            }
            // compute the total quantity of items of the configurable product
            if (array_key_exists($product->getId(), $idQuantities)) {
                $totalQty  = array_sum($idQuantities[$product->getId()]);
                $tierPrice = $product->getPriceModel()->getBasePrice($product, $totalQty);
            }
        }

        return $tierPrice;
    }

    /**
     * Retrieves all visible quote items from the session.
     *
     * @return array with instances of Mage_Sales_Model_Quote_Item
     */
    private function _getAllVisibleItems()
    {
        if (Mage::helper('spranks_configurabletierprices')->isAdmin()) {
            return Mage::getSingleton('adminhtml/session_quote')->getQuote()->getAllVisibleItems();
        } else {
            return Mage::getSingleton('checkout/session')->getQuote()->getAllVisibleItems();
        }
    }

}
