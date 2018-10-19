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
class Ktpl_Catalogprint_Model_Catalog_Pdf extends Mage_Sales_Model_Order_Pdf_Abstract {

    //config vars
    protected $marginLeft = 20;
    protected $attributeValueFontSize = 7;
    protected $attributeHeadingFontSize = 8;
    protected $marginBottom = 10;
    protected $marginTop = 10;
    protected $fontSize = 11;
    protected $skuFontSize = 8;
    protected $attributeFontSize = 10;
    protected $fontStyle = 'regular';
    protected $mainCatHeaderfontSize = 27;
    protected $secondCatHeaderfontSize = 27;
    protected $websiteNameFontSize = 17;
    protected $page_header_category_firstline_font_size = 11;
    protected $page_header_category_secondline_font_size = 16;
    protected $page_header_category_thirdline_font_size = 16;
    protected $headerfontSize = 20;
    protected $sub_cat_header_bottom_margin = 20; //bottom-margin from sub-category header
    protected $whole_grid_bottom_margin = 30; //bottom-margin from sub-category header
    protected $grid_bottom_margin = 25; //bottom-margin from sub-category header
    protected $grid_total_height = 205;
    protected $product_grid_total_height = 367; //205 new 237
    protected $product_grid_total_width = 185; //136 old 135
    protected $product_grid_top_padding = 5; //5 old
    protected $product_grid_left_padding = 5; //5 old
    protected $product_grid_bottom_margin = 5;
    protected $gridProductImageSize = 175; //125old
    protected $listProductImageSize = 105; //125old
    protected $pageNumberFontSize = 10;
    protected $pageDateFontSize = 8;
    protected $pageFooterFontSize = 8;
    protected $indexFontSize = 10;
    protected $indexVerticalSpacing = 2; //should be given in backend
    protected $list_total_height = 150;
    protected $list_total_width = 530;
    protected $collection = array();
    protected $flag = null;
    protected $_memoryManager = null;
    protected $_totalProducts = 0;
    protected $k = 0;
    protected $_logFile;
    protected $_pdfFile;
    protected $pageNumber = 1;
    protected $tmpArray = array();
    protected $manufacturerNumbers = array();
    protected $startTableOfContentsIndex = 0;
    protected $endTableOfContentsIndex = 0;
    protected $tmpTableOfContentsFlag = 0;
    
    protected $labelFontStyle = 'bold';
    protected $valueFontStyle = 'regular';
    protected $titleFontStyle = 'regular';
    protected $labelFontColor = '#000000';
    protected $valueFontColor = '#000000';
    protected $titleFontColor = '#006137';
    protected $displayDate = 1;
    
    protected $gridBorder = 1;
    protected $gridBorderColor = '#000000';
    protected $listBorderColor = '#eadc07';
    protected $gridDisplayReadMore = 1;
    protected $listDisplayReadMore = 1;
    
    protected $footerText = '';
    protected $footerLines = array();
    protected $footerSteps = 0;
    
    protected $coverpageFontColor = '#000000';
    
    protected $selectedProductCounts = 6;
    protected $selectedLayout = array();
    protected $perPageSettings = array(
                                    '4'=>array('height'=>367,'width'=>278,'img_size'=>268,'padding'=>100),
                                    '6'=>array('height'=>367,'width'=>185,'img_size'=>175,'padding'=>0),
                                    '9'=>array('height'=>237,'width'=>185,'img_size'=>155,'padding'=>0),
                                    '12'=>array('height'=>237,'width'=>135,'img_size'=>125,'padding'=>0)
                                );
    protected $isRoot = false;
    protected $storeId = 0;
    protected $customerGroups = null;
    protected $websiteId = null;
    
    public function _construct()
    {
        parent::_construct();
        $errorMsg = Mage::helper('catalogprint/util')->checkModuleActivation();
        if(is_array($errorMsg) && count($errorMsg) > 0){
            $config = new Mage_Core_Model_Config();
            $config->saveConfig('catalogprint/general/enable_frontend','0');
            $config->saveConfig('catalogprint/general/enable_backend','0');
        }
    }

    /**
     * Return PDF document
     *
     * @param  array $collection
     * @return Zend_Pdf
     */
    public function getPdf($collection = array()) {
        
        try {
            $this->storeId = $collection['store'];
            $this->isRoot = (isset($collection['is_root']) && $collection['is_root'] == 1) ? true: false;
            $this->labelFontStyle = Mage::getStoreConfig('catalogprint/fonts/label_font');
            $this->valueFontStyle = Mage::getStoreConfig('catalogprint/fonts/description_font');
            $this->titleFontStyle = Mage::getStoreConfig('catalogprint/fonts/title_font');
            $this->footerText = Mage::getStoreConfig('catalogprint/general/pdf_footer_text');
            $this->displayDate = Mage::getStoreConfig('catalogprint/general/enable_date');
            if(Mage::getStoreConfig('catalogprint/general/coverpage_font_color') != ''){
            $this->coverpageFontColor = '#'.str_replace('#','',Mage::getStoreConfig('catalogprint/general/coverpage_font_color'));
            }
            $this->collection = $collection;
            
            if(array_key_exists('layout', $this->collection) && $this->collection['layout'] == 'grid'){
                $this->gridDisplayReadMore = Mage::getStoreConfig('catalogprint/product_grid/enable_readmore');
                if(Mage::getStoreConfig('catalogprint/product_grid/label_color') != ''){
                $this->labelFontColor = '#'.str_replace('#','',Mage::getStoreConfig('catalogprint/product_grid/label_color'));
                }
                if(Mage::getStoreConfig('catalogprint/product_grid/value_color') != ''){
                $this->valueFontColor = '#'.str_replace('#','',Mage::getStoreConfig('catalogprint/product_grid/value_color'));
                }
                if(Mage::getStoreConfig('catalogprint/product_grid/title_color') != ''){
                $this->titleFontColor = '#'.str_replace('#','',Mage::getStoreConfig('catalogprint/product_grid/title_color'));
                }
                
                $this->gridBorder = (int)Mage::getStoreConfig('catalogprint/product_grid/border_option');
                if(Mage::getStoreConfig('catalogprint/product_grid/border_color') != ''){
                $this->gridBorderColor = '#'.str_replace('#','',Mage::getStoreConfig('catalogprint/product_grid/border_color'));
                }
                if(Mage::getStoreConfig('catalogprint/product_grid/product_count')){
                    $this->selectedProductCounts = Mage::getStoreConfig('catalogprint/product_grid/product_count');
                }
                $this->selectedLayout = $this->perPageSettings[$this->selectedProductCounts];
            }elseif(array_key_exists('layout', $this->collection) && $this->collection['layout'] == 'list'){
                $this->listDisplayReadMore = Mage::getStoreConfig('catalogprint/product_list/enable_readmore');
                if(Mage::getStoreConfig('catalogprint/product_list/label_color') != ''){
                $this->labelFontColor = '#'.str_replace('#','',Mage::getStoreConfig('catalogprint/product_list/label_color'));
                }
                if(Mage::getStoreConfig('catalogprint/product_list/value_color') != ''){
                $this->valueFontColor = '#'.str_replace('#','',Mage::getStoreConfig('catalogprint/product_list/value_color'));
                }
                if(Mage::getStoreConfig('catalogprint/product_list/title_color') != ''){
                $this->titleFontColor = '#'.str_replace('#','',Mage::getStoreConfig('catalogprint/product_list/title_color'));
                }
                if(Mage::getStoreConfig('catalogprint/product_list/border_color') != ''){
                $this->listBorderColor = '#'.str_replace('#','',Mage::getStoreConfig('catalogprint/product_list/border_color'));
                }
            }
            $this->setFileNames($collection['current_cat_id'], $collection['uniqid']);

            $pdf = new Zend_Pdf();
            $memoryManager = $this->getMemoryManager();
            $pdf->setMemoryManager($memoryManager);

            $this->_setPdf($pdf);
            if(array_key_exists('customer_group_id', $this->collection)){
                $this->customerGroups = $this->collection['customer_group_id'];
            }
            if(array_key_exists('website_id', $this->collection)){
                $this->websiteId = $this->collection['website_id'];
            }
            $page = $this->newPage(array('displayHeaderFooter'=>false,'displayPageNumber'=>false));
            $this->insertCover($page);
            $cover = Zend_Pdf_Destination_Zoom::create($page);
            $pdf->setNamedDestination('cover', $cover);

            $font = $this->_setFontRegular($page, $this->attributeValueFontSize);        
            $this->footerLines = explode("\n", $this->getWrappedText($this->footerText, $font, $this->attributeValueFontSize, $page->getWidth() - ($this->marginLeft * 5)));
            $this->footerSteps = count($this->footerLines) > 1 ? count($this->footerLines) * 2: 0;
            $this->countTotalProducts($collection);
            
            $this->drawProducts($collection);

            $this->drawIndexes();
            if (array_key_exists('contents', $this->collection)) {
                $this->drawTableOfContents($collection);
                //set order of table of contents
                $this->sortTableOfContents();
            } else {
                if (!array_key_exists('cover', $this->collection)) {
                    unset($this->_getPdf()->pages[0]);
                }
            }

            $this->_getPdf()->save($this->_pdfFile);

            //set 100% progress
            file_put_contents($this->_logFile, '100');
        } catch (Exception $e) {
            var_dump($e);
            return;
        }
        return;
    }

    protected function sortTableOfContents() {
        $len = $this->endTableOfContentsIndex - $this->startTableOfContentsIndex + 1;
        $t = $this->_getPdf()->pages;
        for ($i = 1; $i < sizeof($t); $i++) {
            $this->_getPdf()->pages[$i + $len] = $t[$i];
        }
        $t = $this->_getPdf()->pages;
        for ($i = $this->startTableOfContentsIndex + $len - 1, $k = 1; $i < sizeof($t); $i++, $k++) {
            $this->_getPdf()->pages[$k] = $t[$i];
            unset($this->_getPdf()->pages[$i]);
        }
        if (!array_key_exists('cover', $this->collection)) {
            unset($this->_getPdf()->pages[0]);
        }
    }

    protected function setFileNames($catId, $uniqid) {
        $folder = Mage::getBaseDir('media') . DS . 'catalog-print';
        $io = new Varien_Io_File();
        $this->_pdfFile = $folder . DS . $catId . '-' . $uniqid . '.pdf';
        $this->_logFile = $folder . DS . $catId . '-progress-' . $uniqid . '.txt';

        if (!opendir($folder)) {
            $io->mkdir($folder, 0777, true);
        }
    }

    /**
     * Create new page and assign to PDF object
     *
     * @param  array $settings
     * @return Zend_Pdf_Page
     */
    public function newPage(array $args = array('displayHeaderFooter' => true,'displayPageNumber'=>true)) {
        /* Add new table head */
        $page = $this->_getPdf()->newPage(Zend_Pdf_Page::SIZE_A4);

        $this->_getPdf()->pages[] = $page;
        $this->y = 800;
        if ($args['displayHeaderFooter']) {
            $this->_drawPageHeader($page, $args['displayPageNumber']);
            $this->_drawPageFooter($page);
        }
        return $page;
    }

    protected function getWrappedText($string, Zend_Pdf_Resource_Font $font, $fontsize, $max_width) {
        $wrappedText = '';
        $lines = explode("\n", $string);
        foreach ($lines as $i => $line) {
            $words = explode(' ', $line);
            $word_count = count($words);
            $i = 0;
            $wrappedLine = '';
            while ($i < $word_count) {
                /* if adding a new word isn't wider than $max_width,
                  we add the word */
                if ($this->widthForStringUsingFontSize($wrappedLine . ' ' . $words[$i]
                                , $font
                                , $fontsize) < $max_width
                ) {
                    if (!empty($wrappedLine)) {
                        $wrappedLine .= ' ';
                    }
                    $wrappedLine .= $words[$i];
                } else {
                    $wrappedText .= $wrappedLine . "\n";
                    $wrappedLine = $words[$i];
                }
                $i++;
            }
            $wrappedText .= $wrappedLine . "\n";
        }
        return $wrappedText;
    }

    /**
     * Insert Cover to pdf page
     *
     * @param Zend_Pdf_Page $page
     * @param null $store
     */
    protected function insertCover(&$page, $store = null) {
        //insert background image 595x842
        $image = Mage::getBaseDir('media') . DS . 'theme' . DS .
                Mage::getStoreConfig('catalogprint/general/coverbackground');
        //Added $displaycovertitle to display/hide cover page title
        $displaycovertitle = Mage::getStoreConfig('catalogprint/general/covertitle');

        if ($image) {
            if (is_file($image)) {
                $image = Zend_Pdf_Image::imageWithPath($image);
                $top = 830; //top border of the page
                $width = $image->getPixelWidth();
                $height = $image->getPixelHeight();

                $y2 = 842;
                $y1 = $y2 - $height;
                $x1 = 0;
                $x2 = $x2 + $width;


                //coordinates after transformation are rounded by Zend
                $page->drawImage($image, $x1, $y1, $x2, $y2);
            }
        }

        //insert logo
        $this->y = 720;
        
        //add main category header
        //Added if condition to display/hide cover page title
        if($displaycovertitle)
        {
            $this->y -= 140;
            $font = $this->_setFontBold($page, $this->mainCatHeaderfontSize);

            $page->setFillColor(Zend_Pdf_Color_Html::color($this->coverpageFontColor));
    	    $nameByLevel = $this->getNameByLevel($this->collection, 1, 1);
            $text = strtoupper($nameByLevel[0]);
            $feed = $this->getAlignCenter($text, $this->marginLeft, $page->getWidth() - $this->marginLeft, $font, $this->mainCatHeaderfontSize);
            $this->y -= $this->mainCatHeaderfontSize;
            $page->drawText($text, $feed, $this->y, 'UTF-8');
            if(isset($this->collection['url']) && strlen($this->collection['url']) > 0){
            $target = Zend_Pdf_Action_URI :: create($this->collection['url']);
                if($target){
                $text_width = $this->widthForStringUsingFontSize($text, $font, $this->mainCatHeaderfontSize);
                $annotation = Zend_Pdf_Annotation_Link :: create($feed, $this->y, $feed + $text_width, $this->y + $this->websiteNameFontSize, $target);
                $page->attachAnnotation($annotation);
                }
            }
            $this->y -= 30; //bottom margin
            //add second level cat header
            /* $font = $this->_setFontBold($page, $this->secondCatHeaderfontSize);
              $text = strtoupper($this->getNameByLevel($this->collection, 2, 2));
              $feed = $this->getAlignCenter($text, $this->marginLeft, $page->getWidth() - $this->marginLeft, $font, $this->secondCatHeaderfontSize);
              $page->drawText($text, $feed, $this->y, 'UTF-8');
              $this->y -= 30; //bottom margin */

            //add current website name
            $font = $this->_setFontRegular($page, $this->websiteNameFontSize);
            $siteName = strtoupper(Mage::app()->getWebsite()->getName());
            $siteLink = Mage::getBaseUrl();
            $feed = $this->getAlignCenter($siteName, $this->marginLeft, $page->getWidth() - $this->marginLeft, $font, $this->websiteNameFontSize);
            $page->drawText($siteName, $feed, $this->y, 'UTF-8');
            $target = Zend_Pdf_Action_URI :: create($siteLink);
            if($target){
                $text_width = $this->widthForStringUsingFontSize($siteName, $font, $this->websiteNameFontSize);
                $annotation = Zend_Pdf_Annotation_Link :: create($feed, $this->y, $feed + $text_width, $this->y + $this->websiteNameFontSize, $target);
                $page->attachAnnotation($annotation);
            }
        }
    }

    protected function array_put_to_position(&$array, $object, $position, $name = null) {
        $count = 0;
        $return = array();
        foreach ($array as $k => $v) {
            // insert new object
            if ($count == $position) {
                if (!$name)
                    $name = $count;
                $return[$name] = $object;
                $inserted = true;
            }
            // insert old object
            $return[$k] = $v;
            $count++;
        }
        if (!$name)
            $name = $count;
        if (!$inserted)
            $return[$name];
        $array = $return;
        return $array;
    }

    protected function getNameByLevel($collection, $level, $currentLevel) {
        if ($collection['level'] == $level) {
            //return $collection['name'];
            return array($collection['name'],$collection['id']);
        } else {
            if (isset($collection['child']) && is_array($collection['child'])) {
                $collection = $collection['child'][0];
                return $this->getNameByLevel($collection, $level, $currentLevel);
            }
        }
        return '';
    }

    protected function countTotalProducts($collection) {
        
        if (isset($collection['child'])) {
            if (is_array($collection['child'])) {
                $collection = $collection['child'];
                foreach ($collection as $c) {
                    $this->countTotalProducts($c);
                }
            } else {
                if(is_string($collection['child']) && is_file($collection['child']))
                {
                    $items = unserialize(file_get_contents($collection['child']));
                    $this->_totalProducts += count($items->getItems());
                }else{
                    $this->_totalProducts += count($collection['child']->getData());
                }
            }
        }
    }

    protected function getParentNameByLevel($collection, $level, $currentLevel, $currentParents) {
        if ($collection['level'] == $level) {
            //return $collection['name'];
            return array($collection['name'],$collection['id']);
        } else {
            if (isset($collection['child']) && is_array($collection['child'])) {
                foreach ($collection['child'] as $c) {
                    if (in_array($c['id'], $currentParents))
                        $collection = $c;
                }
                return $this->getParentNameByLevel($collection, $level, $currentLevel, $currentParents);
            }
        }
        return '';
    }

    protected function drawProducts($collection) {
        if(is_string($collection[$collection['id']]['products']['child']) && is_file($collection[$collection['id']]['products']['child']))
        {
            $object = unserialize(file_get_contents($collection[$collection['id']]['products']['child']));
            unset($collection[$collection['id']]['products']['child']);
            $collection[$collection['id']]['products']['child'] = $object;
        }
        if (count($collection[$collection['id']]['products']['child']) > 0) {
            $page = $this->newPage(array('displayHeaderFooter'=>true,'displayPageNumber'=>true));
            $x = $this->marginLeft;
            $this->y = $page->getHeight() - $this->marginTop;

            switch ($this->fontStyle) {
                case 'bold':
                    $font = $this->_setFontBold($page, $this->fontSize);
                    break;
                case 'italic':
                    $font = $this->_setFontItalic($page, $this->fontSize);
                    break;
                default:
                    $font = $this->_setFontRegular($page, $this->fontSize);
                    break;
            }
            
            if (array_key_exists('layout', $this->collection) && $this->collection['layout'] == 'grid') {
                $this->drawGrid($collection[$collection['id']]['products'], $page);
            }
            if (array_key_exists('layout', $this->collection) && $this->collection['layout'] == 'list') {
                $this->drawList($collection[$collection['id']]['products'], $page);
            }
        }
        $resourceModel = Mage::getModel('catalog/product');
        if (is_array($collection['child'])) {

            $collection = $collection['child'];

            foreach ($collection as $c) {
                $this->drawProducts($c);
            }
        } else {
            $page = $this->newPage(array('displayHeaderFooter'=>true,'displayPageNumber'=>true));

            $x = $this->marginLeft;
            $this->y = $page->getHeight() - $this->marginTop;

            switch ($this->fontStyle) {
                case 'bold':
                    $font = $this->_setFontBold($page, $this->fontSize);
                    break;
                case 'italic':
                    $font = $this->_setFontItalic($page, $this->fontSize);
                    break;
                // New font added by ktplDev
                case 'italicbold':
                    $font = $this->_setFontItalicbold($page, $this->fontSize);
                    break;
                default:
                    $font = $this->_setFontRegular($page, $this->fontSize);
                    break;
            }
            try {
                if(is_string($collection['child']) && is_file($collection['child']))
                {
                    $object = unserialize(file_get_contents($collection['child']));
                    unset($collection['child']);
                
                    $collection['child'] = $object;
                }
                if (count($collection['child']) > 0) {
                    if (array_key_exists('layout', $this->collection) && $this->collection['layout'] == 'grid') {
                        $this->drawGrid($collection, $page);
                    }
                    if (array_key_exists('layout', $this->collection) && $this->collection['layout'] == 'list') {
                        $this->drawList($collection, $page);
                    }
                }
            } catch (Exception $e) {
                echo $e;
                die;
            }
        }
    }

    protected function drawHeader($page, $collection, $index) {
        $x = $this->marginLeft;
        $this->y = $page->getHeight() - $this->marginTop;

        $currentLevel = $collection['level'];
        $currentParents = Mage::getModel('catalog/category')->load($collection['id'])->getParentIds();

        $firstLevel = ($currentLevel > 1) ?
                $this->getParentNameByLevel($this->collection, 1, $currentLevel, $currentParents) :
                $this->getNameByLevel($collection, 1, $currentLevel);
        $secondLevel = ($currentLevel > 2) ?
                $this->getParentNameByLevel($this->collection, 2, $currentLevel, $currentParents) :
                $this->getNameByLevel($collection, 2, $currentLevel);
        $thirdLevel = ($currentLevel > 3) ?
                $this->getParentNameByLevel($this->collection, 3, $currentLevel, $currentParents) :
                $this->getNameByLevel($collection, 3, $currentLevel);
        $fourthLevel = ($currentLevel > 4) ?
                $this->getParentNameByLevel($this->collection, 4, $currentLevel, $currentParents) :
                $this->getNameByLevel($collection, 4, $currentLevel);
        $fifthLevel = ($currentLevel > 5) ?
                $this->getParentNameByLevel($this->collection, 5, $currentLevel, $currentParents) :
                $this->getNameByLevel($collection, 5, $currentLevel);

        if($this->isRoot){
            $firstLine = $firstLevel[0] . ' > ' . $secondLevel[0];
            $lastLine .= (!empty($fourthLevel[0])) ? ' > ' .$fourthLevel[0] : '';
            $lastLine .= (!empty($fifthLevel[0])) ? ' > ' . $fifthLevel[0] : '';
            
        }else{
            $firstLine = $firstLevel[0] . ' > ' . $secondLevel[0];
            $lastLine .= (!empty($fourthLevel[0])) ? ' > ' .$fourthLevel[0] : '';
            $lastLine .= (!empty($fifthLevel[0])) ? ' > ' . $fifthLevel[0] : '';
        }

        $this->y -= $this->page_header_category_firstline_font_size;
        $font = $this->_setFontRegular($page, $this->page_header_category_firstline_font_size);
        $feed = $this->getAlignCenter($firstLine, $x, $page->getWidth() - $x, $font, $this->page_header_category_firstline_font_size);
        $page->drawText(trim($firstLine,' > '), $feed, $this->y, 'UTF-8');

        //get border color from catalog setting added by ktplDev
        if (Mage::getStoreConfig('catalogprint/general/border_color') == "") {
            $borderColor = '#000000';
        } else {
            $borderColor = Mage::getStoreConfig('catalogprint/general/border_color');
        }
        //draw separator yellow line
        $x1 = $x + $page->getWidth() - $this->marginLeft - $this->marginLeft;
        $this->y -= 5;
        $page->setLineColor(Zend_Pdf_Color_Html::color('#000000'));
        $page->drawLine($x, $this->y, $x1, $this->y);
        $page->setLineColor(Zend_Pdf_Color_Html::color($borderColor));

        if (!empty($thirdLevel[0]) && $index == 0) {
            $this->y -= $this->page_header_category_secondline_font_size;
            $font = $this->_setFontBold($page, $this->page_header_category_secondline_font_size);
            $page->drawText($thirdLevel[0], $x, $this->y, 'UTF-8');
        }

        if (!empty($lastLine) && $index == 0) {
            $this->y -= $this->page_header_category_thirdline_font_size;
            $font = $this->_setFontRegular($page, $this->page_header_category_thirdline_font_size);
            $page->drawText($lastLine, $x, $this->y, 'UTF-8');
        }

        $this->y -= $this->page_header_category_firstline_font_size + $this->page_header_category_secondline_font_size - 15;

        //create this sub-cat named dest.
        $cat_link = Zend_Pdf_Destination_Zoom::create($page);
        //link for last level
        if(!isset($this->tmpArray[$collection['name'].$collection['id']])){
            $this->_getPdf()->setNamedDestination($collection['name'].$collection['id'], $cat_link);
            $this->tmpArray[$collection['name'].$collection['id']] = $this->pageNumber - 1;
        }
        //link for upper levels
        if ($this->_getPdf()->getNamedDestination($secondLevel[0].$secondLevel[1]) == null) {
            $this->_getPdf()->setNamedDestination($secondLevel[0].$secondLevel[1], $cat_link);
            $this->tmpArray[$secondLevel[0].$secondLevel[1]] = $this->pageNumber - 1;
        }
        if ($this->_getPdf()->getNamedDestination($thirdLevel[0].$thirdLevel[1]) == null) {
            $this->_getPdf()->setNamedDestination($thirdLevel[0].$thirdLevel[1], $cat_link);
            $this->tmpArray[$thirdLevel[0].$thirdLevel[1]] = $this->pageNumber - 1;
        }
        if ($this->_getPdf()->getNamedDestination($fourthLevel[0].$fourthLevel[1]) == null) {
            $this->_getPdf()->setNamedDestination($fourthLevel[0].$fourthLevel[1], $cat_link);
            $this->tmpArray[$fourthLevel[0].$fourthLevel[1]] = $this->pageNumber - 1;
        }
    }

    protected function drawList($collection, $page) {
        
	$printHelper = Mage::helper("catalogprint");
        
            $titleFont = null;
            $lableFont = null;
            $valueFont = null;
        
        $qrCodeDir = Mage::getBaseDir().DS.'media'.DS.'catalog-print'.DS.'qrcodes'.DS;
        if( Mage::getStoreConfig('catalogprint/product_list/height') != "" ){
            $this->list_total_height  = Mage::getStoreConfig('catalogprint/product_list/height');
        }
        if (array_key_exists('heading', $this->collection)) {
            $this->drawHeader($page, $collection, 0);
        } else {
            $this->y -= 20;
        }
        $x = $this->marginLeft;
        $p = 0;

        //$options = unserialize(Mage::getStoreConfig('catalogprint/general/product_attributes'));
        $options = explode(',', Mage::getStoreConfig('catalogprint/general/product_attributes'));
        $nameAttribute = 'name';
        if (in_array($nameAttribute, $options)) {
            $key = array_search($nameAttribute, $options);
            unset($options[$key]);
            array_unshift($options, $nameAttribute);
        } else {
            array_unshift($options, $nameAttribute);
        }
        
        switch (count($options)) {
            case 1:
                $options[] = 'nodisplay';
                $options[] = 'nodisplay';
                $options[] = 'nodisplay';
                $options[] = 'nodisplay';
                break;

            case 2:
                $options[] = 'nodisplay';
                $options[] = 'nodisplay';
                $options[] = 'nodisplay';
                break;
            case 3:
                $options[] = 'nodisplay';
                $options[] = 'nodisplay';
                break;
            case 4:
                $options[] = 'nodisplay';
                break;
        }
        
        $tmpOptions = $options;
        $pdesKey = array_search($nameAttribute, $tmpOptions);
        unset($tmpOptions[$pdesKey]);
        $type = array();
        $counter = 0;
        foreach ($collection['child'] as $item) {
            $Url = Mage::getBaseUrl() . '' . $item->getUrlPath();
            $this->k++;
            $index = 0;
            //create manufacturer number link
            $manu_no_link = Zend_Pdf_Destination_Zoom::create($page);
            $this->_getPdf()->setNamedDestination($item->getSku(), $manu_no_link);
            $this->manufacturerNumbers[] = array(
                'manufacturerNo' => $item->getSku(),
                'partDesc' => $item->getName() . '( ' . $collection['name'] . ' )',
                'pageNo' => $this->pageNumber,
            );

            if ($this->y - ($this->list_total_height + $this->marginBottom) < 0) {
                if ($p % 40 == 0) {
                    try {
                        $this->_getPdf()->save($this->_pdfFile);
                        $pdf = Zend_Pdf::load($this->_pdfFile);
                        $memoryManager = $this->getMemoryManager();
                        $pdf->setMemoryManager($memoryManager);
                        $this->_setPdf($pdf);
                        unset($pdf);
                    } catch (Exception $e) {
                        echo '<pre>';
                        print_r($e);
                    }
                }
                $p++;
                $page = $this->newPage(array('displayHeaderFooter'=>true,'displayPageNumber'=>true));
                if (array_key_exists('heading', $this->collection)) {
                    $this->drawHeader($page, $collection, 1);
                } else {
                    $this->y -= 20;
                }
                $x = $this->marginLeft;
                
            }
            if($this->titleFontStyle == 'regular'){
                $titleFont = $this->_setFontRegular($page, $this->attributeHeadingFontSize);
            }elseif($this->titleFontStyle == 'bold'){
                $titleFont = $this->_setFontBold($page, $this->attributeHeadingFontSize);
            }elseif($this->titleFontStyle == 'italic'){
                $titleFont = $this->_setFontItalic($page, $this->attributeHeadingFontSize);
            }else{
                $titleFont = $this->_setFontItalicbold($page, $this->attributeHeadingFontSize);
            }
            
            if($this->labelFontStyle == 'regular'){
                $lableFont = $this->_setFontRegular($page, $this->attributeHeadingFontSize);
            }elseif($this->labelFontStyle == 'bold'){
                $lableFont = $this->_setFontBold($page, $this->attributeHeadingFontSize);
            }elseif($this->labelFontStyle == 'italic'){
                $lableFont = $this->_setFontItalic($page, $this->attributeHeadingFontSize);
            }else{
                $lableFont = $this->_setFontItalicbold($page, $this->attributeHeadingFontSize);
            }
            
            if($this->valueFontStyle == 'regular'){
                $valueFont = $this->_setFontRegular($page, $this->attributeValueFontSize);
            }elseif($this->valueFontStyle == 'bold'){
                $valueFont = $this->_setFontBold($page, $this->attributeValueFontSize);
            }elseif($this->valueFontStyle == 'italic'){
                $valueFont = $this->_setFontItalic($page, $this->attributeValueFontSize);
            }else{
                $valueFont = $this->_setFontItalicbold($page, $this->attributeValueFontSize);
            }
            $tmp = $this->y;
            $this->y -= $this->product_grid_top_padding; //top padding
            //remove image distortion
            $size = ($this->listProductImageSize * $this->listProductImageSize) / ($this->listProductImageSize * 72 / 96) + 10;
            //product image
            $image = (string) Mage::helper('catalogprint/image')->init($item, 'small_image')->resize($size)->getBaseFile();
            
            
            if (is_file($image)) {
                try {
                    $image = Zend_Pdf_Image::imageWithPath($image);
                    $this->y -= $this->listProductImageSize;
                    $page->drawImage($image, $x, $this->y, $x + $this->listProductImageSize, $this->y + $this->listProductImageSize);
                    unset($image);
                } catch (Exception $e) {
                    //Zend_Debug::dump($e);
                }
            }
            if(Mage::getStoreConfig('catalogprint/product_list/qrcodes')){
                $qrCodeFile = $qrCodeDir.$item->getId().'.png';
                if(is_file($qrCodeFile)){

                   try {
                       $codeImage = Zend_Pdf_Image::imageWithPath($qrCodeFile);
                       $qX = $x + $this->list_total_width - $this->marginLeft;
                       $imgWidthPts  = $codeImage->getPixelWidth() * 72 / 96;
                       $imgHeightPts = $codeImage->getPixelHeight() * 72 / 96;
                       $page->drawImage($codeImage, $qX - $imgWidthPts, $this->y + 35, $qX, $this->y + 35 + $imgHeightPts);
                       unset($codeImage);
                   } catch(Exception $e){
                       Mage::log($e->getMessage(),false,'qrException.log');
                   }
                }
            }
            
             
            $flag = 0;
            if ($options != null && !empty($options)) {
                $target = Zend_Pdf_Action_URI :: create($Url);
                $additional = 0;
                $AttributeLabels = Mage::getModel('catalogprint/system_config_source_product_attributes')->toOptionArray1($this->storeId);
                $this->y += $this->listProductImageSize - 5;
                foreach ($options as $option) {
                    if($type[$option] != 'price' && (strlen($item->getData($option)) <=0 || $item->getData($option) == '')){
                            continue;
                        }
                    $x += $this->listProductImageSize + 5;
                    
                    if ($option == $nameAttribute) {
                        $name = $item->getData($option);
                        switch($this->titleFontStyle){
                            case 'regular':
                                            $this->_setFontRegular($page, $this->attributeHeadingFontSize);
                                            break;
                            case 'bold':
                                        $this->_setFontBold($page, $this->attributeHeadingFontSize);
                                        break;
                            case 'italic':
                                          $this->_setFontItalic($page, $this->attributeHeadingFontSize);
                                          break;
                            case 'italicbold':
                                            $this->_setFontItalicbold($page, $this->attributeHeadingFontSize);
                                              break;
                        }
                        
                        $page->setFillColor(Zend_Pdf_Color_Html::color($this->titleFontColor));
                        
                        $page->drawText($name, $x, $this->y, 'UTF-8');
                        $text_width = $this->widthForStringUsingFontSize($name, $titleFont, $this->attributeHeadingFontSize);
                        $annotation = Zend_Pdf_Annotation_Link :: create($x, $this->y, $x + $text_width, $this->y + $this->attributeHeadingFontSize, $target);
                        $page->attachAnnotation($annotation);
                        $lx = $x - $this->listProductImageSize + $this->list_total_width - $this->marginLeft;
                        $page->setLineColor(Zend_Pdf_Color_Html::color($this->listBorderColor));
                        $page->drawLine($x, $this->y - 5, $lx, $this->y - 5);
                        $this->y -= 25;
                        $x = $this->listProductImageSize;
                    } 
		    else if($option == 'tier_price'){
                                        $x11 = $this->listProductImageSize + 25;
                                        $tirePrices = $printHelper->getTirePrices($item);
                                            if(count($tirePrices) <= 0){
                                                continue;
                                            }
                                            /* Tire prices block start */
                                            
                                            $heading = $AttributeLabels[$option] . ': ';
                                            $head_text_width = $this->widthForStringUsingFontSize($heading, $lableFont, $this->attributeHeadingFontSize);
                                            
                                            switch($this->labelFontStyle){
                                                case 'regular':
                                                                $this->_setFontRegular($page, $this->attributeHeadingFontSize);
                                                                break;
                                                case 'bold':
                                                            $this->_setFontBold($page, $this->attributeHeadingFontSize);
                                                            break;
                                                case 'italic':
                                                              $this->_setFontItalic($page, $this->attributeHeadingFontSize);
                                                              break;
                                                case 'italicbold':
                                                                $this->_setFontItalicbold($page, $this->attributeHeadingFontSize);
                                                                  break;
                                            }
                                            $page->setFillColor(Zend_Pdf_Color_Html::color($this->labelFontColor));
                                            $page->drawText(trim($heading), $x11, $this->y, 'UTF-8');
                                            if(count($tirePrices) > 0){
                                                $key = array_search($option,$options);
                                                if($index == 1 && $key > 0){
                                                    $this->y = $this->y - 15;
                                                 }
                                                
                                                 
                                                $lines = explode("\n",$this->getWrappedText(implode(", ",$tirePrices), $valueFont, $this->attributeFontSize, $this->list_total_width - $this->listProductImageSize - $head_text_width));       
                                                $tmpOpt = '';
                                                 $stack = array();
                                                 $tmpOpt = $lines[0];
                                                 unset($lines[0]);
                                                 $stack = implode("\n",$lines);
                                                 $lines = explode("\n",$this->getWrappedText(implode(", ",$stack), $valueFont, $this->attributeFontSize, $this->list_total_width - $this->listProductImageSize));
                                                 array_unshift($lines,$tmpOpt);
                                                switch($this->valueFontStyle){
                                                    case 'regular':
                                                                    $this->_setFontRegular($page, $this->attributeValueFontSize);
                                                                    break;
                                                    case 'bold':
                                                                $this->_setFontBold($page, $this->attributeValueFontSize);
                                                                break;
                                                    case 'italic':
                                                                  $this->_setFontItalic($page, $this->attributeValueFontSize);
                                                                  break;
                                                    case 'italicbold':
                                                                    $this->_setFontItalicbold($page, $this->attributeValueFontSize);
                                                                      break;
                                                }
                                                $page->setFillColor(Zend_Pdf_Color_Html::color($this->valueFontColor));
                                                foreach($lines as $i=>$line){
                                                    $tireX1 = ($i == 0) ? $x11 + $head_text_width: $x11;
                                                    $page->drawText($line, $tireX1, $this->y, 'UTF-8');
                                                    $this->y -= 10;
                                                }
                                                $this->y -= 5;
                                                $index = 0;
                                           }
                                        }
		     else {
                        $x11 = $this->listProductImageSize + 25;
                        
                        $x12 = ($this->list_total_width - $this->listProductImageSize - $this->marginLeft) / 2;
                        if (!isset($type[$option])) {
                            $attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', $option);
                            if ($attribute)
                                $type[$option] = $attribute->getSource()->getAttribute()->getFrontendInput();
                        }
                        if ($type[$option] == 'textarea') {
                            $readMoreWithoutSpace = $printHelper->__('Readmore');
                            $readMoreText = $printHelper->__('Read more');
                            $limitChars = (int)Mage::getStoreConfig('catalogprint/product_list/limit_chars');
                            $readMoreTextWidth = 0;
                            $key = array_search($option, $options);
                            if ($index == 1 && $key > 0) {
                                $this->y = $this->y - 15;
                            }
                            
                            switch($this->labelFontStyle){
                                  case 'regular':
                                                 $this->_setFontRegular($page, $this->attributeHeadingFontSize);
                                                 break;
                                  case 'bold':
                                             $this->_setFontBold($page, $this->attributeHeadingFontSize);
                                             break;
                                  case 'italic':
                                                $this->_setFontItalic($page, $this->attributeHeadingFontSize);
                                                break;
                                  case 'italicbold':
                                                   $this->_setFontItalicbold($page, $this->attributeHeadingFontSize);
                                                   break;
                            }
                            $page->setFillColor(Zend_Pdf_Color_Html::color($this->labelFontColor));
  
                            $heading = $AttributeLabels[$option] . ': ';
                            $head_text_width = $this->widthForStringUsingFontSize($heading, $lableFont, $this->attributeHeadingFontSize);
                            
                            $page->drawText(trim($heading), $x11, $this->y, 'UTF-8');
                            
                            
                            
                            if($limitChars == '' || $limitChars <= 0){
                                $Descs = explode("\n", $this->getWrappedText(str_replace(array("\r\n", "\r", "\n"), '', trim(strip_tags($item->getData($option)))), $valueFont, $this->attributeFontSize, $this->list_total_width - $this->listProductImageSize - $head_text_width));
                            }else{
                                $text = str_replace(array("\r\n", "\r", "\n"), '', trim(strip_tags($item->getData($option))));
                                if($this->listDisplayReadMore){
                                    $textWithReadmore = strlen($text) > $limitChars ? substr($text,0,$limitChars).'...'.$readMoreWithoutSpace.'': $text;
                                    $readMoreTextWidth = $this->widthForStringUsingFontSize('...'.$readMoreWithoutSpace.'', $valueFont, $this->attributeFontSize);
                                }else{
                                    $textWithReadmore = strlen($text) > $limitChars ? substr($text,0,$limitChars).'...': $text;
                                    $readMoreTextWidth = 0;
                                }
                                $Descs = explode("\n", $this->getWrappedText($textWithReadmore, $valueFont, $this->attributeFontSize, $this->list_total_width - $this->listProductImageSize - $head_text_width));
                            }
                            $tmpOpt = '';
                            $stack = array();
                            $tmpOpt = $Descs[0];
                            unset($Descs[0]);
                            $stack = implode("\n",$Descs);
                            $Descs = explode("\n", $this->getWrappedText(str_replace(array("\r\n", "\r", "\n"), '', trim(strip_tags($stack))), $valueFont, $this->attributeFontSize, $this->list_total_width - $this->listProductImageSize - $head_text_width));
                                    array_unshift($Descs,$tmpOpt);
                            if(count($Descs) > 7){
                               $counter=7;
                            }else{
                                $counter=count($Descs);
                            }
                            $readMoreExists = false;
                            switch($this->valueFontStyle){
                                  case 'regular':
                                                 $this->_setFontRegular($page, $this->attributeValueFontSize);
                                                 break;
                                  case 'bold':
                                             $this->_setFontBold($page, $this->attributeValueFontSize);
                                             break;
                                  case 'italic':
                                                $this->_setFontItalic($page, $this->attributeValueFontSize);
                                                break;
                                  case 'italicbold':
                                                   $this->_setFontItalicbold($page, $this->attributeValueFontSize);
                                                   break;
                            }
                            $page->setFillColor(Zend_Pdf_Color_Html::color($this->valueFontColor));
                            for ($d=0;$d<count($Descs);$d++){
                       
                                $stringWidth = 0;
                                $trimmedString = str_replace(array("\r\n", "\r", "\n"), '', trim($Descs[$d]));
                                if($d==0){
                                    if (array_key_exists($d, $Descs) && strlen($trimmedString) > 0) {
                                        
                                        if(substr($trimmedString,-strlen('...'.$readMoreWithoutSpace.'')) == '...'.$readMoreWithoutSpace.''){
                                            
                                            $stringWidth = $this->widthForStringUsingFontSize($trimmedString, $valueFont, $this->attributeFontSize);
                                            $trimmedString = str_replace(array('...'.$readMoreWithoutSpace.'',"\r\n", "\r", "\n"),'',$trimmedString);
                                            $readMoreExists = true;
                                        }
                                        $page->drawText($trimmedString, $x11 + $head_text_width, $this->y, 'UTF-8');
                                        if($readMoreExists){
                                            $page->setFillColor(Zend_Pdf_Color_Html::color('#1122CC'));
                                            $page->drawText('...'.$readMoreText.'', $x11 + $head_text_width + $stringWidth - $readMoreTextWidth - 55, $this->y, 'UTF-8');
                                            $annotation = Zend_Pdf_Annotation_Link :: create($x11 + $head_text_width + $stringWidth - $readMoreTextWidth - 55, $this->y, $x11 + $head_text_width + $stringWidth, $this->y + $this->attributeFontSize, $target);
                                            $page->attachAnnotation($annotation);
                                        }
                                        
                                        $this->y = $this->y - 10;
                                    }
                                }else{
                                    if (array_key_exists($d, $Descs) && strlen($trimmedString) > 0) {
                                        if(substr($trimmedString,-strlen('...'.$readMoreWithoutSpace.'')) == '...'.$readMoreWithoutSpace.''){
                                           $stringWidth = $this->widthForStringUsingFontSize($trimmedString, $valueFont, $this->attributeFontSize);
                                           $trimmedString = str_replace(array('...'.$readMoreWithoutSpace.'',"\r\n", "\r", "\n"),'',$trimmedString);
                                           $readMoreExists = true;
                                        }
                                        $page->drawText($trimmedString, $x11, $this->y, 'UTF-8');
                                        if($readMoreExists){
                                           $page->setFillColor(Zend_Pdf_Color_Html::color('#1122CC'));
                                           
                                           $page->drawText('...'.$readMoreText.'', $x11 + $stringWidth - $readMoreTextWidth, $this->y, 'UTF-8'); 
                                           
                                           $annotation = Zend_Pdf_Annotation_Link :: create($x11 + $stringWidth - $readMoreTextWidth, $this->y, $x11 + $stringWidth, $this->y + $this->attributeFontSize, $target);
                                           $page->attachAnnotation($annotation);
                                        }
                                        $this->y = $this->y - 10;
                                    }
                                }
                            }
                            if (!array_key_exists(0, $Descs)) {
                                $this->y = $this->y - 10;
                            }
                            $this->y = $this->y - 5;

                            $index = 0;
                        } else {
                            if ($type[$option] == "select" || $type[$option] == 'multiselect') {
                                $heading = $AttributeLabels[$option] . ': ';
                                $head_text_width = $this->widthForStringUsingFontSize($heading, $lableFont, $this->attributeHeadingFontSize);
                                
                                $optVal = is_array($item->getAttributeText($option)) ? implode(', ',$item->getAttributeText($option)): $item->getAttributeText($option);
                                
                                switch ($index) {
                                    case 0:$hx = $this->listProductImageSize + 25;
                                        break;

                                    case 1:$hx = ($this->list_total_width + $this->listProductImageSize - $this->marginLeft) / 2;
                                        break;
                                    case 2:$hx = $this->listProductImageSize + 25;
                                        $index = 0;
                                        break;
                                }
                                
                                
                                switch($this->labelFontStyle){
                                    case 'regular':
                                                   $this->_setFontRegular($page, $this->attributeHeadingFontSize);
                                                   break;
                                    case 'bold':
                                               $this->_setFontBold($page, $this->attributeHeadingFontSize);
                                               break;
                                    case 'italic':
                                                  $this->_setFontItalic($page, $this->attributeHeadingFontSize);
                                                  break;
                                    case 'italicbold':
                                                     $this->_setFontItalicbold($page, $this->attributeHeadingFontSize);
                                                     break;
                              }
                            $page->setFillColor(Zend_Pdf_Color_Html::color($this->labelFontColor));
                                $page->drawText(trim($heading), $hx, $this->y, 'UTF-8');
                                $hx = $hx + $head_text_width;
                                switch($this->valueFontStyle){
                                    case 'regular':
                                                   $this->_setFontRegular($page, $this->attributeValueFontSize);
                                                   break;
                                    case 'bold':
                                               $this->_setFontBold($page, $this->attributeValueFontSize);
                                               break;
                                    case 'italic':
                                                  $this->_setFontItalic($page, $this->attributeValueFontSize);
                                                  break;
                                    case 'italicbold':
                                                     $this->_setFontItalicbold($page, $this->attributeValueFontSize);
                                                     break;
                              }
                            $page->setFillColor(Zend_Pdf_Color_Html::color($this->valueFontColor));
                                if (stripos($optVal, ',', 0) == false) {
                                    
                                    $page->drawText($optVal, $hx, $this->y, 'UTF-8');
                                    $index++;
                                    if ($index == 2) {
                                        $this->y = $this->y - 15 - $additional;
                                    }
                                } else {
                                    $tmpOpt = '';
                                    $stack = array();
                                    $opts = explode("\n",$this->getWrappedText($optVal, $valueFont, $this->attributeFontSize, $x12 - $head_text_width));
                                    $tmpOpt = $opts[0];
                                    unset($opts[0]);
                                    $stack = implode("\n",$opts);
                                    $opts = explode("\n",$this->getWrappedText(str_replace(array("\n"),"",$stack),$valueFont, $this->attributeFontSize, $x12));
                                    array_unshift($opts,$tmpOpt);
                                    foreach ($opts as $i => $line) {
                                        $Tmpy = $this->y;
                                        if ($i != sizeof($opts) - 1 && sizeof($opts) != 2 && $i != 0) {
                                            $Tmpy = $this->y - $this->attributeFontSize;
                                            $flag = 1;
                                        }
                                        if($i > 1){
                                             $additional += $this->attributeFontSize;
                                        }
                                        $page->drawText($line, $hx, $Tmpy, 'UTF-8');
                                    }
                                    $index++;
                                    if ($index == 2) {
                                        $this->y = $this->y - 15 - $additional;
                                    }
                                }
                            } else {
                                if ($type[$option] == 'price') {
				    $priceArray['price_tax'] = $printHelper->getPriceBlock($item, $this->customerGroups, $this->websiteId, $this->storeId);
                                    $heading = $AttributeLabels[$option] . ': ';
                                    $head_text_width = $this->widthForStringUsingFontSize($heading, $lableFont, $this->attributeHeadingFontSize);

					if($option == 'price'){
                                            if(Mage::getVersion()=="1.9.1.1"){
                                                $optVal = isset($priceArray['price_tax']) ? $priceArray['price_tax'] : $this->currencyByStore($item->getFinalPrice(),Mage::app()->getStore()->getId(),true,false);
                                            }else{
                                                $optVal = isset($priceArray['price_tax']) ? $priceArray['price_tax'] : Mage::helper('core')->currencyByStore($item->getFinalPrice(),Mage::app()->getStore()->getId(),true,false);
                                            }
                                                                                    
                                            if(isset($priceArray['regular_price']) && $priceArray['special_price']){
                                                $optVal = $priceArray['regular_price'].', '.$priceArray['special_price'];
                                            }
                                        }else{
                                            if(Mage::getVersion()=="1.9.1.1"){
                                                $optVal = $this->currencyByStore($item->getData($option), Mage::app()->getStore()->getId(), true, false);
                                            }else{
                                                $optVal = Mage::helper('core')->currencyByStore($item->getData($option), Mage::app()->getStore()->getId(), true, false);
                                            }
                                        }                                    
                                } else {
                                    $heading = $AttributeLabels[$option] . ': ';
                                    $head_text_width = $this->widthForStringUsingFontSize($heading, $lableFont, $this->attributeHeadingFontSize);
                                    
                                    $optVal = $item->getData($option);
                                }
                                
                                switch ($index) {
                                    case 0:$hx = $this->listProductImageSize + 25;
                                        break;

                                    case 1:$hx = ($this->list_total_width + $this->listProductImageSize - $this->marginLeft) / 2;
                                        break;
                                    case 2:$hx = $this->listProductImageSize + 25;
                                        $index = 0;
                                        break;
                                }
                                
                                switch($this->labelFontStyle){
                                    case 'regular':
                                                   $this->_setFontRegular($page, $this->attributeHeadingFontSize);
                                                   break;
                                    case 'bold':
                                               $this->_setFontBold($page, $this->attributeHeadingFontSize);
                                               break;
                                    case 'italic':
                                                  $this->_setFontItalic($page, $this->attributeHeadingFontSize);
                                                  break;
                                    case 'italicbold':
                                                     $this->_setFontItalicbold($page, $this->attributeHeadingFontSize);
                                                     break;
                              }
                            $page->setFillColor(Zend_Pdf_Color_Html::color($this->labelFontColor));
                                if ($option != 'nodisplay') {
                                    $page->drawText(trim($heading), $hx, $this->y, 'UTF-8');
                                }
                                 
                                $hx = $hx + $head_text_width;
                                switch($this->valueFontStyle){
                                    case 'regular':
                                                   $this->_setFontRegular($page, $this->attributeValueFontSize);
                                                   break;
                                    case 'bold':
                                               $this->_setFontBold($page, $this->attributeValueFontSize);
                                               break;
                                    case 'italic':
                                                  $this->_setFontItalic($page, $this->attributeValueFontSize);
                                                  break;
                                    case 'italicbold':
                                                     $this->_setFontItalicbold($page, $this->attributeValueFontSize);
                                                     break;
                              }
                              $page->setFillColor(Zend_Pdf_Color_Html::color($this->valueFontColor));
                                if (stripos($optVal, ',', 0) == false) {
                                    if ($option != 'nodisplay') {
                                        $page->drawText($optVal, $hx, $this->y, 'UTF-8');
                                    }
                                    $index++;
                                    if ($index == 2) {
                                        $this->y = $this->y - 15 - $additional;
                                    }
                                } else {
                                    $opts = explode("\n", $this->getWrappedText($optVal, $valueFont, $this->attributeFontSize, $x12 - $head_text_width));
                                    
                                    foreach ($opts as $i => $line) {
                                        $Tmpy = $this->y;
                                        if ($i != sizeof($opts) - 1 && sizeof($opts) != 2 && $i != 0) {
                                            $Tmpy = $this->y - $this->attributeFontSize;
                                            $flag = 1;
                                        }
                                        if($i > 1){
                                             $additional += $this->attributeFontSize;
                                        }
                                        if ($option != 'nodisplay') {
                                            $page->drawText($line, $hx, $Tmpy, 'UTF-8');
                                        }
                                    }
                                    $index++;
                                    if ($index == 2) {
                                        $this->y = $this->y - 15 - $additional;
                                    }
                                }
                            }
                        }
                    }
                }
                unset($target);
            }
            $this->y = $tmp;

            if ($x >= 500) {
                $x = $this->marginLeft;
                $this->y -= $this->list_total_height + $this->product_grid_bottom_margin;
            }else if (count($tmpOptions) <= 3) {
                $x = $this->marginLeft;
                $this->y -= $this->list_total_height + $this->product_grid_bottom_margin;
            }else{
                $x = $this->marginLeft;
                $this->y -= $this->list_total_height + $this->product_grid_bottom_margin;
            }

            //write progress
            
            $stringData = ($this->k * 100) / $this->_totalProducts;
            if ($stringData < '100'){
                file_put_contents($this->_logFile, $stringData);
            }
        }
    }

    protected function drawGrid($collection, $page) {
        $this->product_grid_total_width = $this->selectedLayout['width'];
        $this->product_grid_total_height = $this->selectedLayout['height'];
        $this->gridProductImageSize = $this->selectedLayout['img_size'];
	$printHelper = Mage::helper("catalogprint");
        $readMoreWithoutSpace = $printHelper->__('Readmore');
        $readMoreText = $printHelper->__('Read more');
        
        $titleFont = null;
            $lableFont = null;
            $valueFont = null;
        $qrCodeDir = Mage::getBaseDir().DS.'media'.DS.'catalog-print'.DS.'qrcodes'.DS;
        
        if (array_key_exists('heading', $this->collection)) {
            $this->drawHeader($page, $collection, 0);
        } else {
            $this->y -= 20;
        }
        $rnk = 0;
        $x = $this->marginLeft;

        $p = 0;
        $options = explode(',', Mage::getStoreConfig('catalogprint/general/product_attributes'));
        $nameAttribute = 'name';
        if (in_array($nameAttribute, $options)) {
            $key = array_search($nameAttribute, $options);
            unset($options[$key]);
            array_unshift($options, $nameAttribute);
        } else {
            array_unshift($options, $nameAttribute);
        }
        $type = array();

        foreach ($collection['child'] as $item) {
            $this->k++;
            $rnk ++;
            $Url = Mage::getBaseUrl() . '' . $item->getUrlPath();
            $manu_no_link = Zend_Pdf_Destination_Zoom::create($page);
            $this->_getPdf()->setNamedDestination($item->getSku(), $manu_no_link);
            $this->manufacturerNumbers[] = array(
                'manufacturerNo' => $item->getSku(),
                'partDesc' => $item->getName() . '( ' . $collection['name'] . ' )',
                'pageNo' => $this->pageNumber,
            );

            if ($this->y - ($this->grid_total_height + $this->marginBottom) < 0){
                if ($p % 40 == 0) {
                    try {
                        $this->_getPdf()->save($this->_pdfFile);
                        $pdf = Zend_Pdf::load($this->_pdfFile);
                        $memoryManager = $this->getMemoryManager();
                        $pdf->setMemoryManager($memoryManager);
                        $this->_setPdf($pdf);
                        unset($pdf);
                    } catch (Exception $e) {
                        echo '<pre>';
                        print_r($e);
                    }
                }
                $p++;
                $page = $this->newPage(array('displayHeaderFooter'=>true,'displayPageNumber'=>true));
                if (array_key_exists('heading', $this->collection)) {
                    $this->drawHeader($page, $collection, 1);
                } else {
                    $this->y -= 20;
                }
                $x = $this->marginLeft;
                $font = $this->_setFontRegular($page, $this->fontSize);
            }

            $tmp = $this->y;

            //grid border added by ktplDev
            if ($this->gridBorder == 1) {
                $page->setLineColor(Zend_Pdf_Color_Html::color($this->gridBorderColor));
                
                $page->drawRectangle($x, $this->y, $x + $this->product_grid_total_width, $this->y - $this->product_grid_total_height, $fillType = Zend_Pdf_Page::SHAPE_DRAW_STROKE);
            }
            
            if($this->titleFontStyle == 'regular'){
                $titleFont = $this->_setFontRegular($page, $this->attributeHeadingFontSize);
            }elseif($this->titleFontStyle == 'bold'){
                $titleFont = $this->_setFontBold($page, $this->attributeHeadingFontSize);
            }elseif($this->titleFontStyle == 'italic'){
                $titleFont = $this->_setFontItalic($page, $this->attributeHeadingFontSize);
            }else{
                $titleFont = $this->_setFontItalicbold($page, $this->attributeHeadingFontSize);
            }
            
            if($this->labelFontStyle == 'regular'){
                $lableFont = $this->_setFontRegular($page, $this->attributeHeadingFontSize);
            }elseif($this->labelFontStyle == 'bold'){
                $lableFont = $this->_setFontBold($page, $this->attributeHeadingFontSize);
            }elseif($this->labelFontStyle == 'italic'){
                $lableFont = $this->_setFontItalic($page, $this->attributeHeadingFontSize);
            }else{
                $lableFont = $this->_setFontItalicbold($page, $this->attributeHeadingFontSize);
            }
            
            if($this->valueFontStyle == 'regular'){
                $valueFont = $this->_setFontRegular($page, $this->attributeValueFontSize);
            }elseif($this->valueFontStyle == 'bold'){
                $valueFont = $this->_setFontBold($page, $this->attributeValueFontSize);
            }elseif($this->valueFontStyle == 'italic'){
                $valueFont = $this->_setFontItalic($page, $this->attributeValueFontSize);
            }else{
                $valueFont = $this->_setFontItalicbold($page, $this->attributeValueFontSize);
            }
            if($this->selectedProductCounts == 9){
                $this->gridProductImageSize = $this->perPageSettings['6']['img_size'];
            }
            $x += $this->product_grid_left_padding; //left padding
            $this->y -= $this->product_grid_top_padding; //top padding
            //remove image distortion
            $size = ($this->gridProductImageSize * $this->gridProductImageSize) / ($this->gridProductImageSize * 72 / 96) + 10;
            //product image
            $image = (string) Mage::helper('catalogprint/image')->init($item, 'small_image')->resize($size)->getBaseFile();
            if(Mage::getStoreConfig('catalogprint/product_grid/qrcodes')){
                $qrCodeFile = $qrCodeDir.$item->getId().'.png';
                if(is_file($qrCodeFile)){
                   try {
                       $codeImage = Zend_Pdf_Image::imageWithPath($qrCodeFile);
                       
                       $imgWidthPts  = $codeImage->getPixelWidth() * 72 / 96;
                       $imgHeightPts = $codeImage->getPixelHeight() * 72 / 96;
                       $x1 = $x + $this->gridProductImageSize - $imgWidthPts;
                       $y1 = $this->y - $this->product_grid_total_height + $imgHeightPts - 23;
                       $x2 = $x1 + $imgWidthPts;
                       $y2 = $y1 + $imgHeightPts;
                       $page->drawImage($codeImage, $x1 , $y1, $x2, $y2);
                       unset($codeImage);
                   } catch(Exception $e){
                       Mage::log($e->getMessage(),false,'qrException.log');
                   }
                }
            }
            if($this->selectedProductCounts == 9){
                $this->gridProductImageSize = $this->selectedLayout['img_size'];
            }
            if (is_file($image)) {
                try {
                    $image = Zend_Pdf_Image::imageWithPath($image);
                    if($this->selectedProductCounts == 4){
                        $this->y -= $this->gridProductImageSize;
                        $page->drawImage($image, $x + 30, $this->y + 60, $x + $this->gridProductImageSize - 30, $this->y + $this->gridProductImageSize);
                        $this->y += 60;
                    }else{
                        $this->y -= $this->gridProductImageSize - 25;
                        $page->drawImage($image, $x, $this->y, $x + $this->gridProductImageSize, $this->y + $this->gridProductImageSize - 25);
                    }
                    unset($image);
                } catch (Exception $e) {
                    //Zend_Debug::dump($e);
                }
            }
            if($this->selectedProductCounts == 9){
            $this->gridProductImageSize = $this->perPageSettings['6']['img_size'];
            }
          
            $flag = 0;
            if ($options != null && !empty($options)) {
                $target = Zend_Pdf_Action_URI :: create($Url);
                $AttributeLabels = Mage::getModel('catalogprint/system_config_source_product_attributes')->toOptionArray1($this->storeId);

                foreach ($options as $option) { 
                    if (!isset($type[$option])) {
                        $attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', $option);
                        if ($attribute)
                            $type[$option] = $attribute->getSource()->getAttribute()->getFrontendInput();
                    }
                    if($type[$option] != 'price' && (strlen($item->getData($option)) <=0 || $item->getData($option) == '')){
                            continue;
                        }
                    $this->y -= $this->attributeFontSize + 5;
                    
                    if ($type[$option] == 'price') {

                        $heading = $AttributeLabels[$option] . ': ';
                        $head_text_width = $this->widthForStringUsingFontSize($heading, $lableFont, $this->attributeHeadingFontSize);
                        switch($this->labelFontStyle){
                            case 'regular':
                                            $this->_setFontRegular($page, $this->attributeHeadingFontSize);
                                            break;
                            case 'bold':
                                        $this->_setFontBold($page, $this->attributeHeadingFontSize);
                                        break;
                            case 'italic':
                                          $this->_setFontItalic($page, $this->attributeHeadingFontSize);
                                          break;
                            case 'italicbold':
                                            $this->_setFontItalicbold($page, $this->attributeHeadingFontSize);
                                              break;
                        }
                        $page->setFillColor(Zend_Pdf_Color_Html::color($this->labelFontColor));
                        $page->drawText(trim($heading), $x, $this->y, 'UTF-8');
                        
                       
                            if($option == 'price'){
                                $priceArray['price_tax'] = $printHelper->getPriceBlock($item, $this->customerGroups, $this->websiteId, $this->storeId);
                                if(Mage::getVersion()=="1.9.1.1"){    
                                    $price = isset($priceArray['price_tax']) ? $priceArray['price_tax'] : $this->currencyByStore($item->getFinalPrice(),Mage::app()->getStore()->getId(),true,false);
                                }else{
                                    $price = isset($priceArray['price_tax']) ? $priceArray['price_tax'] : Mage::helper('core')->currencyByStore($item->getFinalPrice(),Mage::app()->getStore()->getId(),true,false);
                                }
                                if(isset($priceArray['regular_price']) && $priceArray['special_price']){
                                    $price = $priceArray['regular_price'].', '.$priceArray['special_price'];
                                }
                            }else{
                                if(Mage::getVersion()=="1.9.1.1"){    
                                    $price = $this->currencyByStore($item->getData($option), Mage::app()->getStore()->getId(), true, false);
                                }else{
                                    $price = Mage::helper('core')->currencyByStore($item->getData($option), Mage::app()->getStore()->getId(), true, false);
                                }
                            }
                        
                        $lines = explode("\n", $this->getWrappedText($price, $valueFont, $this->attributeValueFontSize, $this->gridProductImageSize - $head_text_width));
                    } 
                    else if($option == 'tier_price'){
                                            $tirePrices = $printHelper->getTirePrices($item);
                                            if(count($tirePrices) <= 0){
                                                continue;
                                            }
                                            /* Tire prices block start */
                                            $heading = $AttributeLabels[$option] . ': ';
                                            $head_text_width = $this->widthForStringUsingFontSize($heading, $lableFont, $this->attributeHeadingFontSize);
                                            
                                            switch($this->labelFontStyle){
                                                case 'regular':
                                                                $this->_setFontRegular($page, $this->attributeHeadingFontSize);
                                                                break;
                                                case 'bold':
                                                            $this->_setFontBold($page, $this->attributeHeadingFontSize);
                                                            break;
                                                case 'italic':
                                                              $this->_setFontItalic($page, $this->attributeHeadingFontSize);
                                                              break;
                                                case 'italicbold':
                                                                $this->_setFontItalicbold($page, $this->attributeHeadingFontSize);
                                                                  break;
                                            }
                                            $page->setFillColor(Zend_Pdf_Color_Html::color($this->labelFontColor));
                                            $page->drawText(trim($heading), $x, $this->y, 'UTF-8');
                                            
                                            if(count($tirePrices) > 0){
                                                
                                                $lines =  explode("\n", $this->getWrappedText(implode(", ",$tirePrices), $valueFont, $this->attributeFontSize, $this->gridProductImageSize - $head_text_width));       
                                                
                                           }

                                           /* Tire prices block ends */
                                        }
                    else {

                        if (!isset($type[$option])) {
                            $attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', $option);
                            if ($attribute)
                                $type[$option] = $attribute->getSource()->getAttribute()->getFrontendInput();
                        }

                        if ($option != $nameAttribute) {
                            $font = $valueFont;
                            if ($type[$option] == 'textarea') {
                                
                                $limitChars = (int)Mage::getStoreConfig('catalogprint/product_grid/limit_chars');
                                $text = str_replace(array("\r\n", "\r", "\n"), '', trim(strip_tags($item->getData($option))));
                                  
                                    $heading = $AttributeLabels[$option] . ': ';
                                    $head_text_width = $this->widthForStringUsingFontSize($heading, $lableFont, $this->attributeHeadingFontSize);
                                    
                                    switch($this->labelFontStyle){
                                                case 'regular':
                                                                $this->_setFontRegular($page, $this->attributeHeadingFontSize);
                                                                break;
                                                case 'bold':
                                                            $this->_setFontBold($page, $this->attributeHeadingFontSize);
                                                            break;
                                                case 'italic':
                                                              $this->_setFontItalic($page, $this->attributeHeadingFontSize);
                                                              break;
                                                case 'italicbold':
                                                                $this->_setFontItalicbold($page, $this->attributeHeadingFontSize);
                                                                  break;
                                            }
                                            $page->setFillColor(Zend_Pdf_Color_Html::color($this->labelFontColor));
                                    $page->drawText(trim($heading), $x, $this->y, 'UTF-8');
                                    
                                    if($limitChars == '' || $limitChars <= 0){
                                        $lines = explode("\n", $this->getWrappedText($text, $valueFont, $this->attributeValueFontSize, $this->gridProductImageSize - $head_text_width));
                                    }else{
                                        if($this->gridDisplayReadMore){
                                            $textWithReadmore = strlen($text) > $limitChars ? substr($text,0,$limitChars).'...'.$readMoreWithoutSpace.'': $text;
                                            $readMoreTextWidth = $this->widthForStringUsingFontSize('...'.$readMoreWithoutSpace.'', $font, $this->attributeFontSize);
                                        }else{
                                            $textWithReadmore = strlen($text) > $limitChars ? substr($text,0,$limitChars).'...': $text;
                                            $readMoreTextWidth = 0;
                                        }
                                        $lines = explode("\n", $this->getWrappedText($textWithReadmore, $valueFont, $this->attributeValueFontSize, $this->gridProductImageSize - $head_text_width));
                                    }
                                
                            } else {
                                if ($type[$option] == 'select' || $type[$option] == 'multiselect') {
                                    $heading = $AttributeLabels[$option] . ': ';
                                    $head_text_width = $this->widthForStringUsingFontSize($heading, $lableFont, $this->attributeHeadingFontSize);
                                    switch($this->labelFontStyle){
                                                case 'regular':
                                                                $this->_setFontRegular($page, $this->attributeHeadingFontSize);
                                                                break;
                                                case 'bold':
                                                            $this->_setFontBold($page, $this->attributeHeadingFontSize);
                                                            break;
                                                case 'italic':
                                                              $this->_setFontItalic($page, $this->attributeHeadingFontSize);
                                                              break;
                                                case 'italicbold':
                                                                $this->_setFontItalicbold($page, $this->attributeHeadingFontSize);
                                                                  break;
                                            }
                                            $page->setFillColor(Zend_Pdf_Color_Html::color($this->labelFontColor));
                                    $page->drawText(trim($heading), $x, $this->y, 'UTF-8');
                                    $this->_setFontRegular($page, $this->attributeValueFontSize);
                                    $optVal = is_array($item->getAttributeText($option)) ? implode(", ",$item->getAttributeText($option)): $item->getAttributeText($option);
                                    $lines = explode("\n", $this->getWrappedText($optVal, $valueFont, $this->attributeValueFontSize, $this->gridProductImageSize - $head_text_width));
                                } else {
                                    $text = $item->getData($option);
                                    
                                    $heading = $AttributeLabels[$option] . ': ';
                                    $head_text_width = $this->widthForStringUsingFontSize($heading, $lableFont, $this->attributeHeadingFontSize);
                                    switch($this->labelFontStyle){
                                                case 'regular':
                                                                $this->_setFontRegular($page, $this->attributeHeadingFontSize);
                                                                break;
                                                case 'bold':
                                                            $this->_setFontBold($page, $this->attributeHeadingFontSize);
                                                            break;
                                                case 'italic':
                                                              $this->_setFontItalic($page, $this->attributeHeadingFontSize);
                                                              break;
                                                case 'italicbold':
                                                                $this->_setFontItalicbold($page, $this->attributeHeadingFontSize);
                                                                  break;
                                            }
                                            $page->setFillColor(Zend_Pdf_Color_Html::color($this->labelFontColor));
                                    $page->drawText(trim($heading), $x, $this->y, 'UTF-8');
                                    
                                    $lines = explode("\n", $this->getWrappedText($text, $valueFont, $this->attributeValueFontSize, $this->gridProductImageSize - $head_text_width));
                                    
                                }
                            }
                        } else {
                            $head_text_width = 0;
                            $font = $titleFont;
                            $lines = explode("\n", $this->getWrappedText($item->getData($option), $titleFont, $this->attributeFontSize, $this->gridProductImageSize));
                        }
                    }
                    $tmpOpt = '';
                    $stack = array();
                    $tmpOpt = $lines[0];
                    unset($lines[0]);
                    $stack = implode("\n ",$lines);
                                    
                    $lines = explode("\n",$this->getWrappedText(str_replace(array("\n"),"",$stack),$font, $this->attributeFontSize, $this->gridProductImageSize + $this->selectedLayout['padding']));
                    array_unshift($lines, $tmpOpt);
                    $readMoreExists = false;
                    if ($option == $nameAttribute) {
                        switch($this->titleFontStyle){
                                                case 'regular':
                                                                $this->_setFontRegular($page, $this->attributeHeadingFontSize);
                                                                break;
                                                case 'bold':
                                                            $this->_setFontBold($page, $this->attributeHeadingFontSize);
                                                            break;
                                                case 'italic':
                                                              $this->_setFontItalic($page, $this->attributeHeadingFontSize);
                                                              break;
                                                case 'italicbold':
                                                                $this->_setFontItalicbold($page, $this->attributeHeadingFontSize);
                                                                  break;
                                            }
                                            
                    }else{
                        switch($this->valueFontStyle){
                                                case 'regular':
                                                                $this->_setFontRegular($page, $this->attributeValueFontSize);
                                                                break;
                                                case 'bold':
                                                            $this->_setFontBold($page, $this->attributeValueFontSize);
                                                            break;
                                                case 'italic':
                                                              $this->_setFontItalic($page, $this->attributeValueFontSize);
                                                              break;
                                                case 'italicbold':
                                                                $this->_setFontItalicbold($page, $this->attributeValueFontSize);
                                                                  break;
                                            }
                                            
                    }
                    foreach ($lines as $i => $line) {
                        if(strlen($line) <= 0){
                            continue;
                        }
                        
                        $stringWidth = 0;
                        if ($i != sizeof($lines) - 1 && sizeof($lines) != 2 && $i != 0) {
                            $this->y -= $this->attributeFontSize;
                            $flag = 1;
                        }

                        if ($option == $nameAttribute) {
                            
                            $page->setFillColor(Zend_Pdf_Color_Html::color($this->titleFontColor));
                            
                        } else {
                            
                            $page->setFillColor(Zend_Pdf_Color_Html::color($this->valueFontColor));
                        }
                        
                        if(substr($line,-strlen('...'.$readMoreWithoutSpace.'')) == '...'.$readMoreWithoutSpace.''){
                            
                            $stringWidth = $this->widthForStringUsingFontSize($line, $valueFont, $this->attributeFontSize);
                            $line = str_replace('...'.$readMoreWithoutSpace.'','',$line);
                            $readMoreExists = true;
                        }
                        if($i == 0){
                            $page->drawText(trim(str_replace(array("\r\n", "\r", "\n"), '', $line)), $x + $head_text_width, $this->y, 'UTF-8');
                        }else{
                            $page->drawText(trim(str_replace(array("\r\n", "\r", "\n"), '', $line)), $x, $this->y, 'UTF-8');
                        }
                        if($readMoreExists){
                            $page->setFillColor(Zend_Pdf_Color_Html::color('#1122CC'));
                            $page->drawText('...'.$readMoreText.'', $x + $stringWidth - $readMoreTextWidth, $this->y, 'UTF-8');
                            $annotation = Zend_Pdf_Annotation_Link :: create($x + $stringWidth - $readMoreTextWidth, $this->y, $x + $stringWidth, $this->y + $this->attributeFontSize, $target);
                            $page->attachAnnotation($annotation);
                        }

                        if ($option == $nameAttribute) {
                            //add link to name
                            $text_width = $this->widthForStringUsingFontSize($line, $font, $this->attributeFontSize);
                            $annotation = Zend_Pdf_Annotation_Link :: create($x, $this->y, $x + $text_width, $this->y + $this->attributeFontSize, $target);
                            $page->attachAnnotation($annotation);
                        }
                    }
                    if ($option == $nameAttribute) {
                        $page->setLineColor(Zend_Pdf_Color_Html::color($this->gridBorderColor));
                        $page->drawLine($x, $this->y - 5, $x + $this->gridProductImageSize, $this->y - 5);
                        $page->setLineColor(Zend_Pdf_Color_Html::color($this->gridBorderColor));
                    }
                    unset($lines);
                }
                unset($target);
            }

            $x = $x + $this->product_grid_total_width;
            $this->y = $tmp;

            if ($x >= 500) {
                $x = $this->marginLeft;

                $this->y -= $this->product_grid_total_height + $this->product_grid_bottom_margin;
            }

            //write progress
            $stringData = ($this->k * 100) / $this->_totalProducts;
            if ($stringData < '100')
                file_put_contents($this->_logFile, $stringData);
        }
    }

    protected function _drawPageHeader($page, $drawPageNumber = true) {
        $x = $this->marginLeft;
        
        $this->y = $page->getHeight() - $this->marginTop;

        //draw page number
        if ($drawPageNumber) {
            $font = $this->_setFontBold($page, $this->pageNumberFontSize);
            $page->setFillColor(Zend_Pdf_Color_Html::color('black'));

            $feed = $x;
            if ($this->pageNumber % 2 != 0) {
                $feed = $x + $page->getWidth() - $this->marginLeft - $this->marginLeft - 5;
            }
            //$feed = $x + $page->getWidth() - $this->marginLeft - $this->marginLeft - 5;
            $page->drawText($this->pageNumber, $feed, $this->y - $this->pageNumberFontSize, 'UTF-8');
        }

        $this->y -= $this->pageNumberFontSize + 20;
        $this->pageNumber++;
    }

    protected function _drawPageFooter($page) {
        $x = $this->marginLeft;
        $steps = 0;
        //draw footer line
        $y = $this->pageFooterFontSize + $this->pageDateFontSize + 5 + 10 + $this->footerSteps;
        $x1 = $x + $page->getWidth() - $this->marginLeft - $this->marginLeft;
        $page->drawLine($x, $y, $x1, $y);

        //draw footer text added by ktplDev
        $font = $this->_setFontRegular($page, $this->pageFooterFontSize);
        
        $text = 'Protected by Copyright ' . date('Y') . '. Property of ' . Mage::getStoreConfig('general/store_information/name') . '.  Tel: ' . Mage::getStoreConfig('general/store_information/phone') . '. Email: ' . Mage::getStoreConfig('trans_email/ident_general/email');
        $page->drawText($text, $x, $this->pageFooterFontSize + $this->pageDateFontSize + 5 + $this->footerSteps, 'UTF-8');

        // footer date format added by ktplDev
         
        //draw current date
        $font = $this->_setFontRegular($page, $this->pageDateFontSize);
        if ($this->displayDate) {
            $date = date('F d, Y');
            $date_width = $this->widthForStringUsingFontSize($date, $font, $this->pageFooterFontSize);
            $page->drawText($date, $x1 - $date_width, $this->pageFooterFontSize + $this->pageDateFontSize + 5 + $this->footerSteps, 'UTF-8');
        }
        
        foreach($this->footerLines as $footerLine){
            $page->drawText($footerLine, $x, $this->pageFooterFontSize + $this->pageDateFontSize - $steps, 'UTF-8');
            $steps += 10;
        }
    }

    protected function getMemoryManager() {
        if ($this->_memoryManager == null) {
            $backendOptions = array(
                'cache_dir' => Mage::getBaseDir('base') . DS . 'var' . DS . 'cache' . DS
            );
            $this->_memoryManager = Zend_Memory::factory('File', $backendOptions);
            $this->_memoryManager->setMemoryLimit('3221225472');
        }
        return $this->_memoryManager;
    }

    protected function drawTableOfContents($collection) {
        $helper = Mage::helper('catalogprint');
        $table_of_contents = $this->newPage(array('displayHeaderFooter'=>false,'displayPageNumber'=>false));
        $table_of_contents->setFillColor(Zend_Pdf_Color_Html::color('black'));
        $font = $this->_setFontRegular($table_of_contents, $this->headerfontSize);
        $this->y = $table_of_contents->getHeight() - $this->marginTop;

        $this->y -= $this->marginTop;
        $feed = $this->getAlignCenter($helper->__('Table of Contents'), $this->marginLeft, $table_of_contents->getWidth() - $this->marginLeft, $font, $this->headerfontSize);
        $table_of_contents->drawText($helper->__('Table of Contents'), $feed, $this->y, 'UTF-8');
        $this->y -= $this->headerfontSize;

        $this->startTableOfContentsIndex = count($this->_getPdf()->pages);
        $paddedCollection = array();
        if(!$this->isRoot){
            $paddedCollection['child'][] = $collection;
            $this->_drawTable($paddedCollection, $table_of_contents);
        }else{
            $this->_drawTable($collection, $table_of_contents);
        }
        
        $this->endTableOfContentsIndex = count($this->_getPdf()->pages);

    }

    protected function _drawTable($collection, &$page) {
        if(count($collection['child']) > 0){
        $x = $this->marginLeft;

        foreach ($collection['child'] as $c) {

            if (is_array($c)) {

                if ($this->y - 50 < 0) {
                    $page = $this->newPage(array('displayHeaderFooter'=>false,'displayPageNumber'=>false));
                    $x = $this->marginLeft;
                    $this->y -= 20;
                    $font = $this->_setFontRegular($page, $this->fontSize);
                }

                if ($c['level'] == 1) {

                    if ($this->tmpTableOfContentsFlag != 0) {
                        $x = $this->marginLeft;
                        $font = $this->_setFontRegular($page, $this->fontSize);
                    }

                    $this->y -= $this->marginTop;
                    $font = $this->_setFontRegular($page, $this->headerfontSize);
                    $page->drawText($c['name'], $x, $this->y, 'UTF-8');
                    $pageNo = (!isset($this->tmpArray[$c['name'].$c['id']])) ? $this->tmpArray[$c['child'][0]['name'].$c['child'][0]['id']]: $this->tmpArray[$c['name'].$c['id']];
                    $feed = $this->getAlignRight($pageNo, $this->marginLeft, $page->getWidth() - 40, $font, $this->headerfontSize);
                    
                    $page->drawText($pageNo, $feed, $this->y, 'UTF-8');
                    $this->drawCenterLine($page, $c['name'], $pageNo, $font, $x, $feed, $this->headerfontSize);
                    

                    $target = Zend_Pdf_Outline::create($c['name'], $this->_getPdf()->getNamedDestination($c['name'].$c['id']));
                    $target = $target->getTarget();
                    if (isset($target)) {
                        $annotation = Zend_Pdf_Annotation_Link :: create($this->marginLeft, $this->y, $page->getWidth() - 40, $this->y + $this->headerfontSize, $target);
                        $page->attachAnnotation($annotation);
                    }

                    $this->y -= $this->headerfontSize + 5;
                    $font = $this->_setFontRegular($page, $this->fontSize);
                    //set flag to create new page on each level 2 category
                    $this->tmpTableOfContentsFlag = 1;
                } else {

                    //set level 3 as bold
                    if ($c['level'] == 2) {
                        $font = $this->_setFontBold($page, $this->fontSize);
                    } else {
                        $font = $this->_setFontRegular($page, $this->fontSize);
                    }

                    //get padding
                    $feed = $c['level'];
                    $x = $this->marginLeft;

                    while ($feed > 2) {
                        $x += 20;
                        $feed--;
                    }


                    $page->drawText($c['name'], $x, $this->y, 'UTF-8');

                    $feed = $this->getAlignRight($this->tmpArray[$c['name'].$c['id']], $this->marginLeft, $page->getWidth() - 40, $font, $this->fontSize);
                    $page->drawText($this->tmpArray[$c['name'].$c['id']], $feed, $this->y, 'UTF-8');

                    $this->drawCenterLine($page, $c['name'], $this->tmpArray[$c['name'].$c['id']], $font, $x, $feed, $this->fontSize);

                    $target = Zend_Pdf_Outline::create($c['name'], $this->_getPdf()->getNamedDestination($c['name'].$c['id']));
                    $target = Zend_Pdf_Outline::create($c['name'], $this->_getPdf()->getNamedDestination($c['name'].$c['id']));
                    $target = $target->getTarget();
                    if (isset($target)) {
                        $annotation = Zend_Pdf_Annotation_Link :: create($this->marginLeft, $this->y, $page->getWidth() - 40, $this->y + $this->fontSize, $target);
                        $page->attachAnnotation($annotation);
                    }
                    $this->y -= $this->fontSize + 5;
                }
                $this->_drawTable($c, $page);
            }
        }
        }
    }

    protected function drawCenterLine($page, $catName, $pageNumber, $font, $x, $feed, $fontsize) {
        $width = $this->widthForStringUsingFontSize($catName, $font, $fontsize) + 3;
        $x1 = $feed - $x - $width - 5;
        $one_dot_width = $this->widthForStringUsingFontSize('.', $font, $fontsize);

        $x = $x + $width;
        $text = '';
        for ($i = 0; $i < ($x1 / $one_dot_width); $i++)
            $text .= '.';
        $page->drawText($text, $x, $this->y, 'UTF-8');
    }

    protected function drawIndexes() {
        if (array_key_exists('mfg_asc', $this->collection)) {
            $this->_drawIndex('manufacturerNo', 'Index By Product SKU');
        }
        if (array_key_exists('part_asc', $this->collection)) {
            $this->_drawIndex('partDesc', 'Index By Product Name');
        }
    }

    protected function _drawIndex($sortBy, $title) {
        $helper = Mage::helper('catalogprint');
        $page = $this->newPage(array('displayHeaderFooter'=>true,'displayPageNumber'=>false));
        $page->setFillColor(Zend_Pdf_Color_Html::color('black'));
        $font = $this->_setFontRegular($page, $this->headerfontSize);
        $this->y = $page->getHeight() - $this->marginTop;

        $this->y -= $this->marginTop;
        $feed = $this->getAlignCenter($helper->__($title), $this->marginLeft, $page->getWidth() - $this->marginLeft, $font, $this->headerfontSize);
        $page->drawText($helper->__($title), $feed, $this->y, 'UTF-8');
        $this->y -= $this->headerfontSize + 20;

        $x = $this->marginLeft;

        //initialize variables
        $partDescColumnWidth = 130;
        $mfgNoColumnWidth = 100;
        $pageNoColumnWidth = 10;
        $spaceBetweenTwoColumns = 5;

        $tmp_y = $this->y;
        $index = 0;

        //draw column headings
        $this->drawIndexColumnHeadings($page, $sortBy, $x, $this->y, $partDescColumnWidth, $mfgNoColumnWidth, $pageNoColumnWidth, $spaceBetweenTwoColumns);
        $this->y -= $this->indexFontSize + 10;

        $page->drawLine($page->getWidth() / 2, $this->marginBottom + $this->pageFooterFontSize + 20, $page->getWidth() / 2, $this->y + $this->indexFontSize);
        $font = $this->_setFontRegular($page, $this->indexFontSize);

        if (!empty($this->manufacturerNumbers) && !empty($sortBy)) {
            $this->aasort($this->manufacturerNumbers, $sortBy);

            foreach ($this->manufacturerNumbers as $value) {

                if (is_array($value)) {

                    $lines = explode("\n", $this->getWrappedText($value['partDesc'], $font, $this->indexFontSize, $partDescColumnWidth));
                    if ($this->y - ($this->marginTop + $this->marginBottom + ((sizeof($lines)) * 10)) < 0) {
                        if (($index + 1) % 2 == 0) {
                            $page = $this->newPage(array('displayHeaderFooter'=>true,'displayPageNumber'=>false));
                            $font = $this->_setFontRegular($page, $this->indexFontSize);
                            $x = $this->marginLeft;
                            $page->drawLine($page->getWidth() / 2, $this->marginBottom + $this->pageFooterFontSize + 10, $page->getWidth() / 2, $this->y + $this->indexFontSize);
                        } else {
                            $x = ($page->getWidth() / 2) + 10;

                            $this->y = $tmp_y;
                        }

                        //draw column headings
                        $this->drawIndexColumnHeadings($page, $sortBy, $x, $this->y, $partDescColumnWidth, $mfgNoColumnWidth, $pageNoColumnWidth, $spaceBetweenTwoColumns);
                        $this->y -= $this->indexFontSize + 10;

                        $font = $this->_setFontRegular($page, $this->indexFontSize);

                        $index++;
                    }

                    $tmp = $this->y;

                    if ($sortBy == 'manufacturerNo') {
                        $page->drawText($value['manufacturerNo'], $x, $tmp, 'UTF-8');
                        foreach ($lines as $i => $line) {
                            if ($i != 0) {
                                $this->y -= 15;
                            }
                            //draw part desc
                            $page->drawText($line, $x + $mfgNoColumnWidth +
                                    $spaceBetweenTwoColumns, $this->y, 'UTF-8');
                        }
                    } else {
                        foreach ($lines as $i => $line) {
                            if ($i != 0) {
                                $this->y -= 15;
                            }
                            //draw part desc
                            $page->drawText($line, $x, $this->y, 'UTF-8');
                        }
                        $page->drawText($value['manufacturerNo'], $x + $partDescColumnWidth +
                                $spaceBetweenTwoColumns, $tmp, 'UTF-8');
                    }

                    $page->drawText($value['pageNo'] - 1, $x + $mfgNoColumnWidth +
                            $spaceBetweenTwoColumns + $partDescColumnWidth + $spaceBetweenTwoColumns, $tmp, 'UTF-8');

                    $target = Zend_Pdf_Outline::create($value['manufacturerNo'], $this->_getPdf()->getNamedDestination($value['manufacturerNo']));
                    $target = $target->getTarget();
                    if (isset($target)) {
                        $annotation = Zend_Pdf_Annotation_Link :: create($x, $tmp + $this->indexFontSize, $x +
                                        $mfgNoColumnWidth + $spaceBetweenTwoColumns + $partDescColumnWidth +
                                        $spaceBetweenTwoColumns + $pageNoColumnWidth, $this->y + $this->indexFontSize, $target);
                        $page->attachAnnotation($annotation);
                    }

                    $this->y -= $this->indexVerticalSpacing;
                }
            }
        }
    }

    protected function drawIndexColumnHeadings(&$page, $sortBy, $x, $y, $partDescColumnWidth, $mfgNoColumnWidth, $pageNoColumnWidth, $spaceBetweenTwoColumns) {
        $helper = Mage::helper('catalogprint');
        $font = $this->_setFontBold($page, $this->indexFontSize);
        if ($sortBy == 'manufacturerNo') {
            $page->drawText($helper->__('Sku'), $x, $y, 'UTF-8');
            $page->drawText($helper->__('Description'), $x + $mfgNoColumnWidth +
                    $spaceBetweenTwoColumns, $y, 'UTF-8');
            $page->drawText($helper->__('Pg No.'), $x + $mfgNoColumnWidth + $spaceBetweenTwoColumns +
                    $partDescColumnWidth + $spaceBetweenTwoColumns, $y, 'UTF-8');
        } else {
            $page->drawText($helper->__('Description'), $x, $y, 'UTF-8');
            $page->drawText($helper->__('Sku'), $x + $partDescColumnWidth +
                    $spaceBetweenTwoColumns, $y, 'UTF-8');
            $page->drawText($helper->__('Pg No.'), $x + $partDescColumnWidth + $spaceBetweenTwoColumns +
                    $mfgNoColumnWidth + $spaceBetweenTwoColumns, $y, 'UTF-8');
        }
    }

    protected function aasort(&$array, $key) {
        $sorter = array();
        $ret = array();
        $pageArray = array();
        reset($array);
        foreach ($array as $ii => $va) {
            $sorter[$ii] = $va[$key];
            $pageArray[$ii] = $va['pageNo'];
        }
        asort($sorter);
        $Array1 = array_unique(array_values($sorter));
        $tmpArray = array();
        $finalArray = array();
        foreach ($Array1 as $Va) {
            foreach ($sorter as $ii => $va) {
                if ($va == $Va) {
                    $tmpArray[$ii] = $pageArray[$ii];
                }
            }
            asort($tmpArray);
            foreach ($tmpArray as $ii => $pgno) {
                $finalArray[$ii] = $sorter[$ii];
            }
            $tmpArray = array();
        }
        $sorter = $finalArray;
        foreach ($sorter as $ii => $va) {
            $ret[$ii] = $array[$ii];
        }
        $array = $ret;
    }

    /**
     * Set font as regular
     *
     * @param  Zend_Pdf_Page $object
     * @param  int $size
     * @return Zend_Pdf_Resource_Font
     */
    protected function _setFontRegular($object, $size = 7) {
        //$font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
        if (Mage::getStoreConfig('catalogprint/fonts/regular_font') == "") {
            $font = Zend_Pdf_Font::fontWithPath(Mage::getBaseDir() . '/lib/CalibriFont/Calibri.ttf');
        } else {
            $font = Zend_Pdf_Font::fontWithPath(Mage::getBaseDir() . Mage::getStoreConfig('catalogprint/fonts/regular_font'));
        }
        $object->setFont($font, $size);
        return $font;
    }

    /**
     * Set font as bold
     *
     * @param  Zend_Pdf_Page $object
     * @param  int $size
     * @return Zend_Pdf_Resource_Font
     */
    protected function _setFontBold($object, $size = 7) {

        //$font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD);
        if (Mage::getStoreConfig('catalogprint/fonts/bold_font') == "") {
            $font = Zend_Pdf_Font::fontWithPath(Mage::getBaseDir() . '/lib/CalibriFont/Calibri_Bold.ttf');
        } else {
            $font = Zend_Pdf_Font::fontWithPath(Mage::getBaseDir() . Mage::getStoreConfig('catalogprint/fonts/bold_font'));
        }
        $object->setFont($font, $size);
        return $font;
    }

    /**
     * Set font as italic
     *
     * @param  Zend_Pdf_Page $object
     * @param  int $size
     * @return Zend_Pdf_Resource_Font
     */
    protected function _setFontItalic($object, $size = 7) {

        //$font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD);
        if (Mage::getStoreConfig('catalogprint/fonts/italic_font') == "") {
            $font = Zend_Pdf_Font::fontWithPath(Mage::getBaseDir() . '/lib/CalibriFont/Calibri_Italic.ttf');
        } else {
            $font = Zend_Pdf_Font::fontWithPath(Mage::getBaseDir() . Mage::getStoreConfig('catalogprint/fonts/italic_font'));
        }
        $object->setFont($font, $size);
        return $font;
    }

    /**
     * Set font as italicbold
     *
     * @param  Zend_Pdf_Page $object
     * @param  int $size
     * @return Zend_Pdf_Resource_Font
     */
    protected function _setFontItalicbold($object, $size = 7) {
        if (Mage::getStoreConfig('catalogprint/fonts/italicbold_font') == "") {
            $font = Zend_Pdf_Font::fontWithPath(Mage::getBaseDir() . '/lib/md-catalog-print/Calibri_Bold_Italic.ttf');
        } else {
            $font = Zend_Pdf_Font::fontWithPath(Mage::getBaseDir() . Mage::getStoreConfig('catalogprint/fonts/italicbold_font'));
        }
       
        $object->setFont($font, $size);
        return $font;
    }
     /* override from Mage_Core_Helper_Data. This function  does not exist in 1.9 enterprise at Mage_Core_Helper_Data thats why add here for work in all versions. */
    public static function currencyByStore($value, $store = null, $format = true, $includeContainer = true)
    {
        try {
            if (!($store instanceof Mage_Core_Model_Store)) {
                $store = Mage::app()->getStore($store);
            }

            $value = $store->convertPrice($value, $format, $includeContainer);
        }
        catch (Exception $e){
            $value = $e->getMessage();
        }

        return $value;
    }

}
