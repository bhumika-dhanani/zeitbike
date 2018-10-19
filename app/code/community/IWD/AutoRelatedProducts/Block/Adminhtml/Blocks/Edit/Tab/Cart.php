<?php
class IWD_AutoRelatedProducts_Block_Adminhtml_Blocks_Edit_Tab_Cart extends Mage_Adminhtml_Block_Widget_Form{
    
    protected function _prepareForm(){
        
        if (Mage::registry('iwd_related_products_data')) {
            $blocks = Mage::registry('iwd_related_products_data');
        } else {
            $blocks = Mage::getModel("iwd_autorelatedproducts/blocks");
        }

        $cart_conditions = $blocks->getShoppingCartConditions();

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('cart_rule_');

        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
            ->setTemplate('promo/fieldset.phtml')
            ->setNewChildUrl($this->getUrl('*/blocks/newCartConditionHtml/form/cart_rule_conditions_fieldset'));

        $fieldset = $form->addFieldset('conditions_fieldset', array(
            'legend'=>Mage::helper('salesrule')->__('Shopping cart conditions')
        ))->setRenderer($renderer);
        $fieldset->addField('conditions', 'text', array(
            'name' => 'conditions',
            'label' => Mage::helper('salesrule')->__('Conditions'),
            'title' => Mage::helper('salesrule')->__('Conditions'),
        ))->setRule($cart_conditions)->setRenderer(Mage::getBlockSingleton('rule/conditions'));

        $form->setValues($blocks);

        //$form->setUseContainer(true);

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
