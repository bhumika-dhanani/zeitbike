<?php
/**
 * Magedelight
 * Copyright (C) 2014  Magedelight <info@magedelight.com>
 *
 * NOTICE OF LICENSE
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://opensource.org/licenses/gpl-3.0.html.
 *
 * @category   Ktpl
 * @package    Ktpl_Catalogprint
 * @copyright  Copyright (c) 2014 Mage Delight (http://www.magedelight.com/)
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 * @author     Magedelight <info@magedelight.com>
 */

class Ktpl_Catalogprint_Helper_Data extends Mage_Core_Helper_Abstract
{
protected $_excludeString = 'Excl.Tax';
    protected $_includeString = 'Incl.Tax';
    protected $_regularString = 'RegularPrice';
    protected $_specialString = 'SpecialPrice';
    protected $_fromString = 'From';
    protected $_toSting = 'To';
    protected $_starting = 'Starting at:';
    public function getFancyboxOptions(){
        $array = array();
        $helper = Mage::helper("catalogprint");
        $array['layout_options'][] = $helper->__('Print PDF Catalog Layout View');
        $array['layout_options'][] = $helper->__('Grid Layout');
        $array['layout_options'][] = $helper->__('List Layout');
        
        $array['pdf_options'][] = $helper->__('Print PDF Catalog Options');
        $array['pdf_options'][] = $helper->__('Product Categories/Sub Categories Headings');
        $array['pdf_options'][] = $helper->__('Title/cover page');
        $array['pdf_options'][] = $helper->__('Table of Contents with page numbers');
        $array['pdf_options'][] = $helper->__('Document index by Product SKU in ascending order.');
        $array['pdf_options'][] = $helper->__('Document index by Product Name in alphabetical sorted ascending order.');
        
        return Mage::helper("core")->jsonEncode($array);
    }
    public function getTirePrices($product)
    {
        $cache = Mage::app()->getCache();
        $cachedPrices = $cache->load("tire_price".$product->getId());
        if(count(Mage::helper('core')->jsonDecode($cachedPrices)) <= 0){
            $product = Mage::getModel("catalog/product")->load($product->getId());
        $prices = $product->getFormatedTierPrice();
        
        if (isset($parent) && $parent->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
            /* @var $bundlePriceModel Mage_Bundle_Model_Product_Price */
            $bundlePriceModel = Mage::getModel('bundle/product_price');
        }
        
        $res = array();
        if (is_array($prices)) {
            foreach ($prices as $price) {
                $price['price_qty'] = $price['price_qty'] * 1;
                $productPrice = $product->getPrice();
                if ($product->getPrice() != $product->getFinalPrice()) {
                    $productPrice = $product->getFinalPrice();
                }
                // Group price must be used for percent calculation if it is lower
                $groupPrice = $product->getGroupPrice();
                if ($productPrice > $groupPrice) {
                    $productPrice = $groupPrice;
                }
                if ($price['price'] < $productPrice) {
                    // use the original prices to determine the percent savings
                    $price['savePercent'] = ceil(100 - ((100 / $productPrice) * $price['price']));

                    // if applicable, adjust the tier prices
                    if (isset($bundlePriceModel)) {
                        $price['price']         = $bundlePriceModel->getLowestPrice($parent, $price['price']);
                        $price['website_price'] = $bundlePriceModel->getLowestPrice($parent, $price['website_price']);
                    }

                    $tierPrice = Mage::app()->getStore()->convertPrice(
                        Mage::helper('tax')->getPrice($product, $price['website_price'])
                    );
                    $price['formated_price'] = Mage::app()->getStore()->formatPrice($tierPrice);
                    $price['formated_price_incl_tax'] = Mage::app()->getStore()->formatPrice(
                        Mage::app()->getStore()->convertPrice(
                            Mage::helper('tax')->getPrice($product, $price['website_price'], true)
                        )
                    );
                    if (Mage::helper('catalog')->canApplyMsrp($product)) {
                        $oldPrice = $product->getFinalPrice();
                        $product->setPriceCalculation(false);
                        $product->setPrice($tierPrice);
                        $product->setFinalPrice($tierPrice);

                        $this->getLayout()->getBlock('product.info')->getPriceHtml($product);
                        $product->setPriceCalculation(true);

                        $price['real_price_html'] = $product->getRealPriceHtml();
                        $product->setFinalPrice($oldPrice);
                    }
                    $res[] = Mage::helper("catalogprint")->__("Use ".$price['price_qty']." for ".strip_tags($price['formated_price_incl_tax'])." each and save ".$price['savePercent']."%");
                    $cache->save(Mage::helper('core')->jsonEncode($res),"tire_price".$product->getId());
                }
            }
        }
        return $res;
        }
        return Mage::helper('core')->jsonDecode($cachedPrices);
    }
    
    public function getPriceBlock($product, $customerGroup = null, $websiteId = null, $storeId = null){
        
        $_taxHelper = Mage::helper('tax');
        $_coreHelper = Mage::helper('core');
        $isGroupPriceEnabled = (boolean)Mage::getStoreConfig('catalogprint/general/display_group_price',$storeId);
        $priceString = '';
        
            $groupSpecialPrice = null;
            if($product->getTypeId() == 'bundle' || $product->getTypeId() == 'grouped'){
            $product = Mage::getModel("catalog/product")
                        ->getCollection()->setStore($product->getStore())
                        ->addIdFilter(array($product->getId()))
                        ->addAttributeToSelect(Mage::getSingleton("catalog/config")->getProductAttributes())
                        ->addMinimalPrice()
                        ->addFinalPrice()
                        ->addTaxPercents()
                        ->load()
                        ->getFirstItem();
                            $_minimalPriceTax = null;
                            $_maximalPriceTax = null;
                            $_minimalPriceInclTax = null;
                            $_maximalPriceInclTax = null;
                if(!is_null($customerGroup) && !is_null($websiteId) && $isGroupPriceEnabled){
                    $coreResource = Mage::getSingleton('core/resource');
                    $readAdapter = $coreResource->getConnection('core_read');
                    $bundlePriceTable = $coreResource->getTableName('catalog/product_index_price');
                    $query = "SELECT `min_price`,`max_price` FROM `".$bundlePriceTable."` WHERE `entity_id`='".$product->getId()."' AND `customer_group_id`='".$customerGroup."' AND `website_id`='".$websiteId."' LIMIT 1";
                    $result = $readAdapter->fetchAll($query);
                    if(is_array($result) && count($result) > 0){
                        $_minimalPriceTax = $result[0]['min_price'];
                        $_maximalPriceTax = $result[0]['max_price'];
                
                        $_minimalPriceInclTax = $_taxHelper->getPrice($product, $_minimalPriceTax, true);
                        $_maximalPriceInclTax = $_taxHelper->getPrice($product, $_maximalPriceTax, true);
                    }
                }
            }else{
                $product = Mage::getModel("catalog/product")->setStoreId($product->getStoreId())->load($product->getId());
                
                if(!is_null($customerGroup) && !is_null($websiteId) && $isGroupPriceEnabled){
                    $groupPrice = $product->getData("group_price");
                    foreach($groupPrice as $groupPriceUnit){
                        if(($groupPriceUnit['website_id'] == $websiteId || $groupPriceUnit['website_id'] == 0) && $groupPriceUnit['cust_group'] == $customerGroup){
                            $groupSpecialPrice = $groupPriceUnit['price'];
                        }
                    }
                }
            }
            $_simplePricesTax = ($_taxHelper->displayPriceIncludingTax() || $_taxHelper->displayBothPrices());
            $_minimalPriceValue = $product->getMinimalPrice();
            if(!is_null($groupSpecialPrice)){
                $_price = $_taxHelper->getPrice($product, $groupSpecialPrice);
                $_regularPrice = $_taxHelper->getPrice($product, $groupSpecialPrice, $_simplePricesTax);
                $_finalPrice = $_taxHelper->getPrice($product, $groupSpecialPrice);
                $_finalPriceInclTax = $_taxHelper->getPrice($product, $groupSpecialPrice, true);
            }else{
                $_price = $_taxHelper->getPrice($product, $product->getPrice());
                $_regularPrice = $_taxHelper->getPrice($product, $product->getPrice(), $_simplePricesTax);
                $_finalPrice = $_taxHelper->getPrice($product, $product->getFinalPrice());
                $_finalPriceInclTax = $_taxHelper->getPrice($product, $product->getFinalPrice(), true);
            }
            if($product->getTypeId() == 'simple' || $product->getTypeId() == 'configurable' || $product->getTypeId() == 'virtual' || $product->getTypeId() == 'downloadable'){
                if($_taxHelper->displayBothPrices()){
                    if($_finalPrice >= $_price){
                        $priceString .= $_taxHelper->__('Excl. Tax:').' '.$_coreHelper->formatPrice($_price,false).', ';
                        $priceString .= $_taxHelper->__('Incl. Tax:').' '.$_coreHelper->formatPrice($_finalPriceInclTax,false);
                    }else{
                        $priceString .= $_taxHelper->__('Regular Price:').''.$_coreHelper->formatPrice($_regularPrice,false).', ';
                        $priceString .= $_taxHelper->__('Special Price:').''.$_taxHelper->__('Excl. Tax:').' '.$_coreHelper->formatPrice($_finalPrice,false).', ';
                        $priceString .= $_taxHelper->__('Incl. Tax:').' '.$_coreHelper->formatPrice($_finalPriceInclTax,false);
                    }
                }elseif($_taxHelper->displayPriceIncludingTax()){
                    if($_finalPrice >= $_price){
                        $priceString .= $_coreHelper->formatPrice($_finalPriceInclTax,false);
                    }else{
                        $priceString .= $_taxHelper->__('Regular Price:').''.$_coreHelper->formatPrice($_regularPrice,false).', ';
                        $priceString .= $_taxHelper->__('Special Price:').' '.$_coreHelper->formatPrice($_finalPriceInclTax,false);
                    }
                }else{
                    if($_finalPrice >= $_price){
                        $priceString .= $_coreHelper->formatPrice($_price,false);
                    }else{
                        $priceString .= $_taxHelper->__('Regular Price:').''.$_coreHelper->formatPrice($_regularPrice,false).', ';
                        $priceString .= $_taxHelper->__('Special Price:').' '.$_coreHelper->formatPrice($_finalPrice,false);
                    }
                }
            }elseif($product->getTypeId() == 'bundle'){
                $_priceModel  = $product->getPriceModel();
                
                if(is_null($_minimalPriceTax) && is_null($_maximalPriceTax)){
                    list($_minimalPriceTax, $_maximalPriceTax) = $_priceModel->getPrices($product);
                }
                if(is_null($_minimalPriceInclTax) && is_null($_maximalPriceInclTax)){
                    list($_minimalPriceInclTax, $_maximalPriceInclTax) = $_priceModel->getPricesDependingOnTax($product, null, true);
                }
                if($_taxHelper->displayBothPrices()){
                    $priceString .= $_taxHelper->__('From').' '.$_taxHelper->__('Excl. Tax:').' '.$_coreHelper->formatPrice($_minimalPriceTax,false).', ';
                    $priceString .= $_taxHelper->__('Incl. Tax:').' '.$_coreHelper->formatPrice($_minimalPriceInclTax,false).', ';
                    $priceString .= $_taxHelper->__('To').' '.$_taxHelper->__('Excl. Tax:').' '.$_coreHelper->formatPrice($_maximalPriceTax,false).', ';
                    $priceString .= $_taxHelper->__('Incl. Tax:').' '.$_coreHelper->formatPrice($_maximalPriceInclTax,false);
                }elseif($_taxHelper->displayPriceIncludingTax()){
                    $priceString .= $_taxHelper->__('From').' '.$_coreHelper->formatPrice($_minimalPriceInclTax,false).', ';
                    $priceString .= $_taxHelper->__('To').' '.$_coreHelper->formatPrice($_maximalPriceInclTax,false);
                }else{
                    $priceString .= $_taxHelper->__('From').' '.$_coreHelper->formatPrice($_minimalPriceTax,false).', ';
                    $priceString .= $_taxHelper->__('To').' '.$_coreHelper->formatPrice($_maximalPriceTax,false);
                }
            }elseif($product->getTypeId() == 'grouped'){
                $_exclTax = $_taxHelper->getPrice($product, $_minimalPriceValue);
                $_inclTax = $_taxHelper->getPrice($product, $_minimalPriceValue, true);
                if($_taxHelper->displayBothPrices()){
                    $priceString .= $_taxHelper->__('Starting at:').' '.$_taxHelper->__('Excl. Tax:').' '.$_coreHelper->formatPrice($_exclTax, false).', ';
                    $priceString .= $_taxHelper->__('Incl. Tax:').' '.$_coreHelper->formatPrice($_inclTax, false);
                }elseif($_taxHelper->displayPriceIncludingTax()){
                    $priceString .= $_taxHelper->__('Starting at:').' '.$_coreHelper->formatPrice($_inclTax, false);
                }else{
                    $priceString .= $_taxHelper->__('Starting at:').' '.$_coreHelper->formatPrice($_exclTax, false);
                }
            }
            return $priceString;
    }
    
    public function parsePriceString($priceString){
        
        $this->_excludeString = 'Excl. Tax:';
    $this->_includeString = 'Incl. Tax:';
    $this->_regularString = 'RegularPrice';
    $this->_specialString = 'SpecialPrice';
    $this->_fromString = 'From';
    $this->_toSting = 'To';
    $this->_starting = 'Starting at:';
        $priceArray = array();
        $priceString = "start-".$priceString."-end";
        if(strpos($priceString,$this->_regularString,0) === FALSE || strpos($priceString,$this->_specialString,0) === FALSE)
        {
            if(strpos($priceString,$this->_excludeString,0) !== FALSE && strpos($priceString,$this->_includeString,0) !== FALSE)
            {
                if(strpos($priceString,$this->_fromString,0) === FALSE && strpos($priceString,$this->_toSting,0) === FALSE)
                {
                    $exclude = array();
                    $include = array();
                    preg_match('/Excl.Tax:(.*)Incl.Tax/',$priceString,$exclude);
                    preg_match('/Incl.Tax:(.*)-end/',$priceString,$include);
                    //$priceArray['price_including_tax'] = $include[1];
                    $priceArray['price_tax'] = "Excl.Tax: ".$exclude[1].", Incl.Tax: ".$include[1];
                }elseif(strpos($priceString,$this->_fromString,0) !== FALSE && strpos($priceString,$this->_toSting,0) !== FALSE)
                {
                    $fromExclude = array();
                    $toExclude = array();
                    
                    $fromInclude = array();
                    $toInclude = array();
                    preg_match('/From:Excl.Tax:(.*)Incl.Tax/',$priceString,$fromExclude);
                    preg_match('/To:Excl.Tax:(.*)Incl.Tax/',$priceString,$toExclude);
                    
                    preg_match('/Incl.Tax(.*)-end/',$priceString,$toInclude);
                    preg_match('/Incl.Tax:(.*)To:/',$priceString,$fromInclude);
                    $priceArray['price_tax'] = "From:Excl.Tax: ".$fromExclude[1]." Incl.Tax: ".$fromInclude[1].", To:Excl.Tax: ".$toExclude[1]." Incl.Tax: ".$toInclude[1];
                    
                }
            }else{
                if(strpos($priceString,$this->_fromString,0) !== FALSE && strpos($priceString,$this->_toSting,0) !== FALSE)
                {
                    $from = array();
                    $to = array();
                    preg_match('/From:(.*)To/',$priceString,$from);
                    preg_match('/To:(.*)-end/',$priceString,$to);
                    $priceArray['price_tax'] = "From: ".$from[1].", To: ".$to[1];
                }elseif(strpos($priceString,$this->_starting,0) !== FALSE){
                    $startingAt = array();
                }
            }
        }else{
            if(strpos($priceString,$this->_excludeString,0) !== FALSE && strpos($priceString,$this->_includeString,0) !== FALSE)
            {
                
                $regular = array();
                $exclude = array();
                $include = array();
                preg_match('/RegularPrice:(.*)SpecialPrice/',$priceString,$regular);
                $priceArray['regular_price'] = "RegularPrice: ".$regular[1];
                preg_match('/SpecialPriceExcl.Tax:(.*)Incl.Tax/',$priceString,$exclude);
                preg_match('/Incl.Tax:(.*)-end/',$priceString,$include);
                $priceArray['special_price'] = "Excl.Tax: ".$exclude[1].", Incl.Tax: ".$include[1];
            }else{
                $regular = array();
                $special = array();
                preg_match('/RegularPrice:(.*)SpecialPrice/',$priceString,$regular);
                $priceArray['regular_price'] = "RegularPrice: ".$regular[1];
                
                preg_match('/SpecialPrice(.*)-end/',$priceString,$special);
                $priceArray['special_price'] = "SpecialPrice: ".$special[1];
            }
        }
        
        return $priceArray;
    }
    
    public function getRootCategoryIds()
    {
        $category = Mage::getModel('catalog/category')->getCollection()
                ->addFieldToFilter('path', array('neq' => '1'))
                ->addLevelFilter(1);
        return $category->getAllIds();
    }
    
    public function getSelectedAttributes()
    {
        $options = explode(',',Mage::getStoreConfig('catalogprint/general/product_attributes'));
        array_unshift($options,'name');
        array_unshift($options,'sku');
        return array_unique($options);
    }
    
    public function getCachedCollectionFile($pathIds,$categoryId,$urlKey,$storeId)
    {
        $targetDir = implode(DIRECTORY_SEPARATOR,$pathIds).''.DS;
        $basePath = Mage::getBaseDir().DS.'var'.DS.'catalog-print'.DS.'store-'.$storeId.DS;
        $fileName = $categoryId.'-'.$urlKey.'.log';
        
        return $basePath.$targetDir.$fileName;
    }
    
    public function isAllowGroups(){
       
        $customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
        $config = null;
        $isAllowed = false;
        $value = array();
        if(strlen(Mage::getStoreConfig("catalogprint/general/enable_customergroups"))){
            $config = (string)Mage::getStoreConfig("catalogprint/general/enable_customergroups");
                $value = explode(",",$config);
        }
        
        if(strlen($config) <= 0){
            $isAllowed = true;
        }elseif(count($value) < 0){
            $isAllowed = true;
        }elseif(in_array($customerGroupId,$value)){
            $isAllowed = true;
        }
        
        return $isAllowed;
    }
    
}
