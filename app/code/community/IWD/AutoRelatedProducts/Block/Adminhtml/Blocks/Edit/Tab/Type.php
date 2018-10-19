<?php
class IWD_AutoRelatedProducts_Block_Adminhtml_Blocks_Edit_Tab_Type extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareLayout()
    {
        $this->setChild('continue_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('Continue'),
                    'class'     => 'save',
                    'type'      => 'submit'
                ))
        );
        return parent::_prepareLayout();
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('settings', array('legend'=>Mage::helper('catalog')->__('Select Block Type')));

        $fieldset->addField('block_type', 'select', array(
            'label' => Mage::helper('catalog')->__('Block Type'),
            'title' => Mage::helper('catalog')->__('Block Type'),
            'name'  => 'block_type',
            'value' => '',
            'values'=> Mage::getModel('iwd_autorelatedproducts/block_options_type')->getOptionArray()
        ));

        $fieldset->addField('continue_button', 'note', array(
            'text' => $this->getChildHtml('continue_button'),
        ));

        $fieldset->addField('next_step', 'hidden', array(
            'name'  => 'next_step',
            'value' => '1',
        ));

        $this->setForm($form);
    }
}
