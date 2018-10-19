<?php
class IWD_SalesRepresentative_Block_Adminhtml_Report_Filter_Form_Order extends IWD_SalesRepresentative_Block_Adminhtml_Report_Filter_Form
{
 	protected function _prepareForm()
    {
        parent::_prepareForm();
        $form = $this->getForm();
        $htmlIdPrefix = $form->getHtmlIdPrefix();
        /** @var Varien_Data_Form_Element_Fieldset $fieldset */
        $fieldset = $this->getForm()->getElement('base_fieldset');

        return $this;
    }
}
