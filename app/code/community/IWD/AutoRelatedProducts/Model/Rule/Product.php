<?php
class IWD_AutoRelatedProducts_Model_Rule_Product extends Mage_CatalogRule_Model_Rule_Condition_Product
{
    protected function _getAttributeValue($object)
    {
        $storeId = $object->getStoreId();
        $defaultStoreId = Mage_Core_Model_App::ADMIN_STORE_ID;

        /*** seance override this ***/
        //$productValues  = isset($this->_entityAttributeValues[$object->getId()]) ? $this->_entityAttributeValues[$object->getId()] : array();
        $productValues = isset($this->_entityAttributeValues[$object->getId()]) ?
            $this->_entityAttributeValues[$object->getId()] : array($defaultStoreId => $object->getData($this->getAttribute()));
        /****************************/

        $defaultValue = isset($productValues[$defaultStoreId]) ? $productValues[$defaultStoreId] : null;
        $value = isset($productValues[$storeId]) ? $productValues[$storeId] : $defaultValue;

        $value = $this->_prepareDatetimeValue($value, $object);
        $value = $this->_prepareMultiselectValue($value, $object);

        return $value;
    }
}