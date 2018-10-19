<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Stockstatus
 */
class Amasty_Stockstatus_Block_Bundle_Sales_Order_Items_Renderer extends Mage_Bundle_Block_Sales_Order_Items_Renderer
{
    const BUNDLE_ORDER_EMAIL = 'bundle/email/order/items/order/default.phtml';

    public function getValueHtml($item)
    {
        $html = parent::getValueHtml($item);

        /* Amasty Code start*/
        $bundleOrderEmail = $this->getTemplate() == self::BUNDLE_ORDER_EMAIL;
        $status = Mage::helper('amstockstatus')->getStockStatusByIdInCart(
            $item->getProductId(),
            $item->getQtyOrdered(),
            $bundleOrderEmail
        );
        if ($status) {
            $html .= ' ( ' . $status . ' ) ';
        }
        /*Amasty code end*/
        return $html;
    }
}
