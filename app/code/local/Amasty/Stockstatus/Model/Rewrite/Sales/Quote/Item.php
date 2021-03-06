<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Stockstatus
 */


class Amasty_Stockstatus_Model_Rewrite_Sales_Quote_Item extends Amasty_Stockstatus_Model_Rewrite_Sales_Quote_Item_Pure
{
    public function getMessage($string = true)
    {
        $checkoutTypes = array('checkout', 'amscheckout', 'amscheckoutfront', 'onestepcheckout');
        if (in_array(Mage::app()->getRequest()->getModuleName(), $checkoutTypes)
            && Mage::getStoreConfig('amstockstatus/general/displayincart')
            && Mage::app()->getRequest()->getActionName() !== 'add'
        ) {
            $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $this->getSku());
            if (!$product) {
                $product = Mage::getModel('catalog/product')->load($this->getProduct()->getId());
            }
            if ('true' == (string)Mage::getConfig()->getNode('modules/Amasty_Preorder/active')
                && Mage::helper('ampreorder')->getIsProductPreorder($product)
            ) {
                return parent::getMessage($string);
            }

            $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
            if (!(Mage::getStoreConfig('amstockstatus/general/displayforoutonly')
                    && $product->isSaleable())
                || ($product->isInStock()
                    && $stockItem->getData('qty') <= Mage::helper('amstockstatus')->getBackorderQnt())
            ) {
                $status = Mage::helper('amstockstatus')->getCustomStockStatusText(
                    Mage::getModel('catalog/product')->load($product->getId())
                );
                if ($status) {
                    $status = strip_tags($status);
                    $messages = parent::getMessage($string);
                    $isset = 0;
                    if (is_array($messages)) {

                        foreach ($messages as $mess) {
                            if ($status == $mess) {
                                $isset = 1;
                            }
                        }
                    }
                    if (!$isset) {
                        $this->addMessage($status);
                    }

                }
            }
        }
        return parent::getMessage($string);
    }
}
