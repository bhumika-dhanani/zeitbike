<?php
class Rishabhsoft_AdvanceImport_Model_Convert_Adapter_Abstract
    extends Mage_Catalog_Model_Convert_Adapter_Product
{
    protected $_galleryBackendModel;
    public function __construct()
    {
        parent::__construct();
        $this->_galleryBackendModel = $this->getAttribute('media_gallery')->getBackend();
    }

    public function getProductModel()
    {

        if (is_null($this->_productModel)) {
            $productModel = Mage::getModel('advanceimport/product');
            $this->_productModel = Mage::objects()->save($productModel);
        }
        return Mage::objects()->load($this->_productModel);
    }

    public function saveRow(array $importData)
    {

		$product = $this->getProductModel()
            ->reset();

        if (empty($importData['store'])) {
            if (!is_null($this->getBatchParams('store'))) {
                $store = $this->getStoreById($this->getBatchParams('store'));
            } else {
                $message = Mage::helper('catalog')->__('Skip import row, required field "%s" not defined', 'store');
                Mage::throwException($message);
            }
        }
        else {
            $store = $this->getStoreByCode($importData['store']);
        }

        if ($store === false) {
            $message = Mage::helper('catalog')->__('Skip import row, store "%s" field not exists', $importData['store']);
            Mage::throwException($message);
        }

        if (empty($importData['sku'])) {
            $message = Mage::helper('catalog')->__('Skip import row, required field "%s" not defined', 'sku');
            Mage::throwException($message);
        }
        $product->setStoreId($store->getId());
        $productId = $product->getIdBySku($importData['sku']);

        if ($productId) {
            $product->load($productId);
        }
        else {
            $productTypes = $this->getProductTypes();
            $productAttributeSets = $this->getProductAttributeSets();

            /**
             * Check product define type
             */
            if (empty($importData['type']) || !isset($productTypes[strtolower($importData['type'])])) {
                $value = isset($importData['type']) ? $importData['type'] : '';
                $message = Mage::helper('catalog')->__('Skip import row, is not valid value "%s" for field "%s"', $value, 'type');
                Mage::throwException($message);
            }
            $product->setTypeId($productTypes[strtolower($importData['type'])]);
            /**
             * Check product define attribute set
             */
            if (empty($importData['attribute_set']) || !isset($productAttributeSets[$importData['attribute_set']])) {
                $value = isset($importData['attribute_set']) ? $importData['attribute_set'] : '';
                $message = Mage::helper('catalog')->__('Skip import row, is not valid value "%s" for field "%s"', $value, 'attribute_set');
                Mage::throwException($message);
            }
            $product->setAttributeSetId($productAttributeSets[$importData['attribute_set']]);

            foreach ($this->_requiredFields as $field) {
                $attribute = $this->getAttribute($field);
                if (!isset($importData[$field]) && $attribute && $attribute->getIsRequired()) {
                    $message = Mage::helper('catalog')->__('Skip import row, required field "%s" for new products not defined', $field);
                    Mage::throwException($message);
                }
            }
        }

        // process row with media data only
        if (isset($importData['_media_image']) && strlen($importData['_media_image'])) {
            $this->saveImageDataRow($product, $importData);
            return true;
        }

        $this->setProductTypeInstance($product);

		/* Rishabhsoft Configurable Product Import Function */

		$configurableImport = $this->getImportType('configurable');
		if(!is_object($configurableImport)){
			//echo 'Class Not Found';
		}else{
			$configurableImport->import($importData,$product);
		}

		/* Rishabhsoft Configurable Product Import Function */

		/* Rishabhsoft Related Products Import Function */

		$relatedImport = $this->getImportType('related');
		if(!is_object($relatedImport)){
			//echo 'Related Class Not Found';
		}else{
			$relatedImport->import($importData,$product);
		}
		/* Rishabhsoft Related Products Import Function */
		/* Rishabhsoft Upsell Products Import Function */

		$upsellImport = $this->getImportType('upsell');
		if(!is_object($upsellImport)){
			//echo 'Upsell Class Not Found';
		}else{
			$upsellImport->import($importData,$product);
		}
		/* Rishabhsoft Upsell Products Import Function */
		/* Rishabhsoft crosssell Products Import Function */

		$crosssellImport = $this->getImportType('crosssell');
		if(!is_object($crosssellImport)){
			//echo 'Crosssell Class Not Found';
		}else{
			$crosssellImport->import($importData,$product);
		}
		/* Rishabhsoft crosssell Products Import Function */
		/* Rishabhsoft grouped Products Import Function */

		$groupedImport = $this->getImportType('grouped');
		if(!is_object($groupedImport)){
			//echo 'grouped Class Not Found';
		}else{
			$groupedImport->import($importData,$product);
		}
		/* Rishabhsoft grouped Products Import Function */
		/* Rishabhsoft tierprice Products Import Function */

		$tierpriceImport = $this->getImportType('tierprice');

		if(!is_object($tierpriceImport)){
			//echo 'tierprice Class Not Found';
		}else{
			$tierpriceImport->import($importData,$product);
		}

		/*  tierprice Products Import Function */

        $gallaryImport = $this->getImportType('imagegallery');

        if(!is_object($gallaryImport))
        {

        }
        else
        {
            $gallaryImport->import($importData,$product);
        }

        if (isset($importData['category_ids'])) {
            $product->setCategoryIds($importData['category_ids']);
        }

        foreach ($this->_ignoreFields as $field) {
            if (isset($importData[$field])) {
                unset($importData[$field]);
            }
        }

        if ($store->getId() != 0) {
            $websiteIds = $product->getWebsiteIds();
            if (!is_array($websiteIds)) {
                $websiteIds = array();
            }
            if (!in_array($store->getWebsiteId(), $websiteIds)) {
                $websiteIds[] = $store->getWebsiteId();
            }
            $product->setWebsiteIds($websiteIds);
        }

        if (isset($importData['websites'])) {
            $websiteIds = $product->getWebsiteIds();
            if (!is_array($websiteIds) || !$store->getId()) {
                $websiteIds = array();
            }
            $websiteCodes = explode(',', $importData['websites']);
            foreach ($websiteCodes as $websiteCode) {
                try {
                    $website = Mage::app()->getWebsite(trim($websiteCode));
                    if (!in_array($website->getId(), $websiteIds)) {
                        $websiteIds[] = $website->getId();
                    }
                }
                catch (Exception $e) {}
            }
            $product->setWebsiteIds($websiteIds);
            unset($websiteIds);
        }
        $custom_options = array();
        $bundle_options = array();
        $bundle_selections = array();
        //$iii=0;
		$i=0;

        foreach ($importData as $field => $value) {
            if (in_array($field, $this->_inventoryFields)) {
                continue;
            }
            if (is_null($value)) {
                continue;
            }
           /* if (in_array($field, $this->_imageFields)) {
                continue;
            }*/
            $attribute = $this->getAttribute($field);
            if (!$attribute) {
			    if(strpos($field,'$')!==FALSE && strlen($value))
                {
                    $CO_BI = explode('$',$field );
                    if(strtolower($CO_BI[0])=='co')
					{
						/* Rishabhsoft Product Custom Option Import Function */
						$customoptionImport = $this->getImportType('customoption');
						if(!is_object($customoptionImport)){
							//echo 'CustomOption Class Not Found';
						}else{
							$custom_options[] = $customoptionImport->import($importData,$product,$field,$value);
						}
						/* Rishabhsoft Product Custom Option Import Function */
                    }

					if(strtolower($CO_BI[0])=='bi')
					{
						$bundleImport = $this->getImportType('bundle');
						if(!is_object($bundleImport)){
							//echo 'Bundle Class Not Found';
						}else{
							$returnArray[] = $bundleImport->import($importData,$product,$field,$value,$i);
							$lastArray = end($returnArray);
							//print_r($lastArray);
							foreach($lastArray as $key1=>$value1)
							{
								if($key1 == 'bundle_options'){
									foreach($value1 as $k1=>$v1)
									{
										$bundle_options_array[] = $v1;
									}

								}
								if($key1 == 'bundle_selections'){
									foreach($value1 as $k2=>$v2)
									{
										$bundle_selections_array[] = $v2;
									}
								}

							}
							$product->setBundleOptionsData($bundle_options_array);
							$product->setBundleSelectionsData($bundle_selections_array);
							$product->setCanSaveCustomOptions(true);
							$product->setCanSaveBundleSelections(true);
							$i++;
						}
					}
                }
				continue;
            }
			$isArray = false;
            $setValue = $value;
			if ($attribute->getFrontendInput() == 'multiselect') {
                $value = explode(self::MULTI_DELIMITER, $value);
                $isArray = true;
                $setValue = array();
            }
            if ($value && $attribute->getBackendType() == 'decimal') {
                $setValue = $this->getNumber($value);
            }
            if ($attribute->usesSource())
			{
                $options = $attribute->getSource()->getAllOptions(false);
                if ($isArray) {
                    foreach ($options as $item) {
                        if (in_array($item['label'], $value)) {
                            $setValue[] = $item['value'];
                        }
                    }
                } else {
                    $setValue = false;
                    foreach ($options as $item) {
                        if ($item['label'] == $value) {
                            $setValue = $item['value'];
                        }
                    }
                }
            }
            $product->setData($field, $setValue);

        }


        if (!$product->getVisibility()) {
            $product->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE);
        }

        $stockData = array();
        $inventoryFields = isset($this->_inventoryFieldsProductTypes[$product->getTypeId()])
            ? $this->_inventoryFieldsProductTypes[$product->getTypeId()]
            : array();
        foreach ($inventoryFields as $field) {
            if (isset($importData[$field])) {
                if (in_array($field, $this->_toNumber)) {
                    $stockData[$field] = $this->getNumber($importData[$field]);
                }
                else {
                    $stockData[$field] = $importData[$field];
                }
            }
        }
        $product->setStockData($stockData);


        /* Rishabh Soft update*/
        $mediaGalleryBackendModel = $this->getAttribute('media_gallery')->getBackend();

        $arrayToMassAdd = array();

        foreach ($product->getMediaAttributes() as $mediaAttributeCode => $mediaAttribute) {

            Mage::log($mediaAttributeCode, null, 'import-image.log');

            if (isset($importData[$mediaAttributeCode])) {
                $file = trim($importData[$mediaAttributeCode]);
                if (!empty($file) && !$mediaGalleryBackendModel->getImage($product, $file)) {
                    $arrayToMassAdd[] = array('file' => trim($file), 'mediaAttribute' => $mediaAttributeCode);
                }
            }
        }


        $addedFilesCorrespondence = $mediaGalleryBackendModel->addImagesWithDifferentMediaAttributes(
            $product,
            $arrayToMassAdd, Mage::getBaseDir('media') . DS . 'import',
            false,
            false
        );

        foreach ($product->getMediaAttributes() as $mediaAttributeCode => $mediaAttribute) {
            $addedFile = '';
            if (isset($importData[$mediaAttributeCode . '_label'])) {
                $fileLabel = trim($importData[$mediaAttributeCode . '_label']);
                if (isset($importData[$mediaAttributeCode])) {
                    $keyInAddedFile = array_search($importData[$mediaAttributeCode],
                        $addedFilesCorrespondence['alreadyAddedFiles']);
                    if ($keyInAddedFile !== false) {
                        $addedFile = $addedFilesCorrespondence['alreadyAddedFilesNames'][$keyInAddedFile];
                    }
                }

                if (!$addedFile) {
                    $addedFile = $product->getData($mediaAttributeCode);
                }
                if ($fileLabel && $addedFile) {
                    $mediaGalleryBackendModel->updateImage($product, $addedFile, array('label' => $fileLabel));
                }
            }
        }

        if($importData['group_customerGroupId_price']){
            $groupPriceFormattedData = array();
            $groupPriceData = explode(',',$importData['group_customerGroupId_price']);
            if(is_array($groupPriceData) && !empty($groupPriceData)){
                foreach($groupPriceData as $groupprice){
                    $grouppricePart = explode(':',$groupprice);
                    if(isset($grouppricePart[0]) && isset($grouppricePart[1])){
                        $groupPriceFormattedData[] = array(
                            'website_id' => 0,
                            'cust_group' => $grouppricePart[0],
                            'price' => $grouppricePart[1]
                        );
                    }
                }
                $product->setData('group_price',$groupPriceFormattedData);
            }
        }

        $product->setIsMassupdate(true);
        $product->setExcludeUrlRewrite(true);
        $product->save();
        $this->_addAffectedEntityIds($product->getId());
        $id = "";
        $id = $product->getId();

        //echo $product->getId();

        //$importData['tier_qty_price'];
        if($importData['tier_customerGroupId_qty_price']){
            $tierPriceData = explode(',',$importData['tier_customerGroupId_qty_price']);
            if(is_array($tierPriceData) && !empty($tierPriceData)){
                foreach($tierPriceData as $tierprice){
                    $tierpricePart = explode(':',$tierprice);

                    if(isset($tierpricePart[0]) && isset($tierpricePart[1]) && isset($tierpricePart[2])){
                        $tierPrices[] = array(
                            'customer_group_id' => $tierpricePart[0],
                            'qty'               => $tierpricePart[1],
                            'price'             => $tierpricePart[2],
                            'website_id'        => 0
                        );
                        $tierPriceModel = Mage::getModel('catalog/product_attribute_tierprice_api');
                        $tierPriceModel->update($id, $tierPrices);
                    }
                }
            }
        }




		$customoptionImport = $this->getImportType('customoption');
		if(!is_object($customoptionImport)){
			//echo 'CustomOption Class Not Found';
		}else{
			$customoptionImport->saveCustomOption($product,$custom_options);
		}
        return true;
    }




	public function getImportType($code)
	{
		$importType = Mage::getConfig()->getNode('global/advanceimport/importtype');
		foreach($importType->children() as $key=>$value)
		{
			if($code == $key)
			{
				$class = $value->getClassName();
                    if ($class && ($model = Mage::getModel($class))) {
                        return $model;
                    }else{
						false;
					}
			}
		}
		return false;
	}

	protected function skusToIds($userData,$product) {
		$productIds = array();
		foreach ($this->userCSVDataAsArray($userData) as $oneSku) {
			if (($a_sku = (int)$product->getIdBySku($oneSku)) > 0) {
				parse_str("position=", $productIds[$a_sku]);
			}
		}
		return $productIds;
	}

    /**
     * Silently save product (import)
     *
     * @param array $
     * @return bool
     */
    public function saveRowSilently(array $importData)
    {
        try {
            $result = $this->saveRow($importData);
            return $result;
        }
        catch (Exception $e) {
            return false;
        }
    }

    /**
     * Process after import data
     * Init indexing process after catalog product import
     *
     */
    public function finish()
    {
        /**
         * Back compatibility event
         */
        Mage::dispatchEvent('catalog_product_import_after', array());

        $entity = new Varien_Object();
        Mage::getSingleton('index/indexer')->processEntityAction(
            $entity, self::ENTITY, Mage_Index_Model_Event::TYPE_SAVE
        );
    }
	protected function userCSVDataAsArray($data) {
		return explode(',', str_replace(" ", "", $data));
	}
    public function saveImageDataRow($product, $importData)
    {
        $imageData = array(
            'label'         => $importData['_media_lable'],
            'position'      => $importData['_media_position'],
            'disabled'      => $importData['_media_is_disabled']
        );

        $imageFile = trim($importData['_media_image']);
        $imageFile = ltrim($imageFile, DS);
        $imageFilePath = Mage::getBaseDir('media') . DS . 'import' . DS . $imageFile;

        $updatedFileName = $this->_galleryBackendModel->addImage($product, $imageFilePath, null, false,
            (bool) $importData['_media_is_disabled']);
        $this->_galleryBackendModel->updateImage($product, $updatedFileName, $imageData);

        $this->_addAffectedEntityIds($product->getId());
        $product->setIsMassupdate(true)
            ->setExcludeUrlRewrite(true)
            ->save();

        return $this;
    }
}
