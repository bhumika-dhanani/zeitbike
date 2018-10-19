<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * BSS Commerce does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BSS Commerce does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   BSS
 * @package    Bss_FastOrder
 * @author     Extension Team
 * @copyright  Copyright (c) 2014-2015 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
require_once('Mage/Checkout/controllers/CartController.php');
class Rishabhsoft_Speedorder_AjaxController extends Mage_Checkout_CartController
{
  /**
   * Get a products list corresponding to the sku typed
   */

  public function addAction() {
      $cart   = $this->_getCart();
      $params = $this->getRequest()->getParams();
      if($params['isAjax'] == 1){
          $response = array();
          try {
              if (isset($params['qty'])) {
                  $filter = new Zend_Filter_LocalizedToNormalized(
                      array('locale' => Mage::app()->getLocale()->getLocaleCode())
                  );
                  $params['qty'] = $filter->filter($params['qty']);
              }

              $product = $this->_initProduct();
              $related = $this->getRequest()->getParam('related_product');

              /**
               * Check product availability
               */
              if (!$product) {
                  $response['status'] = 'ERROR';
                  $response['message'] = $this->__('Unable to find Product ID');
              }

              $cart->addProduct($product, $params);
              if (!empty($related)) {
                  $cart->addProductsByIds(explode(',', $related));
              }

              $cart->save();

              $this->_getSession()->setCartWasUpdated(true);

              /**
               * @todo remove wishlist observer processAddToCart
               */
              Mage::dispatchEvent('checkout_cart_add_product_complete',
                  array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
              );

              if (!$cart->getQuote()->getHasError()){
                  $message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->htmlEscape($product->getName()));
                  $response['status'] = 'SUCCESS';
                  $response['message'] = $message;
                  //New Code Here
                  $this->loadLayout();
                  $minicart = $this->getLayout()->getBlock('minicart_head')->toHtml();
                  $toplink = $this->getLayout()->getBlock('top.links')->toHtml();
                  Mage::register('referrer_url', $this->_getRefererUrl());
                  $response['minicart'] = $minicart;
                  $response['toplink'] = $toplink;

              }
          } catch (Mage_Core_Exception $e) {
              $msg = "";
              if ($this->_getSession()->getUseNotice(true)) {
                  $msg = $e->getMessage();
              } else {
                  $messages = array_unique(explode("\n", $e->getMessage()));
                  foreach ($messages as $message) {
                      $msg .= $message.'<br/>';
                  }
              }

              $response['status'] = 'ERROR';
              $response['message'] = $msg;
          } catch (Exception $e) {
              $response['status'] = 'ERROR';
              $response['message'] = $this->__('Cannot add the item to shopping cart.');
              Mage::logException($e);
          }
          $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
          return;
      }else{
          return parent::addAction();
      }
  }


    public function showoptionAction()
    {
        $response = array();
        $this->loadLayout();
        $showOptiotns = $this->getLayout()->getBlock('show_optiotns')->toHtml();
        $response['status'] = 'SUCCESS';
        $response['options'] = $showOptiotns;
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
        return;
    }
}