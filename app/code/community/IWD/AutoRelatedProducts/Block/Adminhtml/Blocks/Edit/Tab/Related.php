<?php
class IWD_AutoRelatedProducts_Block_Adminhtml_Blocks_Edit_Tab_Related extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        if (Mage::registry('iwd_related_products_data')) {
            $blocks = Mage::registry('iwd_related_products_data');
        } else {
            $blocks = Mage::getModel("iwd_autorelatedproducts/blocks");
        }

        $related_conditions = $blocks->getRelatedConditions();

        $form = new Varien_Data_Form();
        $helper = Mage::helper('iwd_autorelatedproducts');
        $form->setHtmlIdPrefix('r_rule_');

        /*** Conditions ***/
        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
            ->setTemplate('promo/fieldset.phtml')
            ->setNewChildUrl($this->getUrl('*/blocks/newRelatedConditionHtml/form/r_rule_conditions_fieldset'));

        $fieldset_condition = $form->addFieldset('conditions_fieldset', array(
                'legend' => $helper->__('Set the conditions for the choice of related products'))
        )->setRenderer($renderer);

        $fieldset_condition->addField('related_conditions', 'text', array(
            'name' => 'related_conditions',
            'label' => $helper->__('Conditions'),
            'title' => $helper->__('Conditions'),
            'required' => true,
        ))->setRule($related_conditions)->setRenderer(Mage::getBlockSingleton('rule/conditions'));


        /*** Grid ***/
        $fieldset_grid = $form->addFieldset('related_grid_fieldset', array(
            'legend' => $helper->__('Or select related products manually'),
            'class' => 'iwd_fieldset_grid'
        ));

        $related_products_grid = $this->getLayout()->createBlock('iwd_autorelatedproducts/adminhtml_blocks_edit_grid_related', 'iwd_autorelatedproducts.related.grid')->toHtml();
        $serialize_block = $this->getLayout()->createBlock('adminhtml/widget_grid_serializer');
        $serialize_block->initSerializerBlock('iwd_autorelatedproducts.related.grid', 'getSelectedProducts', 'related_products', 'selected_products');
        $related_products_grid .= $serialize_block->toHtml();

        $fieldset_grid->addField('related_grid', 'note', array(
            'name' => 'related_grid',
            'text' => $related_products_grid,
        ));

        $form->setValues($blocks);

        //$form->setUseContainer(true);

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
