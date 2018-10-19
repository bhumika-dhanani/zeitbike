<?php
class IWD_BirthdayCoupon_Block_System_Config_Form_Fieldset_Documentations extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return '<span class="notice"><a href="https://docs.google.com/document/d/1FAAAcsyMuCOfABgJzreOQMyZ7H8YBsOshcUm6lrI0NU/" target="_blank">User Guide</span>';
    }
}