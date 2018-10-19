<?php
require_once(Mage::getBaseDir('lib') . '/RumbleshipSDK/autoload.php');

class Rumbleship_Payments_IndexController extends Mage_Core_Controller_Front_Action {
  /**
  * Redirect to Rumbleship after 'Rumbleship Flexible Payments' is selected
  * Call createRumbleshipPurchaseOrder
  * Build redirect URL
  */
  public function redirectAction() {
    $quote = Mage::getModel('checkout/session')->getQuote();
    try {
      $order = $this->createMagePurchaseOrder();
    } catch(Exception $e) {
      Mage::getSingleton('core/session')->addError($e->getMessage);
      Mage::log($e);
      $this->_redirect('*/*/failure');
      return;
    }

    try {
      $apiResp = Mage::getSingleton('Rumbleship_Payments_Model_Api')
        ->login()
        ->createPurchaseOrder($order);
      if($apiResp->status_code >= 400) {
        Mage::log("Error creating Rumbleship Purchase Order.");
        Mage::log($apiResp->body);
        $this->_redirect('*/*/failure');
        return;
      }
    } catch (Exception $e) {
      Mage::log($e);
      $this->_redirect('*/*/failure');
      return;
    }

    $poData = $apiResp->body;
    if (!$poData || !array_key_exists('hashid', $poData)) {
      Mage::getSingleton('core/session')->addError('Error creating purchase order in Rumbleship. Please try again');
      $this->_redirect('*/*/failure');
    };

    $pohashid = $poData['hashid'];
    $payment = $order->getPayment();
    $payment->setAdditionalInformation(['po_hashid' => $pohashid]);
    $payment->save();

    $this->saveToSession($quote, $order);

    $payUrl = Mage::getSingleton('Rumbleship_Payments_Model_Api')->payURL($pohashid);
    $this->_redirectUrl($payUrl);
  }

  /**
  * Return action called when directed back to Magento from Rumbleship checkout
  */
  public function returnAction() {
    $session = Mage::getSingleton('checkout/type_onepage')->getCheckout();
    $order = Mage::getModel('sales/order')->load($session->getLastOrderId());

    try {
      $apiResp = Mage::getSingleton('Rumbleship_Payments_Model_Api')
        ->setJwt($_GET['rfi_token'])
        ->confirmForShipment($order);
    } catch(Exception $e) {
      Mage::log("Exception thrown while confirming PO with RFI.");
      Mage::log($e->getMessage());
      $this->_redirect('*/*/failure');
      return;
    }
    $statusCode = $apiResp->status_code;
    if (!$apiResp->success && $statusCode != 409) {
      Mage::log("Failed to confirm PO with RFI, no exception thrown. API response follows.");
      Mage::log($apiResp);
      $this->_redirect('*/*/failure');
      return;
    }

    if($statusCode === 409){
      Mage::log("attemped to re-confirm order in Rumbleship. Continuing with cart cleanup etc");
    }

    $pohashid = $apiResp->body['hashid'];
    $order->setStatus(Mage_Sales_Model_Order::STATE_PROCESSING);
    $order->addStatusHistoryComment("Purchase Order $pohashid confirmed in Rumbleship. Payment schedule pending shipment.");
    $order->queueNewOrderEmail();
    $order->save();

    Mage::dispatchEvent('rumbleship_payment_processing', ['order' => $order]);

    $this->_redirect('*/*/success');
  }
  /**
  * Order success page
  */
  public function successAction() {
    $checkoutSession = Mage::getSingleton('checkout/type_onepage')->getCheckout();
    if (!$checkoutSession->getLastSuccessQuoteId()) {
      Mage::log("RFI failed to retrieve last successful quote id.");
      $this->_redirect('*/*/failure');
      return;
    }
    $lastQuoteId = $checkoutSession->getLastQuoteId();
    $lastOrderId = $checkoutSession->getLastOrderId();
    $lastRecurringProfiles = $checkoutSession->getLastRecurringProfileIds();
    if (!$lastQuoteId) {
      Mage::log("RFI failed to retrieve lastQuoteId. Failing.");
      $this->_redirect('*/*/failure');
    }
    if (!$lastOrderId && empty($lastRecurringProfiles)) {
      Mage::log("RFI failed to retrieve lastOrder and lastRecurringProfiles was empty. Failing.");
      $this->_redirect('*/*/failure');
      return;
    }

    $this->clearQuote($lastQuoteId);
    $this->_redirect('checkout/onepage/success');
  }
  /**
  * Order failure page
  */
  public function failureAction() {
    $checkoutSession = Mage::getSingleton('checkout/type_onepage')->getCheckout();
    $lastOrderId = $checkoutSession->getLastOrderId();
    Mage::getModel('sales/order')
      ->load($lastOrderId)
      ->setStatus('canceled')
      ->addStatusHistoryComment("An error occured during Rumbleship checkout process. Order cancelled automatically.")
      ->save();
    Mage::getSingleton('core/session')
      ->addError("An error occurred while processing payment for your order.");
    $this->_redirect('checkout/cart');
  }


  private function saveToSession($quote, $order) {
    $quoteid = $quote->getId();
    $orderid = $order->getId();
    $incrementid = $order->getIncrementId();
    $checkoutSession = Mage::getSingleton('checkout/type_onepage')->getCheckout()
      ->setLastSuccessQuoteId($quoteid)
      ->setLastQuoteId($quoteid)
      ->setLastOrderId($orderid)
      ->setIncrementId($incrementid);
  }

  /**
  * Creates an order in Magento: sets info from the $quote on the $order
  * @return $order object
  */
  private function createMagePurchaseOrder() {
    $quote = Mage::getModel('checkout/session')->getQuote();
    $storeId = Mage::app()->getStore()->getStoreId();
    $quoteId = $quote->getId();
    $incrementOrderId = Mage::getSingleton('eav/config')->getEntityType('order')->fetchNewIncrementId($storeId);

    $order = Mage::getModel('sales/order')
      ->setIncrementId($incrementOrderId)
      ->setQuoteId($quoteId)
      ->setStoreId($storeId)
      ->setOrder_currency_code('USD')
      ->setCustomerEmail(Rumbleship_Payments_Model_Environment::getLoggedInEmail())
      ->setCustomer($quote->getCustomer());

    $orderShippingAddress = Mage::getModel('sales/order_address')
      ->setData($quote->getShippingAddress()->getData())
      ->setStoreId($storeId)
      ->setAddressType('shipping');

    $orderBillingAddress = Mage::getModel('sales/order_address')
      ->setData($quote->getBillingAddress()->getData())
      ->setStoreId($storeId)
      ->setAddressType('billing');

    $orderPayment = Mage::getModel('sales/order_payment')
      ->setStoreId($storeId)
      ->setMethod('rumbleship_payments');

    $cartItems = Mage::getSingleton('checkout/cart')->getQuote()->getAllVisibleItems();
    foreach ($cartItems as $productId=>$product)
    {
      $item = Mage::getModel('catalog/product')->load($productId);
      $orderItem = Mage::getModel('sales/order_item')
        ->setStoreId($storeId)
        ->setProductId($productId)
        ->setProductType($item->getTypeId())
        ->setQtyOrdered($product['qty'])
        ->setName($item->getName())
        ->setSku($item->getSku())
        ->setPrice($item->getPrice());
      $order->addItem($orderItem);
    }

    $quote->collectTotals()->save();
    $quote->save();
    $service = Mage::getModel('sales/service_quote', $quote);
    $service->submitAll();
    $order = $service->getOrder();
    $order->setStatus(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT);
    $order->save();

    return $order;
  }

  /**
  * Clears Magento cart after successful order and deletes the $quote
  */
  private function clearQuote() {
    $quoteId = Mage::getModel('checkout/session')->getQuote()->getId();
    $quote = Mage::getModel("sales/quote")->load($quoteID);
    $quote->setIsActive(0)->save();
    $quote->delete();
    $quote->save();
    $cart = Mage::getSingleton('checkout/cart');
    $quoteItems = Mage::getSingleton('checkout/session')
    ->getQuote()
    ->getItemsCollection();
    foreach ($quoteItems as $item) {
      $cart->removeItem($item->getId());
    }
    $cart->save();
  }
}
