<?php
class Rumbleship_Payments_Model_Paymentmethod extends Mage_Payment_Model_Method_Abstract {
  const CODE                          = 'rumbleship_payments';
  protected $_code                    = self::CODE;
  protected $_isGateway               = true;
  protected $_canAuthorize            = true;
  protected $_canCapture              = true;
  protected $_canCapturePartial       = false;
  protected $_canRefund               = false;
  protected $_canRefundInvoicePartial = false;
  protected $_canVoid                 = false;
  protected $_canUseInternal          = true;
  protected $_canUseCheckout          = true;
  protected $_canUseForMultishipping  = false;
  protected $_isInitializeNeeded      = true;
  protected $_canFetchTransactionInfo = true;
  protected $_canReviewPayment        = false;
  protected $isForceSync              = true;
  protected $_api;
  /**
  * Show Rumbleship checkout only if customer is logged in and has a BSR
  */
  public function isAvailable($quote = null) {
    $customerSession = Mage::getSingleton('customer/session');
    $websiteEnabled = Rumbleship_Payments_Model_Environment::enabledForWebsite();
    $moduleActive = Rumbleship_Payments_Model_Environment::moduleActive();
    if( $websiteEnabled && $moduleActive && $customerSession->isLoggedIn()) {
      try {
        $bsrResp = Mage::getSingleton('Rumbleship_Payments_Model_Api')
          ->getBuyerSupplierRelationship();
      } catch(Exception $e){
        return false;
      }
      return $bsrResp->success;
    }
    return false;
  }

  /**
  * Get redirect URLS
  */
  public function getCheckoutRedirectUrl() {
    return Mage::getUrl('rumbleship_payments/index/redirect');
  }

  public function getTitle() {
    return "Rumbleship Flexible Payments";
  }
}
