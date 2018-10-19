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
class Bss_FastOrder_AjaxController extends Mage_Checkout_CartController
{
  /**
   * Get a products list corresponding to the sku typed
   */
  public function _getCsvValues($string, $separator=",")
  {
      $elements = explode($separator, trim($string));
      $lengthElement = count($elements);
      for ($i = 0; $i < $lengthElement; $i++) {
          $nquotes = substr_count($elements[$i], '"');
          if ($nquotes %2 == 1) {
              for ($j = $i+1; $j < $lengthElement; $j++) {
                  if (substr_count($elements[$j], '"') > 0) {
                      // Put the quoted string's pieces back together again
                      array_splice($elements, $i, $j-$i+1, implode($separator, array_slice($elements, $i, $j-$i+1)));
                      break;
                  }
              }
          }
          if ($nquotes > 0) {
              // Remove first and last quotes, then merge pairs of quotes
              $qstr =& $elements[$i];
              $qstr = substr_replace($qstr, '', strpos($qstr, '"'), 1);
              $qstr = substr_replace($qstr, '', strrpos($qstr, '"'), 1);
              $qstr = str_replace('""', '"', $qstr);
          }
          $elements[$i] = trim($elements[$i]);
      }
      return $elements;
  }

  public function indexAction() {
    $max = Mage::helper('fastorder')->getMaxResults();
    $name = $this->getRequest()->getParam('sku');

    $collection = Mage::getModel('catalog/product')->getCollection()
    ->addAttributeToSelect('*')
    ->addStoreFilter()
    ->addUrlRewrite()
    ->setPage(1, $max);

    $arrayFillter = Mage::getStoreConfig('bss_fastorder/general_settings/fastorder_skusearch_enable') ? 
      array(
        array(
         'attribute' => 'sku',
         'like' => '%'.$name.'%'
        ),
        array(
         'attribute' => 'name',
         'like' => '%'.$name.'%'
        )
      ) :
      array(
        array(
         'attribute' => 'name',
         'like' => '%'.$name.'%'
        )
      );
    $collection->addAttributeToFilter($arrayFillter);

    Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($collection);

    if(!Mage::getStoreConfig('bss_fastorder/general_settings/fastorder_subproduct_enable'))
    {
      Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
    }

    Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($collection);

    $result = array();
    $groupId = Mage::helper('fastorder')->getCustomerGroup();
    $groups = unserialize(Mage::helper('fastorder')->getConfigGroup());

    foreach($collection as $item)
    {
      if (!$item->getStockItem()->getIsInStock()) {
        continue;
      }
      foreach ($groups as $group) {
          $skuArr = explode(',', $group['product_sku']);
          if($groupId == $group['customer_group'] && in_array($item->getSku(), $skuArr)){
            $disable[] =  $item->getSku();
          }
      }
      if(!empty($disable) && is_array($disable) && in_array($item->getSku(), $disable)) {
        continue;
      }
      $prod = array();
      $prod['prodid'] = $item->getId();
      $prod['name'] = $item->getName();
      $prod['sku'] = $item->getSku();
      $prod['thumbnail'] = Mage::helper('catalog/image')->init($item, 'thumbnail')->__toString();
      $prod['price'] = Mage::helper('core')->currency($item->getFinalPrice(),true,true);
      $prod['typeid'] = $item->getTypeId();
      $product = $this->_getProduct($item->getId());
      if($product->isVisibleInSiteVisibility()){
        $prod['url'] = $item->getProductUrl($item->getId());
      }else{
        $prod['url'] = 'javascript:void(0)';
      }
      $tierprices = $product->getTierPrice();
      if($tierprices){
        $i = 0;
        $prod['tierprices'] .= "<input type='hidden' data-qty='1' class='finalprice tierprice' value='".$product->getFinalPrice(). "'/>";
        foreach ($tierprices as $price){
          if($product->getFinalPrice() > $price['price'] ){
            $prod['tierprices'] .= "<input type='hidden' data-qty='". number_format($price['price_qty'],0). "' class='tierprice tier-".$i."' value='".$price['price'] .  "'/>";
            $i++;
          }
        }
      }
      $options = $product->getOptions();
      $prod['option'] = !empty($options) ? 1 : 0;
      $result[] = $prod;
    }
    return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
  }

  public function addAction() {
    if(Mage::getSingleton('fastorder/session')->getSessionForm()) Mage::getSingleton('fastorder/session')->setSessionForm();
    $itemsParam = $this->getRequest()->getParams();
    parse_str($itemsParam['items'], $items);
    $success = false;
    $error = false;
    $num = 0;
    $messageError = '';
    $message = '';
    for($i=0; $i < count($items['sku']) ;$i++) {
      if(!empty($items['sku'][$i])) {
        $cart   = Mage::getModel('checkout/cart');
        $sku = $items['sku'][$i];
        $j= $i+1;
        $name = 'fastorder-ref-'.$j;
        $qty = $items['fastorder-qty'][$i];
        $id = $this->_getProductId($sku);
        $product = $this->_getProduct($id);
        if ($id == '') {
          Mage::getSingleton('checkout/session')->addError($this->__("The SKU you entered was not found."));
          $success = false;
          $this->_goBack();
          return;
        }else{
          $params = array();
          $params['product'] = $id;
          $params['qty'] = $qty;
          if($items['typeid'][$i] != 'configurable') {
            $session = Mage::getSingleton('fastorder/session')->getCustomOptionValue();
            $params['options'] = $session[$i];
          }else{
            $session = Mage::getSingleton('fastorder/session')->getOptionsArrays();
            $params['super_attribute'] = $session[$i][0];
            $params['options'] = $session[$i][1];
          }
          try {
            $cart->addProduct($product, $params);
            if($num == 0){
              $message .= Mage::helper('core')->escapeHtml($product->getName());
            }else{
              $message .= Mage::helper('core')->escapeHtml(', ' . $product->getName());
            }
            $success = true;
            $num++;
          } catch (Mage_Core_Exception $e) {
            $error = true;
            $messageError .= $this->__($e->getMessage() . '<br>');
          }
        }
      }
    };
    
    $_response = array();
    if($success && !$error){
      if($num == 1){
        $result = $this->__('%s was added to your shopping cart.', $message);
      }else{
        $result = $this->__('%s were added to your shopping cart.', $message);
      }
      $cart->save();
      $this->_getSession()->setCartWasUpdated(true);
      $_response['success'] = $this->__('Items added to cart successfully.');
      $_response['messagesuccess'] = $result;
      Mage::getSingleton('checkout/session')->addSuccess($result);
    }
    if(!$success && !$error){
      $result = $this->__('Please insert at least 1 products');
      $_response['messageerror'] = $result;
    }
    if($error){
       $_response['messageerror'] = $messageError;
    }
    $this->loadLayout();
    return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($_response));
  }
  public function optionAction() {
    $params = $this->getRequest()->getParams();
    Mage::getSingleton('fastorder/session')->setRowFastOrder($params['row']);
    Mage::getSingleton('fastorder/session')->setPostionFastOrder($params['position']);
    $product = $this->_initProduct();
    Mage::register('product', $product);

    Mage::register('current_product', $product);

    $update = $this->getLayout()->getUpdate();
    $this->addActionLayoutHandles();

    $update->addHandle('PRODUCT_TYPE_'.$product->getTypeId());
    $update->addHandle('PRODUCT_'.$product->getId());

    if ($product->getPageLayout()) {
      $this->getLayout()->helper('page/layout')
      ->applyHandle($product->getPageLayout());
    }

    $this->loadLayoutUpdates();
    $update->addUpdate($product->getCustomLayoutUpdate());

    $this->generateLayoutXml()->generateLayoutBlocks();

    if ($product->getPageLayout()) {
      $this->getLayout()->helper('page/layout')
      ->applyTemplate($product->getPageLayout());
    }
    $this->renderLayout();
    return;
  }
  public function saveOptionAction() {
    $params = $this->getRequest()->getParams();
    parse_str($params['items'], $items);
    $row = $params['row'] - 1;
    $session = Mage::getSingleton('fastorder/session')->getOptionsArrays();
    if($session && is_array($session)) {
    }else  {
      $session = array();
    }
    $session[$row][0] = $items['fastorder_super_attribute'];
    $session[$row][1] = $items['options'];//echo '<pre>';print_r($session);die();
    Mage::getSingleton('fastorder/session')->setOptionsArrays($session);
    Mage::getSingleton('fastorder/session')->setTest('test');
    return 'done';
  }
  public function customSimpleOptionAction() {
    $params = $this->getRequest()->getParams();
    parse_str($params['items'], $param);
    $row = $params['row'] - 1;
    $session = Mage::getSingleton('fastorder/session')->getCustomOptionValue();
    if($session && is_array($session)) {

    }else {
      $session = array();
    }
    $session[$row] = $param['options'];
    Mage::getSingleton('fastorder/session')->setCustomOptionValue($session);
    return 'done';
  }

  public function importCsvAction(){
    $csvFile = $_FILES['file']['tmp_name'];
    $csv = trim(file_get_contents($csvFile));
    $exceptions = array();
    $csvLines = explode("\n", $csv);
    $csvLine = array_shift($csvLines);
    $csvLine = $this->_getCsvValues($csvLine,",");
    $success = false;
    $error2 = false;
    foreach ($csvLine as $key => $value) {
      $value = strtolower($value);
      if($value == 'sku'){
        $skuCol = $key;
        $success = true;
      }
      if($value == 'name') $nameCol = $key;
      if($value == 'qty')  $qtyCol = $key;
      if($value == 'id')  $idCol = $key;
    }
    if($success){
      foreach ($csvLines as $rows) {
        $rows = $this->_getCsvValues($rows,",");
        $skuImport  = $rows[$skuCol];
        $nameImport = $rows[$nameCol];
        $qtyImport  = $rows[$qtyCol];
        $idImport  = $rows[$idCol];
        if($skuImport){
          $collection = Mage::getModel('catalog/product')->getCollection()
                        ->addAttributeToSelect('*')
                        ->addStoreFilter()
                        ->addUrlRewrite()
                        ->addAttributeToFilter('type_id', array('eq' => 'simple'))
                        ->addAttributeToFilter('sku', array('eq' => $skuImport));
        }else{
          continue;
        }
        if(empty($collection)) {
          $error2 = true;
          continue;
        }
        Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($collection);
        if(!Mage::getStoreConfig('bss_fastorder/general_settings/fastorder_subproduct_enable')){
          Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
        }
        Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($collection);
        $result = array();
        foreach($collection as $item){
          $prod = array();
          $prod['prodid'] = $item->getId();
          $prod['name'] = $item->getName();
          $prod['sku'] = $item->getSku();
          $prod['thumbnail'] = Mage::helper('catalog/image')->init($item, 'thumbnail')->__toString();
          $prod['price'] = Mage::helper('core')->currency($item->getFinalPrice(),true,true);
          $prod['url'] = $item->getProductUrl();
          $prod['typeid'] = $item->getTypeId();
          $prod['qty'] = $qtyImport;

          if($prod['typeid'] == 'simple'){
            $product = $this->_getProduct($item->getId());
            $options = $product->getOptions();
            if(!empty($options)) {
              continue;
            }
          }else{
            continue;
          }
          $result = $prod;
        }
        $result2[] = $result;
      }
    }else{
      return false;
    }
    if(!$result2) $result2[] = array('error'=>true);
    if($error2 && $result2) $result2[] = array('error2'=>true);
    return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result2));
  }

  public function loadFormAction(){
    $this->loadLayout();
    return $this->getResponse()->setBody($this->getLayout()->createBlock('core/template')->setTemplate('bss/fastorder/form-all.phtml')->toHtml());
  }

  public function saveSessionFormAction(){
    if(Mage::helper('fastorder')->getCustomerSession()){
      $params = $this->getRequest()->getParams();
      Mage::getSingleton('fastorder/session')->setSessionForm($params['formsession']);
    }
  }

  protected function _getProduct($id){
    return Mage::getModel('catalog/product')->load($id);
  }

  protected function _getProductId($sku){
    return Mage::getModel('catalog/product')->getIdBySku($sku);
  }

    public function speedorderAction()
    {
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
                    $toplink = $this->getLayout()->getBlock('minicart_head')->toHtml();
                    Mage::register('referrer_url', $this->_getRefererUrl());
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
}