<?php
class Rumbleship_Payments_Model_Websites {
  public function websiteToOptionItem($website) {
      $websiteName = $website->getName();
      $websiteId = $website->getId();
    return ['value' => $websiteId, 'label' => $websiteName];
  }

  public function toOptionArray() {
    $allWebsites = Mage::app()->getWebsites();
    return $websiteOptions = array_map('self::websiteToOptionItem', $allWebsites);
  }
}
