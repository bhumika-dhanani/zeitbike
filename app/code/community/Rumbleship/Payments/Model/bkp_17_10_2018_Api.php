<?php
require_once(Mage::getBaseDir('lib') . '/RumbleshipSDK/autoload.php');

class Rumbleship_Payments_Model_Api extends Rumbleship\Gateway {
  public function __construct() {
    $host = Rumbleship_Payments_Model_Environment::getEnvironmentConfigValue("api_host");
    parent::__construct($host);
    self::login();
    return $this;
  }

  public function setJwt($jwt) {
    parent::setJwt($jwt);
    return $this;
  }

  public function loginSupplier() {
    self::login(true);
    return $this;
  }

  public function login($onlySupplier = false) {
    $credentials = [
      'id_token' => Rumbleship_Payments_Model_Environment::apiKey()
    ];

    if(!$onlySupplier) {
      $credentials['email'] = Rumbleship_Payments_Model_Environment::getLoggedInEmail();
    }
    $loginSuccess = parent::login($credentials);
    return $this;
  }

  public function payURL($pohashid) {
    $jwt = $this->getJwt();
    return 'https://' .
      Rumbleship_Payments_Model_Environment::getEnvironmentConfigValue('pay_host') .
      "/purchase-orders/$pohashid?rfi_token=$jwt";
  }

  public function createShipment($order) {
    $shipmentData = [
      'carrier' => null,
      'tracking_number' => null,
      'shipping_cents' => $this->dollarsToCents($order->getShippingAmount())
    ];
    $payment = $order->getPayment();
    $paymentInformation = $payment->getAdditionalInformation();
    $pohashid = $paymentInformation['po_hashid'];
    return parent::createShipment($pohashid, $shipmentData);
  }

  public function confirmForShipment($order) {
    $discount = $order->getDiscountAmount();
    $payload = [
      'subtotal_cents' => $this->dollarsToCents(($order->getSubtotal() + $discount)),
      'total_cents' => $this->dollarsToCents($order->getGrandTotal()),
      'shipping_total_cents' => $this->dollarsToCents($order->getShippingAmount()),
      'billing_address' => $this->buildAddressData($order->getBillingAddress(), "billing")
    ];
    if ($order->getShippingAddress()) {
      $payload['shipping_address'] = $this->buildAddressData($order->getShippingAddress(), "shipping");
    } else {
      $payload['shipping_address'] = $this->buildAddressData($order->getBillingAddress(), "shipping");
    }

    $payment = $order->getPayment();
    $paymentInformation = $payment->getAdditionalInformation();
    $pohashid = $paymentInformation['po_hashid'];
    return parent::confirmForShipment($pohashid, $payload);
  }

  public function createPurchaseOrder($order){
    $subtotal = $order->getSubtotal();
    $discount = $order->getDiscountAmount();
    $baseUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
    $returnUrl = $baseUrl . 'rumbleship_payments/index/return';
    $poData = [
      'billing_address' => $this->buildAddressData($order->getBillingAddress(), "billing"),
      'total_cents' => $this->dollarsToCents($order->getGrandTotal()),
      'subtotal_cents' => $this->dollarsToCents(($order->getSubtotal() + $discount)),
      'shipping_total_cents' => $this->dollarsToCents($order->getShippingAmount()),
      'misc' => (object) [
        'x_oid' => $order->getId(),
        'x_order_number' => $order->getIncrementId(),
        'x_rurl' => $returnUrl,
        'x_customer_email' => Rumbleship_Payments_Model_Environment::getLoggedInEmail()
      ],
      'line_items' => []
    ];
    if ($order->getShippingAddress()) {
      $poData['shipping_address'] = $this->buildAddressData($order->getShippingAddress(), "shipping");
    } else {
      $poData['shipping_address'] = $this->buildAddressData($order->getBillingAddress(), "billing");
    }
    $rfiLineItems = [];
    foreach ($order->getAllVisibleItems() as $item) {
      array_push(
        $rfiLineItems,
        [
          'sku' => $item->getSku(),
          'name' => $item->getName(),
          'cost_cents' => $this->dollarsToCents($item->getPrice()),
          'quantity' => $item->getQtyOrdered(),
          'total_cents' => $this->dollarsToCents($item->getRowTotal()),
          'misc' => [
            'product_id' => $item->getProductId()
          ],
        ]
      );
    }
    if($discount){
      array_push(
        $rfiLineItems['line_items'],
        [
          'sku'=> 'DISCOUNT',
          'name'=> 'Supplier Discount',
          'cost_cents' => round($discount * 100),
          'quantity' => 1,
          'total_cents' => round($discount * 100)
        ]
      );
    }
    $poData['line_items'] = $rfiLineItems;

    return parent::createPurchaseOrder($poData);
  }

  private function buildAddressData($addressObj, $prefix="shipping") {
    return [
      $prefix . '_first_name' => $addressObj->getFirstname(),
      $prefix . '_last_name' => $addressObj->getLastname(),
      $prefix . '_address_1' => $addressObj->getStreet(1),
      $prefix . '_address_2' => $addressObj->getStreet(2),
      $prefix . '_city' => $addressObj->getCity(),
      $prefix . '_state' => $addressObj->getRegion(),
      $prefix . '_zip' => $addressObj->getPostcode(),
      $prefix . '_country' => $addressObj->getCountryId(),
      $prefix . '_phone' => $addressObj->getTelephone()
    ];
  }

  private function dollarsToCents($dollars){
    return round($dollars * 100);
  }
}
