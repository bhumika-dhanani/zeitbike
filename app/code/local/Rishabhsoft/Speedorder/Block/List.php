<?php
class Rishabhsoft_Speedorder_Block_List extends Mage_Catalog_Block_Product_List
{
    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {
            $layer = $this->getLayer();
            /* @var $layer Mage_Catalog_Model_Layer */
            if ($this->getShowRootCategory()) {
                $this->setCategoryId(Mage::app()->getStore()->getRootCategoryId());
            }

            // if this is a product view page
            if (Mage::registry('product')) {
                // get collection of categories this product is associated with
                $categories = Mage::registry('product')->getCategoryCollection()
                    ->setPage(1, 1)
                    ->load();
                // if the product is associated with any category
                if ($categories->count()) {
                    // show products from this category
                    $this->setCategoryId(current($categories->getIterator()));
                }
            }

            $origCategory = null;
            if ($this->getCategoryId()) {
                $category = Mage::getModel('catalog/category')->load($this->getCategoryId());
                if ($category->getId()) {
                    $origCategory = $layer->getCurrentCategory();
                    $layer->setCurrentCategory($category);
                    $this->addModelTags($category);
                }
            }
            $this->_productCollection = $layer->getProductCollection();

            $this->prepareSortableFieldsByCategory($layer->getCurrentCategory());

            if ($origCategory) {
                $layer->setCurrentCategory($origCategory);
            }
        }
        if($this->getRequest()->getParam('search_query') != ''){
            $searchQuery = $this->getRequest()->getParam('search_query');
            if(strpos($searchQuery," ") !== false){
                $searchQueryarray = explode(" ",$searchQuery);
            }

            if(!empty($searchQueryarray)){
                $finalquery[] = array('attribute' => 'sku', 'like' => '%'.$searchQuery.'%');
                foreach ($searchQueryarray as $queryFind){
                    $finalquery[] = array('attribute' => 'name', 'like' => '%'.$queryFind.'%');
                }
            }else{
                $finalquery[] = array('attribute' => 'sku', 'like' => '%'.$searchQuery.'%');
                $finalquery[] = array('attribute' => 'name', 'like' => '%'.$searchQuery.'%');
            }

            $this->_productCollection->addAttributeToFilter(
                $finalquery

            );
        }
        $this->_productCollection->addAttributeToSort('sku', 'ASC');

        return $this->_productCollection;
    }
}