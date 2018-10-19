<?php
class IWD_AutoRelatedProducts_Block_Adminhtml_Blocks_Edit_Tab_Current extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    protected function _prepareForm()
    {
        if (Mage::registry('iwd_related_products_data')) {
            $blocks = Mage::registry('iwd_related_products_data');
        } else {
            $blocks = Mage::getModel("iwd_autorelatedproducts/blocks");
        }

        $current_conditions = $blocks->getCurrentConditions();
        $helper = Mage::helper('iwd_autorelatedproducts');
        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('c_rule_');

        /*** Conditions ***/
        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
            ->setTemplate('promo/fieldset.phtml')
            ->setNewChildUrl($this->getUrl('*/blocks/newCurrentConditionHtml/form/c_rule_conditions_fieldset'));

        $fieldset = $form->addFieldset('conditions_fieldset', array(
            'legend' => $helper->__('Set the conditions for the choice of current products'))
        )->setRenderer($renderer);

        $fieldset->addField('current_conditions', 'text', array(
            'name' => 'current_conditions',
            'label' => $helper->__('Conditions'),
            'title' => $helper->__('Conditions'),
            'required' => true,
        ))->setRule($current_conditions)->setRenderer(Mage::getBlockSingleton('rule/conditions'));

        /*** Grid ***/
        $fieldset_grid = $form->addFieldset('current_grid_fieldset', array(
            'legend' => $helper->__('Or select current products manually'),
            'class'=>'iwd_fieldset_grid'
        ));

        $current_products_grid = $this->getLayout()->createBlock('iwd_autorelatedproducts/adminhtml_blocks_edit_grid_current', 'iwd_autorelatedproducts.current.grid')->toHtml();
        $serialize_block = $this->getLayout()->createBlock('adminhtml/widget_grid_serializer');
        $serialize_block->initSerializerBlock('iwd_autorelatedproducts.current.grid', 'getSelectedProducts', 'current_products', 'selected_products');
        $current_products_grid .= $serialize_block->toHtml();

        $fieldset_grid->addField('current_grid', 'note', array(
            'name' => 'current_grid',
            'text' => $current_products_grid,
        ));



        $form->setValues($blocks);

        //$form->setUseContainer(true);

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
