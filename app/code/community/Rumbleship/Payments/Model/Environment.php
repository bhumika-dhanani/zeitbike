<?php
class Rumbleship_Payments_Model_Environment
{
  const SANDBOX_API_HOST = 'sandbox-api.rumbleship.com/';
  const SANDBOX_PAY_HOST = 'sandbox-pay.rumbleship.com';
  const STAGING_API_HOST = 'api.staging-rumbleship.com';
  const STAGING_PAY_HOST = 'pay.staging-rumbleship.com';
  const DEVELOPMENT_API_HOST = 'api.local-rumbleship.com:8080';
  const DEVELOPMENT_PAY_HOST = 'pay.local-rumbleship.com:8081';
  const PRODUCTION_API_HOST = 'api.rumbleship.com';
  const PRODUCTION_PAY_HOST = 'pay.rumbleship.com';
  const DEMO_API_HOST = 'sandbox-api.rumbleship.com/';
  const DEMO_PAY_HOST = 'sandbox-pay.rumbleship.com';
  const DEMO_API_KEY = 'ZyQQRNWuYAnIgQPAaAjQO73oPB';
  const DEMO_BUYER_EMAIL = 'demo+ijw@rumbleship.com';

  private static $envConfig = [
    'SANDBOX' => [
      'api_host' => self::SANDBOX_API_HOST,
      'pay_host' => self::SANDBOX_PAY_HOST
    ],
    'STAGING' => [
      'api_host' => self::STAGING_API_HOST,
      'pay_host' => self::STAGING_PAY_HOST
    ],
    'DEVELOPMENT' => [
      'api_host' => self::DEVELOPMENT_API_HOST,
      'pay_host' => self::DEVELOPMENT_PAY_HOST
    ],
    'PRODUCTION' => [
      'api_host' => self::PRODUCTION_API_HOST,
      'pay_host' => self::PRODUCTION_PAY_HOST
    ],
    'DEMO' => [
      'api_host' => self::DEMO_API_HOST,
      'pay_host' => self::DEMO_PAY_HOST,
      'api_key' => self::DEMO_API_KEY,
      'buyer_email' => self::DEMO_BUYER_EMAIL
    ]
  ];

  public static function getEnvironmentConfigValue($key) {
    $env = self::getStoreConfigValue("config_environment");
    return self::$envConfig[$env][$key];
  }

  public static function getStoreConfigValue($key) {
    $rfiConfig =  Mage::getStoreConfig('payment/rumbleship_payments', Mage::app()->getStore());
    return $rfiConfig[$key] ?: $rfiConfig[$key];
  }

  public static function enabledForWebsite() {
    $customerSession = Mage::getSingleton('customer/session');
    $enabledIds = explode(",", Rumbleship_Payments_Model_Environment::getStoreConfigValue("website_toggle"));
    $currentWebsiteId = Mage::app()->getWebsite()->getId();
    if (empty($enabledIds) || in_array($currentWebsiteId, $enabledIds) !== false) {
      return true;
    }
    return false;
  }

  public static function moduleActive() {
    return Rumbleship_Payments_Model_Environment::getStoreConfigValue("active");
  }

  /*
  * NOTE: for apiKey() and getLoggedInEmail(): if the environment is set to demo mode, we intentionally
  * override getting API key from store config and email from logged in user, in favor of hard coded
  * credentials tied to a specific Demo account on our Sandbox environment. This enables anybody
  * to download module, install it, and see the broad flow work in the context of their store.
  */
  public static function apiKey() {
    return self::isDemo() ? self::getEnvironmentConfigValue("api_key") : self::getStoreConfigValue("api_key");
  }

  public static function getLoggedInEmail() {
    $customerSession = Mage::getSingleton('customer/session');
    if($customerSession->isLoggedIn()) {
      $customer = $customerSession->getCustomer();
      $email = self::isDemo() ? self::getEnvironmentConfigValue("buyer_email") : $customer->getEmail();
      return $email;
    }
  }

  public function toOptionArray() {
    return array(
      ['value' => 'SANDBOX', 'label' => 'Sandbox'],
      ['value' => 'DEMO', 'label' => 'Demo'],
      ['value' => 'STAGING', 'label' => 'Staging'],
      ['value' => 'DEVELOPMENT', 'label' =>'Development'],
      ['value' => 'PRODUCTION', 'label' =>'Production']
    );
  }

  private static function isDemo() {
    $env = self::getStoreConfigValue("config_environment");
    return ($env === "DEMO" || !$env);
  }
}
