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
class Ktpl_Catalogprint_IndexController extends Mage_Core_Controller_Front_Action
{
    protected $_collection = array();
    protected $_categoryModel = null;
    protected $_flag = 0;
    protected $_fileName = null;
    protected $_store = null;
    protected $_customerGroup = null;

    public function generatePdfAction()
    {
                
		$params = $this->getRequest()->getParams();
                $rootIds = Mage::helper('catalogprint')->getRootCategoryIds();
                if(array_key_exists('customer_group_id',$params)){
                    $this->_customerGroup = $params['customer_group_id'];
                }
		unset($params['category']);
		unset($params['id']);
		$category_id = $this->getRequest()->getParam('category');
        $uniqid = $this->getRequest()->getParam('id');
        if (isset($category_id) && !empty($category_id)) {
            if(!in_array($category_id,$rootIds) && !is_array($category_id))
                {
                $category = Mage::getModel('catalog/category')->setStoreId($params['store'])->load($category_id);

                Mage::register('current_category', $category);

                $collection = array();

                if ($category->getId()) {

                        $this->prepareCategoryProductCollection($category, $params['store']);
                        
                }
            }elseif(is_array($category_id)){
                $this->_collection['id'] = $rootIds[0];
                $this->_collection['name'] = Mage::helper('catalogprint')->__('PDF Catalog Print');
                $this->_collection['level'] = '1';
                foreach($category_id as $id){
                    $category = Mage::getModel('catalog/category')->setStoreId($params['store'])->load($id);
                    $this->_collection['child'][] = $this->prepareChoosenCategories($category, $params['store']);
                }
            }else{
                    $this->_collection = $this->prepareRootCategoryProductCollection($category_id, $params['store']);
                }
           try{
                ini_set('memory_limit', '2048M');
               //ini_set('memory_limit', -1);
            } catch(Exception $e) {}
            $this->_collection['uniqid'] = $uniqid;
            if(is_array($category_id)){
                $this->_collection['current_cat_id'] = $rootIds[0];
            }else{
                $this->_collection['current_cat_id'] = $category_id;
            }
            
			foreach($params as $key=>$value)
			{
				$this->_collection[$key] = $value;
			}
            if(class_exists('Mage_Core_Model_App_Emulation')){      
                $appEmulation = Mage::getSingleton('core/app_emulation');
                $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($params['store']);
                $pdf = Mage::getModel('catalogprint/catalog_pdf')->getPdf($this->_collection);
                $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
            }else{
                $storeCode = Mage::getModel('core/store')->load($params['store'])->getCode();
                Mage::app($storeCode);
                $pdf = Mage::getModel('catalogprint/catalog_pdf')->getPdf($this->_collection);
            }
            
        } else {
            throw new Exception('category is not defined.');
        }
    }
    
        public function prepareChoosenCategories($category, $storeId){
            $data = array();
                $data['id'] = $category->getId();
                $data['name'] = $category->getName();
                $data['level'] = '2';
                $path = Mage::helper('catalogprint')->getCachedCollectionFile($category->getPathIds(), $category->getId(), $category->getUrlKey(), $storeId);
                if(is_file($path) && Mage::getStoreConfig('catalogprint/indexer_group/enable_cache')){
                $data['child'] = $path;
            }else{
                $data['child'] = $category->getProductCollection()
                                    ->addAttributeToSelect('*')
                                    ->addMinimalPrice()
                                    ->addFinalPrice()
                                    ->addTaxPercents()
                                    ->addFieldToFilter('visibility',
                Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
                                    ->addAttributeToFilter('visiblein_catalogprint', 1)
                                    ->load();
            }
            return $data;
        }
	public function getQueryString($posts)
	{
		$qstr = '';
                $posts['store'] = (!array_key_exists('store', $posts)) ? Mage::app()->getStore()->getId() : $posts['store'];
                $posts['store_code'] = !array_key_exists('store_code', $posts) ? Mage::app()->getStore()->getCode() : $posts['store_code'];
		$posts['website_id'] = Mage::getModel('core/store')->load($posts['store'])->getWebsite()->getId();
                $count = count($posts);
		$index = 1;
		
		foreach($posts as $key=>$value)
		{
                        if($key == 'category' && strstr($value,",") !== false){
                            $cIndex = 1;
                            $array = array_unique(explode(",",$value));
                            foreach($array as $categoryId){
                                if($categoryId != ''){
                                    $qstr .= $key."[]=".$categoryId;
                                    if($cIndex < count($array) - 1){
                                        $qstr .= '\&';
                                        $cIndex++;
                                    }
                                }
                            }
                        }else{
                            $qstr .= $key."=".$value;
                        }
			if($index < $count)
			{
				$qstr .= '\&';
				$index++;
			}
		}
		return $qstr;
	}
    
    public function downloadAction()
    {
        $file = $this->getRequest()->getParam('file');
        if(isset($file) && !empty($file)) {
            $explodedPart = explode("-",$file);
            $catName = Mage::getModel('catalog/category')->load($explodedPart[0]);
            $fileName = str_replace(array(' ','\n','\r','&','\\','/',':','"','*','?','|','<','>'),'_',$catName->getName()).'.pdf';
            $rFileName = preg_replace('/[_]{2,}/','_',$fileName);
            $pdf = Zend_Pdf::load(Mage::getBaseDir('media') . DS . 'catalog-print' . DS . $file);
            $this->_prepareDownloadResponse($rFileName,  str_replace('/Annot /Subtype /Link', '/Annot /Subtype /Link /Border[0 0 0]', $pdf->render()), 'application/pdf');
        }
    }

    /**
     * Get catalog layer model
     *
     * @return Mage_Catalog_Model_Layer
     */
    public function getLayer()
    {
        $layer = Mage::registry('current_layer');
        if ($layer) {
            return $layer;
        }
        return Mage::getSingleton('catalog/layer');
    }

    /**
     * Prepare category product collection
     */
    public function prepareCategoryProductCollection($category, $storeId = 0) {

        //first of all check its level
        //if its level is not 2 then get its all parent categories upto level 2
        //here in magento level 1 is root category

        $this->_collection = $this->prepareParents($category, $storeId);

    }
    
    public function prepareRootCategoryProductCollection($categoryId, $storeId)
    {
        
        return $this->getTreeCategories($categoryId, false, $storeId);
        
    }
    
    public function getTreeCategories($parentId, $isChild, $storeId)
    {
        $array = array();
        if(!$isChild){
            $array['id'] = $parentId;
             $array['name'] = Mage::getModel('catalog/category')->setStoreId($storeId)->load($parentId)->getName();
              $array['level'] = 0;
              $array['is_root'] = 1;
        }
        
        $allCats = Mage::getModel('catalog/category')->getCollection()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('parent_id',array('eq' => $parentId));
        foreach ($allCats as $category) 
        {
            $loadCat = Mage::getModel('catalog/category')->setStoreId($storeId)->load($category->getId());
            if($loadCat->getIsActive()){
                $array['child'][] = $this->prepareParents($loadCat, $storeId);
            }
        }
        return $array;
    }

    public function prepareParents($category, $storeId) {
        $parentIds = $category->getParentIds();
        
        if(isset($parentIds[2]))
            return $this->_prepareParentsRecursive($parentIds[2], $parentIds, $category, $storeId);
        else
            return $this->_prepareParentsRecursive($category->getId(), $parentIds, $category, $storeId);
    }

    public function _prepareParentsRecursive($currentId, $ids, $category, $storeId) {
        
        $cat = $this->getCategoryModel()->setStoreId($storeId)->load($currentId);

        $result = array();
        
        $array['id'] = $cat->getId();
        $array['name'] = $cat->getName();
        $array['level'] = $cat->getLevel()-1;
        $array['url'] = $cat->getUrl();
        
        if($cat->hasChildren()) {
            foreach($cat->getChildrenCategories() as $c) {
                if(in_array($c->getId(), $ids)){
                    if($c->getIsActive()){
                        $array['child'][] = $this->_prepareParentsRecursive($c->getId(), $ids, $category, $storeId);
                    }
                    
                }
            }
            
        }

        if($category->getLevel()=='2') {
            
            $array = $this->prepareChilds($category, $storeId);
        } else {
            if($cat->getId() == end($ids) && $this->_flag == 0) {
                
                $array['child'][] = $this->prepareChilds($category, $storeId);
                $this->_flag = 1;
            }
        }
        
        $result = $array;
        return $result;
    }

    protected function getCategoryModel() {
        if($this->_categoryModel) {
            return $this->_categoryModel;
        }
        $this->_categoryModel = Mage::getModel('catalog/category');
        return $this->_categoryModel;
    }

    public function prepareChilds($category, $storeId) {
        return $this->_prepareChildsRecursive($category, $storeId);
    }

    public function _prepareChildsRecursive($cat, $storeId) {
        $result = array();
        $array['id'] = $cat->getId();
        $array['name'] = $cat->getName();
        $array['level'] = $cat->getLevel()-1;
        $array['url'] = $cat->getUrl();
        
        
        if($cat->hasChildren()) {
            if(Mage::getStoreConfig('catalogprint/general/products_display', $storeId) == 'both'){
            $path = Mage::helper('catalogprint')->getCachedCollectionFile($cat->getPathIds(), $cat->getId(), $cat->getUrlKey(), $storeId);
    
            if(is_file($path) && Mage::getStoreConfig('catalogprint/indexer_group/enable_cache')){
                $tmp_collection = $path;
            }else{
                $tmp_collection = $cat->getProductCollection()
                        ->addAttributeToSelect('*')
                        ->addMinimalPrice()
                        ->addFinalPrice()
                        ->addTaxPercents()
                        ->addAttributeToFilter('visiblein_catalogprint', 1);
                Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($tmp_collection);
                Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($tmp_collection);
                /*try {
                    $tmp_collection->getSelect()->where('cat_index.is_parent = 1');
                } catch (Exception $e) {
                    Mage::log($e->getTraceAsString(), null, 'query.log');
                    return;
                }*/
            }
            
                $array[$cat->getId()]['products']['child'] = $tmp_collection;
                $array[$cat->getId()]['products']['name'] = $cat->getName();
                $array[$cat->getId()]['products']['id'] = $cat->getId();
                $array[$cat->getId()]['products']['level'] = $cat->getLevel() - 1;
        }
            foreach($cat->getChildrenCategories() as $c) {
                $loadedC = Mage::getModel('catalog/category')->setStoreId($storeId)->load($c->getId());
                    $array['child'][] = $this->_prepareChildsRecursive($loadedC, $storeId);
            }
        } else { 
            $path = Mage::helper('catalogprint')->getCachedCollectionFile($cat->getPathIds(), $cat->getId(), $cat->getUrlKey(), $storeId);
            if(is_file($path) && Mage::getStoreConfig('catalogprint/indexer_group/enable_cache')){
                $array['child'] = $path;
            }else{
                $main_collection = $cat->getProductCollection()
                                    ->addAttributeToSelect('*')
                                    ->addMinimalPrice()
                                    ->addFinalPrice()
                                    ->addTaxPercents()
                                    ->addAttributeToFilter('visiblein_catalogprint', 1);
                Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($main_collection);
                Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($main_collection);
                $array['child'] = $main_collection;
            }
            
            
        }

        $result = $array;
        return $result;
    }

    public function execInBackground($cmd, $url) {
		$ch = curl_init($url);

	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_TIMEOUT_MS, 1000);
        try{
		curl_exec($ch);
				
		curl_close($ch);
        }catch(Exception $e){
            //Mage::getSingleton("core/session")->addError($e->getMessage());
            Mage::register('pdf_generate_url', str_replace("\\","",$url));
	}
    }

    public function indexAction() {
                
		$posts = $this->getRequest()->getParams();
		$id = uniqid();
		$Qstr = $this->getQueryString($posts);	
        Mage::getModel('core/session')->setData('uniqid', $id);        
        $url = Mage::getUrl('catalog-print/index/generatePdf',array('_secure' => Mage::app()->getFrontController()->getRequest()->isSecure())).'?category='.$this->getRequest()->getParam('category').'\&id='. $id .'\&'.$Qstr;               
		$this->execInBackground('wget --spider '.$url, str_replace("\\","",$url));
		//Mage::register('pdf_generate_url', str_replace("\\","",$url));
		//$this->_redirect($url);
        $this->loadLayout();
		$this->getLayout()->getBlock('head')->setTitle('Print Catalog');
        $this->renderLayout();
    }

    public function getProgressAction() {
        $category_id = $this->getRequest()->getParam('category');
        if(strchr($category_id,",") != false){
            $rootIds = Mage::helper('catalogprint')->getRootCategoryIds();
            $category_id = $rootIds[0];
        }
        $uniqid = Mage::getModel('core/session')->getData('uniqid');
        
        $myFile = Mage::getBaseDir('media') . DS . 'catalog-print' . DS . $category_id . '-progress-' . $uniqid . '.txt';
        
        $fh = fopen($myFile, 'r');
        $data = fread($fh, filesize($myFile));
        fclose($fh);
        if($data == '100') {
            unlink($myFile);
            echo Mage::getUrl('catalog-print/index/download',array('_secure' => Mage::app()->getFrontController()->getRequest()->isSecure())). '?file=' .$category_id . '-' . $uniqid . '.pdf';
            } else {        
            echo $data;
        }  
	Mage::dispatchEvent("catalogprint_download_send",array());     
        return;
    }
    
    /* override from Mage_Core_Controller_Front_Action. This function  does not exist in 1.9 enterprise at Mage_Core_Controller_Front_Action thats why add here for work in all versions. */
    protected function _prepareDownloadResponse($fileName, $content, $contentType = 'application/octet-stream',$contentLength = null) {
        $session = Mage::getSingleton('admin/session');
        if ($session->isFirstPageAfterLogin()) {
            $this->_redirect($session->getUser()->getStartupPageUrl());
            return $this;
        }

        $isFile = false;
        $file   = null;
        if (is_array($content)) {
            if (!isset($content['type']) || !isset($content['value'])) {
                return $this;
            }
            if ($content['type'] == 'filename') {
                $isFile         = true;
                $file           = $content['value'];
                $contentLength  = filesize($file);
            }
        }

        $this->getResponse()
            ->setHttpResponseCode(200)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
            ->setHeader('Content-type', $contentType, true)
            ->setHeader('Content-Length', is_null($contentLength) ? strlen($content) : $contentLength)
            ->setHeader('Content-Disposition', 'attachment; filename="'.$fileName.'"')
            ->setHeader('Last-Modified', date('r'));

        if (!is_null($content)) {
            if ($isFile) {
                $this->getResponse()->clearBody();
                $this->getResponse()->sendHeaders();

                $ioAdapter = new Varien_Io_File();
                if (!$ioAdapter->fileExists($file)) {
                    Mage::throwException(Mage::helper('core')->__('File not found'));
                }
                $ioAdapter->open(array('path' => $ioAdapter->dirname($file)));
                $ioAdapter->streamOpen($file, 'r');
                while ($buffer = $ioAdapter->streamRead()) {
                    print $buffer;
                }
                $ioAdapter->streamClose();
                if (!empty($content['rm'])) {
                    $ioAdapter->rm($file);
                }

                exit(0);
            } else {
                $this->getResponse()->setBody($content);
            }
        }
        return $this;
    }
}
