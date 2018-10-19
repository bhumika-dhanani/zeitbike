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
class Ktpl_Catalogprint_Adminhtml_Catalogprint_IndexController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
    }
    
    public function categoriesJsonAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('catalogprint/adminhtml_categories')
                ->getCategoryChildrenJson($this->getRequest()->getParam('category'))
        );
    }
    public function indexCategoryAction()
    {
        $storeIds = Mage::getModel('core/store')->getCollection()
                                ->addFieldToFilter('is_active',array('eq'=>1))
                                ->addFieldToFilter('group_id',array('gt'=>0))->getAllIds();
        foreach($storeIds as $storeId){
            $categories = Mage::getModel('catalog/category')->setStoreId($storeId)->getCollection();
            $targetDir = Mage::getBaseDir().DS.'var'.DS.'catalog-print'.DS.'store-'.$storeId.DS;
            foreach($categories as $categoryunLoad)
            {
                $category = Mage::getModel('catalog/category')->setStoreId($storeId)->load($categoryunLoad->getId());
                $dir = implode(DIRECTORY_SEPARATOR,$category->getPathIds()).''.DS;
                $products = $category->getProductCollection()
                                        ->addAttributeToSelect('*')
                                        ->addTaxPercents()
                                        ->addFieldToFilter('visibility',
                    Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
                                        ->addAttributeToFilter('visiblein_catalogprint', 1);

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
            }
        }
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('catalogprint')->__('Categories Indexed Successfully.'));
        $this->_redirectReferer();
        return $this;
    }
    
    public function indexQrCodesAction()
    {
        Mage::getModel('catalogprint/indexer_qrcodes')->reindexAll();
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('catalogprint')->__('QR Codes Indexed Successfully.'));
        $this->_redirectReferer();
        return $this;
    }
}
