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

class Ktpl_Catalogprint_Model_Observer {

    public function addUseInCatalogPrintAttribute($observer) {
        $fieldset = $observer->getForm()->getElement('front_fieldset');
        $yesnoSource = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();
        $fieldset->addField('use_in_catalogprint', 'select', array(
            'name' => 'use_in_catalogprint',
            'label' => Mage::helper('eav')->__('Use in Catalog Print'),
            'title' => Mage::helper('eav')->__('Can be used in catalog print attribute configuration?'),
            'note'  => Mage::helper('eav')->__('Can be used in catalog print attribute configuration?'),
            'values' => $yesnoSource,
        ));
    }
    public function emptyDir(Varien_Event_Observer $observer){
        $path = Mage::getBaseDir().DS.'media'.DS.'catalog-print'.DS;
        $d1 = date("Y-m-d H:i:s",strtotime(now()));
        $handel = scandir($path);
        if(is_dir($path) && $handel){
            foreach($handel as $file){
                $fullPath = $path.$file;
                if(is_file($fullPath)){
                    try{
                        $d2 = date("Y-m-d H:i:s",filemtime($fullPath));
                        $diff = strtotime($d1) - strtotime($d2);
                        if($diff >= 28800){
                            unlink($fullPath); 
                        }
                    } catch (Exception $e) {
                        Mage::log($e->getMessage(),false,"catalog-print.log");
                    }
                }
            }
       }
    }
    
    public function injectTabs(Varien_Event_Observer $observer)
    {
        if(Mage::getStoreConfig('catalogprint/general/enable_backend') == '1'){
            $block = $observer->getEvent()->getBlock();
            if($block instanceof Mage_Adminhtml_Block_Catalog_Category_Tabs){
                $categoryId = (int) Mage::app()->getRequest()->getParam('id',false);

                if($categoryId){
                    $block->addTab('catalogprint',array(
                        'label'=>Mage::helper('catalogprint')->__('PDF Catalog Print'),
                        'content'=> $block->getLayout()->createBlock('catalogprint/adminhtml_category_tab_printoptions')->toHtml()
                    ));
                }
            }
        }
    }
        
    public function saveCollectionInCache(Varien_Event_Observer $observer)
    {
        $attributes = Mage::helper('catalogprint')->getSelectedAttributes();
        $category = $observer->getEvent()->getCategory();
        
        $products = $category->getProductCollection()
                                    ->addAttributeToSelect('*')
                                    ->addTaxPercents()
                                    ->addFieldToFilter('visibility',
                Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
                                    ->addAttributeToFilter('visiblein_catalogprint', 1);
        $dir = implode(DIRECTORY_SEPARATOR,$category->getPathIds()).''.DS;
        $targetDir = Mage::getBaseDir().DS.'var'.DS.'catalog-print'.DS;
       
        if(!is_dir($targetDir.$dir)){
           mkdir($targetDir.$dir, 0777,true);
        }
        $varienData = new Varien_Data_Collection();
        foreach($products->getItems() as $product)
        {
            $varienData->addItem($product);
        }
        $serializedData = serialize($varienData);
        file_put_contents($targetDir.$dir.''.$category->getId().'-'.$category->getUrlKey().'.log', $serializedData);
        return $this;
    }
    
    public function removeCollectionInCache(Varien_Event_Observer $observer)
    {
        return $this;
    }
}
