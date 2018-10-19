<?php
require_once(Mage::getBaseDir('lib') . '/RumbleshipSDK/autoload.php');

class Rumbleship_Payments_Model_Observer {
  protected $_api;
  /**
  * Create a shipment in Rumbleship once the observer detects a Magento shipment
  * has been created for a Rumbleship order
  */
  public function createRumbleshipShipment($observer) {
    $shipment = $observer->getEvent()->getShipment();
    $order = $shipment->getOrder();
    $shipmentsCollectionCount = $order->getShipmentsCollection()->count();
    $payment = $order->getPayment();

  	$payment_method_code = $payment->getMethodInstance()->getCode();
  	if($payment_method_code != Rumbleship_Payments_Model_Paymentmethod::CODE){
  		return;
  	}

    $paymentInformation = $payment->getAdditionalInformation();
    $existingShipment = $paymentInformation['shp_hashid'];
    if($shipmentsCollectionCount > 0 && $existingShipment ){
      Mage::log("Short circuiting creating additional Rumbleship shipments, $shipmentsCollectionCount already exist.");
      return;
    }

    $pohashid = $paymentInformation['po_hashid'];
    try {
      $apiResp = Mage::getModel('Rumbleship_Payments_Model_Api')
        ->loginSupplier()
        ->createShipment($order);
    } catch(Exception $e) {
      $message = $e->getMessage();
      Mage::log("Failed to create shipment in Rumbleship $pohashid.");
      Mage::log($message);
      $errorBody = "Failed to create shipment for $pohashid in Rumbleship. Error: $message.";
      Mage::getSingleton('core/session')->addError($errorBody);
      throw $e;
    }

    if($apiResp->success){
      $shipmentData = $apiResp->body;
      $payableTotal = "$" . ($shipmentData['payable']['discount_cents'] / 100);
      $payableDate = $shipmentData['payable']['scheduled_at'];
      $shipmentHashid = $shipmentData['hashid'];
      $paymentInformation['shp_hashid'] = $shipmentHashid;
      $payment->setAdditionalInformation($paymentInformation);
      $commentBody = "Purchase Order $pohashid shipped ($shipmentHashid) in Rumbleship. AP of $payableTotal scheduled for $payableDate";
    } else {
      Mage::log("Failed to create shipment in Rumbleship");
      Mage::log($apiResp->status_code);
      if($apiResp->status_code === 409){
        $existingShipment = $paymentInformation['shp_hashid'];
        $commentBody = "Purchase Order $pohashid already marked shipped in full in Rumbleship as $existingShipment. \nSubsequent shipments are allowed, but not recorded in Rumbleship.";
      } else {
        throw new Exception("Failed to create a shipment for $pohashid in Rumbleship. Please try creating shipment again.");
      }
    }

	  $order->addStatusHistoryComment($commentBody);
	  $order->save();
  }
}
