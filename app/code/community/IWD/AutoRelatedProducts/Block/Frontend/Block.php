<?php
class IWD_AutoRelatedProducts_Block_Frontend_Block extends Mage_Catalog_Block_Product_Abstract implements Mage_Widget_Block_Interface
{
    const XML_PATH_DESIGN_ADDTOBUTTON_STANDARD = "iwd_autorelatedproducts/design/addbutton_standard";

    public function customAddToCartButton()
    {
        return Mage::getStoreConfig(self::XML_PATH_DESIGN_ADDTOBUTTON_STANDARD, Mage::app()->getStore()) == 0;
    }

    public function getBoughtProducts()
    {
        $request = $this->getRequest()->getParams();

        return Mage::getModel('iwd_alsobought/alsobought')->getBoughtProducts($request['id'], $this->getProductPageMaxCount());
    }

    /**
     * A model to serialize attributes
     * @var Varien_Object
     */
    protected $_serializer = null;

    /**
     * Initialization
     */
    protected function _construct()
    {
        $this->_serializer = new Varien_Object();
        parent::_construct();
    }

    /**
     * Produce links list rendered as html
     *
     * @return string
     */
    protected function _toHtml()
    {
        $html = '';
        $block_type = $this->getData('block_type');
        $block_ids = $this->getData('block_ids');

        if (empty($block_type)) {
            return $html;
        }

        $blocks = $this->loadBlocks($block_type, $block_ids);

        $this->assign('blocks', $blocks);

        return parent::_toHtml();
    }

    protected function loadBlocks($block_type, $block_ids)
    {
        $_blocks = Mage::getModel("iwd_autorelatedproducts/blocks")
            ->getCollection()
            ->addFieldToFilter("block_type", $block_type)
            ->addFieldToFilter("status", 1)
            ->addFieldToFilter('from_date',
                array(
                    array('to' => Mage::getModel('core/date')->gmtDate()),
                    array('from_date', 'null' => '')))
            ->addFieldToFilter('to_date',
                array(
                    array('gteq' => Mage::getModel('core/date')->gmtDate()),
                    array('to_date', 'null' => ''))
            );

        if (!empty($block_ids)) {
            $block_ids_array = explode(',', $block_ids);
            if(!in_array(-1, $block_ids_array)){
                $_blocks->addFieldToFilter("id", array('in' => $block_ids_array));
            }
        }

        $blocks = array();

        foreach ($_blocks as $block) {
            if (!$this->checkBlock($block))
                continue;

            $blocks[] = $block;
        }

        return $blocks;
    }

    protected function checkBlock($block)
    {
        /* website */
        $current_store = Mage::app()->getStore()->getStoreId();
        $stores = $block->getStoreView();
        $stores = unserialize($stores);

        if (!in_array(0, $stores) && !in_array($current_store, $stores))
            return false;

        /* customer group*/
        $customer_group_ids = $block->getCustomerGroupIds();
        $customer_group_ids = unserialize($customer_group_ids);
        $logged_in = Mage::getSingleton('customer/session')->isLoggedIn();

        if ($logged_in) {
            $group_id = Mage::getSingleton('customer/session')->getCustomerGroupId();
            if (!in_array($group_id, $customer_group_ids))
                return false;
        } else {
            if (!in_array(0, $customer_group_ids))
                return false;
        }

        return true;
    }
}
