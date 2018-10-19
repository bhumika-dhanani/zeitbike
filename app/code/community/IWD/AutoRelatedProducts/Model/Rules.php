<?php
class IWD_AutoRelatedProducts_Model_Rules extends Mage_Rule_Model_Abstract
{
    private $conditions_key = "conditions";
    private $actions_key = "actions";

    public function __construct(array $parameters)
    {
        $this->_init('iwd_autorelatedproducts/rules');
        $this->conditions_key = isset($parameters["conditions_key"]) ? $parameters["conditions_key"] : "conditions";
    }

    /**
     * Getter for rule combine conditions instance
     * @return Mage_Rule_Model_Condition_Combine
     */
    public function getConditionsInstance()
    {
        return Mage::getModel('catalogrule/rule_condition_combine');
    }

    /**
     * Getter for rule actions collection instance
     * @return Mage_Rule_Model_Action_Collection
     */
    public function getActionsInstance()
    {
        return Mage::getModel('catalogrule/rule_action_collection');
    }


    /**
     * Initialize rule model data from array
     * @param array $data
     * @return Mage_Rule_Model_Abstract
     */
    public function loadPost(array $data)
    {
        $arr = $this->_convertFlatToRecursive($data);
        if (isset($arr[$this->conditions_key])) {
            $this->getConditions()->setData($this->conditions_key, null);
            $this->getConditions()->loadArray($arr[$this->conditions_key][1], $this->conditions_key);
        }
        if (isset($arr[$this->actions_key])) {
            $this->getActions()->setData($this->actions_key, null);
            $this->getActions()->loadArray($arr[$this->actions_key][1], $this->actions_key);
        }

        return $this;
    }

    /**
     * Set specified data to current rule.
     * Set related_conditions and current_conditions recursively.
     * Convert dates into Zend_Date.
     * @param array $data
     * @return array
     */
    protected function _convertFlatToRecursive(array $data)
    {
        $arr = array();
        foreach ($data as $key => $value) {
            if (($key === $this->conditions_key || $key === $this->actions_key) && is_array($value)) {
                foreach ($value as $id => $data) {
                    $path = explode('--', $id);
                    $node =& $arr;
                    for ($i = 0, $l = sizeof($path); $i < $l; $i++) {
                        if (!isset($node[$key][$path[$i]])) {
                            $node[$key][$path[$i]] = array();
                        }
                        $node =& $node[$key][$path[$i]];
                    }
                    foreach ($data as $k => $v) {
                        $node[$k] = $v;
                    }
                }
            }
        }

        return $arr;
    }

    /**
     * Reset rule combine conditions
     * @param null|Mage_Rule_Model_Condition_Combine $conditions
     * @return Mage_Rule_Model_Abstract
     */
    protected function _resetConditions($conditions = null)
    {
        if (is_null($conditions)) {
            $conditions = $this->getConditionsInstance();
        }
        $conditions->setRule($this)->setId('1')->setPrefix($this->conditions_key);
        $this->setConditions($conditions);

        $_temp = $this->_conditions->getConditions();
        if(empty($_temp))
            $this->_conditions->setConditions(array());

        return $this;
    }

    /**
     * Retrieve rule combine conditions model
     * @return Mage_Rule_Model_Condition_Combine
     */
    public function getConditions()
    {
        if (empty($this->_conditions)) {
            $this->_resetConditions();
        }
        // Load rule conditions if it is applicable
        if ($this->hasConditionsSerialized()) {
            $conditions = $this->getConditionsSerialized();
            if (!empty($conditions)) {
                $conditions = unserialize($conditions);
                if (is_array($conditions) && !empty($conditions)) {
                    $this->_conditions->loadArray($conditions);
                }
            }
            $this->unsConditionsSerialized();
        }
        return $this->_conditions;
    }

    private  $_productIds;
    private $_productsFilter;

    /**
     * Filtering products that must be checked for matching with rule
     * @param  int|array $productIds
     */
    public function setProductsFilter($productIds)
    {
        $this->_productsFilter = $productIds;
    }

    /**
     * Returns products filter
     * @return array|int|null
     */
    public function getProductsFilter()
    {
        return $this->_productsFilter;
    }

    public function getWebsiteIds(){
        return $this->getStoreView();
    }

    public function isEmptyRules(){
        $rules = $this->getConditions();
        if(empty($rules))
            return true;

        $rules = $rules->getConditions();
        if(empty($rules) || count($rules) == 0)
            return true;

        return false;
    }

    public function getMatchingProductIds()
    {
        if($this->isEmptyRules())
            return array();

        if (is_null($this->_productIds)) {
            $this->_productIds = array();
            $this->setCollectedAttributes(array());
           if ($this->getWebsiteIds()) {
                /** @var $productCollection Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection */
                $productCollection = Mage::getResourceModel('catalog/product_collection');
                $productCollection->addWebsiteFilter($this->getWebsiteIds());

                if ($this->_productsFilter) {
                    $productCollection->addIdFilter($this->_productsFilter);
                }

               $this->getConditions()->collectValidatedAttributes($productCollection);

               Mage::getSingleton('core/resource_iterator')->walk(
                    $productCollection->getSelect(),
                    array(array($this, 'callbackValidateProduct')),
                    array(
                        'attributes' => $this->getCollectedAttributes(),
                        'product'    => Mage::getModel('catalog/product'),
                    )
                );
           }
        }

        return $this->_productIds;
    }

    /**
     * Callback function for product matching
     *
     * @param $args
     * @return void
     */
    public function callbackValidateProduct($args)
    {
        $product = clone $args['product'];
        $product->setData($args['row']);

        if ($this->getConditions()->validate($product)) {
            $this->_productIds[] = $product->getId();
        }
    }
}
