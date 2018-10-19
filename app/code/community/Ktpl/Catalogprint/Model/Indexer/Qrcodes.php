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
class Ktpl_Catalogprint_Model_Indexer_Qrcodes extends Mage_Index_Model_Indexer_Abstract
{
    const EVENT_MATCH_RESULT_KEY = 'catalogprint_qrcodes_match_result';
    
    protected $_matchedEntities = array(
        Mage_Catalog_Model_Product::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE,
            Mage_Index_Model_Event::TYPE_MASS_ACTION,
            Mage_Index_Model_Event::TYPE_DELETE
        ),
    );
    
    protected function _getProductAttributesDependOn()
    {
        return array(
            'visiblein_catalogprint',
        );
    }
    
    /**
     * Retrieve Fulltext Search instance
     *
     * @return Mage_CatalogSearch_Model_Fulltext
     */
    protected function _getIndexer()
    {
        return Mage::getSingleton('catalogprint/qrcodes');
    }
    
    protected function _getResource()
    {
        return Mage::getResourceSingleton('catalogprint/indexer_qrcodes');
    }
    
    public function getName()
    {
        return Mage::helper('catalogprint')->__('Product QR Codes');
    }
    public function getDescription()
    {
        return Mage::helper('catalogprint')->__('Generate QR Codes for each products');
    }
    
    public function matchEvent(Mage_Index_Model_Event $event) {
        $data       = $event->getNewData();
        if (isset($data[self::EVENT_MATCH_RESULT_KEY])) {
            return $data[self::EVENT_MATCH_RESULT_KEY];
        }
        
        $entity = $event->getEntity();
        $result = parent::matchEvent($event);
        $event->addNewData(self::EVENT_MATCH_RESULT_KEY, $result);
        return $result;
    }
    
    protected function _processEvent(Mage_Index_Model_Event $event) {
        $data = $event->getNewData();
    }
    
    protected function _registerEvent(Mage_Index_Model_Event $event) {
        if ($event->getEntity() == Mage_Catalog_Model_Product::ENTITY) {
            $this->_registerCatalogProduct($event);
        }
    }
    
    protected function _registerCatalogProduct(Mage_Index_Model_Event $event)
    {
        switch ($event->getType()) {
            case Mage_Index_Model_Event::TYPE_SAVE:
                $this->_registerCatalogProductSaveEvent($event);
                break;

            case Mage_Index_Model_Event::TYPE_DELETE:
                $this->_registerCatalogProductDeleteEvent($event);
                break;

            case Mage_Index_Model_Event::TYPE_MASS_ACTION:
                $this->_registerCatalogProductMassActionEvent($event);
                break;
        }
    }
    
    protected function _registerCatalogProductSaveEvent(Mage_Index_Model_Event $event)
    {
        $product = $event->getDataObject();
        $reindexTag = $product->getForceReindexRequired();
        foreach ($this->_getProductAttributesDependOn() as $attributeCode) {
            $reindexTag = $reindexTag || $product->dataHasChangedFor($attributeCode);
        }
        if (!$product->isObjectNew() && $reindexTag) {
            $dir = Mage::getBaseDir().DS."media".DS.'catalog-print'.DS.'qrcodes'.DS;
            $qrCode = new Qrcode_Image(array('level'=>'L','size'=>1));
            if(is_file($dir.$product->getId().'.png')){
                 unlink($dir.$product->getId().'.png');
            }
            $url = $product->getUrlPath();
                        $qrCode->setData(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).$url);
                        $qrCode->setBaseDir($dir);
                        $qrCode->setFileName($product->getId().'.png');
                        $qrCode->generatePngImage();
            //$event->addNewData('qrcodes_reindex_required', true);
        }
    }
    
    protected function _registerCatalogProductDeleteEvent(Mage_Index_Model_Event $event)
    {
        $pk = $event->getEntityPk();
        $dir = Mage::getBaseDir().DS."media".DS.'catalog-print'.DS.'qrcodes'.DS;
        if(is_file($dir.$pk.'.png'))
        {
            unlink($dir.$pk.'.png');
        }
    }
    
    protected function _registerCatalogProductMassActionEvent(Mage_Index_Model_Event $event)
    {
        $actionObject = $event->getDataObject();
        $attributes   = $this->_getProductAttributesDependOn();
        $reindexTags  = false;
        
        $attrData = $actionObject->getAttributesData();
        
        if (is_array($attrData)) {
            foreach ($attributes as $attributeCode) {
                if (array_key_exists($attributeCode, $attrData)) {
                    $reindexTags = true;
                    break;
                }
            }
        }
        
        if ($actionObject->getWebsiteIds()) {
            $reindexTags = true;
        }
        
        if ($reindexTags) {
            $productIds = $actionObject->getProductIds();
            $dir = Mage::getBaseDir().DS."media".DS.'catalog-print'.DS.'qrcodes'.DS;
            if ($productIds) {
                if($attrData['visiblein_catalogprint'] == 0){
                    foreach($productIds as $id){
                        if(is_file($dir.$id.'.png')){
                            unlink($dir.$id.'.png');
                        }
                    }
                }else{
                    $qrCode = new Qrcode_Image(array('level'=>'L','size'=>1));
                    foreach($productIds as $id){
                        if(is_file($dir.$id.'.png')){
                            unlink($dir.$id.'.png');
                        }
                        $url = Mage::getModel('catalog/product')->load($id)->getUrlPath();
                        $qrCode->setData(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).$url);
                        $qrCode->setBaseDir($dir);
                        $qrCode->setFileName($id.'.png');
                        $qrCode->generatePngImage();
                    }
                }
                //$event->addNewData('qrcodes_reindex_product_ids', $productIds);
            }
        }
    }
    
    public function reindexAll($ids = null)
    {
        $dir = Mage::getBaseDir().DS."media".DS.'catalog-print'.DS.'qrcodes'.DS;
        if(!is_dir($dir)){
            mkdir($dir, 0777, true);
        }
        try {
            $collection = Mage::getModel('catalog/product')->getCollection()
                                ->addAttributeToSelect('entity_id')
                                ->addAttributeToSelect('url_path')
                                ->addUrlRewrite()
                                ->addAttributeToFilter('visiblein_catalogprint',array('eq'=>1))
                                ->load();
            
            foreach($collection as $product)
            {
                $qrCode = new Qrcode_Image(array('level'=>'L','size'=>1));
                $qrCode->setData(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).$product->getUrlPath());
                $qrCode->setBaseDir($dir);
                $qrCode->setFileName($product->getId().'.png');
                $qrCode->generatePngImage();
               
            }
            
        }catch(Exception $e){
          throw $e;  
        }
    }
}

