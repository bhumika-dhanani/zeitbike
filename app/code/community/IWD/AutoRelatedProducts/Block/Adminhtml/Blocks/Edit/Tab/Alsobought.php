<?php
class IWD_AutoRelatedProducts_Block_Adminhtml_Blocks_Edit_Tab_Alsobought extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $helper = Mage::helper('iwd_autorelatedproducts');

        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('design', array('legend' => Mage::helper('catalog')->__('Also bought settings')));

        $fieldset->addField('order_statuses', 'multiselect', array(
            'label' => $helper->__('Order Statuses'),
            'title' => $helper->__('Order Statuses'),
            'name' => 'order_statuses',
            'required' => true,
            'values' => Mage::getModel('iwd_autorelatedproducts/block_options_orderstatuses')->getOptionArray()
        ));

        try {
            if (Mage::registry('iwd_related_products_data')) {
                $data = Mage::registry('iwd_related_products_data')->getData();

                if (isset($data['order_statuses_serialized'])) {
                    $data['order_statuses'] = unserialize($data['order_statuses_serialized']);
                }

                $form->setValues($data);
            }
        } catch (Exception $e) {
            Mage::log(__CLASS__ . ": " . $e->getMessage(), null, 'iwd_related_products.log');
        }

        $this->setForm($form);
    }
}
