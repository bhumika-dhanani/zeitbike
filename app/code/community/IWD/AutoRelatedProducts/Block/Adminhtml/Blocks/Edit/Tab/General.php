<?php
class IWD_AutoRelatedProducts_Block_Adminhtml_Blocks_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $block_type = Mage::app()->getRequest()->getParam('block_type');
        if (empty($block_type)) {
            $iwd_related_products_data = Mage::registry('iwd_related_products_data');
            if (!$iwd_related_products_data || !Mage::registry('iwd_related_products_data')->getBlockType()) {
                Mage::app()->getFrontController()->getResponse()->setRedirect($this->getUrl('*/blocks/new'));
                return;
            } else {
                $block_type = $iwd_related_products_data->getBlockType();
            }
        }

        $helper = Mage::helper('iwd_autorelatedproducts');

        $form = new Varien_Data_Form();
        $fieldset_settings = $form->addFieldset('settings', array('legend' => Mage::helper('catalog')->__('Block settings')));
        $fieldset_display = $form->addFieldset('display', array('legend' => Mage::helper('catalog')->__('Display')));
        $fieldset_design = $form->addFieldset('design', array('legend' => Mage::helper('catalog')->__('Design')));


        $types = Mage::getModel('iwd_autorelatedproducts/block_options_type')->getFlatOptionArray();
        if (isset($types[$block_type])) {
            $fieldset_settings->addField('type', 'note', array(
                'label' => $helper->__('Block Type'),
                'text' => "<b>".$types[$block_type]."</b>",
            ));
        }

        $fieldset_settings->addField('status', 'select', array(
            'label' => $helper->__('Status'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'status',
            'value' => '1',
            'values' => array('1' => 'Enabled', '0' => 'Disabled'),
        ));

        $fieldset_settings->addField('title', 'text', array(
            'label' => $helper->__('Block Title'),
            'title' => $helper->__('Block Title'),
            'name' => 'title',
            'value' => '',
        ));

        $fieldset_settings->addField('description', 'textarea', array(
            'label' => $helper->__('Block Description'),
            'title' => $helper->__('Block Description'),
            'name' => 'description',
            'value' => '',
        ));

        $fieldset_display->addField('max_count_elements', 'text', array(
            'label' => $helper->__('Max Count Products In Block'),
            'title' => $helper->__('Max Count Products In Block'),
            'name' => 'max_count_elements',
            'value' => 10,
            'required' => true,
        ));

        $fieldset_display->addField('sort_order', 'select', array(
            'label' => $helper->__('Sort Order'),
            'title' => $helper->__('Sort Order'),
            'name' => 'sort_order',
            'value' => '',
            'values' => Mage::getModel('iwd_autorelatedproducts/block_options_sortorder')->getOptionArray(),
            'required' => true,
        ));

        $fieldset_display->addField('show_out_of_stock', 'select', array(
            'label' => $helper->__('Show Products Out Of Stock'),
            'title' => $helper->__('Show Products Out Of Stock'),
            'name' => 'show_out_of_stock',
            'value' => '',
            'values' => array(0 => $helper->__('No'), 1 => $helper->__('Yes')),
            'required' => true,
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset_display->addField('store_view', 'multiselect', array(
                'name' => 'store_view',
                'label' => $helper->__('Store View'),
                'title' => $helper->__('Store View'),
                'required' => true,
                'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
                'value' => '0',
            ));
        } else {
            $fieldset_display->addField('store_view_single', 'hidden', array(
                'name' => 'store_view_single',
                'value' => '1',
            ));
        }

        $fieldset_display->addField('customer_group_ids', 'multiselect', array(
            'name' => 'customer_group_ids[]',
            'label' => $helper->__('Customer Groups'),
            'title' => $helper->__('Customer Groups'),
            'required' => true,
            'values' => Mage::getResourceModel('customer/group_collection')->toOptionArray()
        ));

        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        $fieldset_display->addField('from_date', 'date', array(
            'label' => $helper->__('Date From'),
            'title' => $helper->__('Date From'),
            'name' => 'from_date',
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'format' => $dateFormatIso,
        ));

        $fieldset_display->addField('to_date', 'date', array(
            'label' => $helper->__('Date To'),
            'title' => $helper->__('Date To'),
            'name' => 'to_date',
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'format' => $dateFormatIso,
        ));

        $fieldset_settings->addField('id', 'hidden', array(
            'name' => 'id',
            'value' => '',
        ));
        $fieldset_settings->addField('block_type', 'hidden', array(
            'name' => 'block_type',
            'value' => $block_type,
        ));

        $fieldset_design->addField('link_buttons', 'multiselect', array(
            'label' => $helper->__('Buttons'),
            'title' => $helper->__('Buttons'),
            'name' => 'link_buttons',
            'required' => true,
            'values' => Mage::getModel('iwd_autorelatedproducts/block_options_links')->getOptionArray()
        ));

        try {
            if (Mage::registry('iwd_related_products_data')) {
                $data = Mage::registry('iwd_related_products_data')->getData();

                if (!Mage::app()->isSingleStoreMode())
                    $data['store_view'] = unserialize($data['store_view']);
                else
                    $data['store_view_single'] = 1;

                $data['customer_group_ids'] = unserialize($data['customer_group_ids']);

                $data['link_buttons'] = unserialize($data['link_buttons']);

                $form->setValues($data);
            }
        } catch (Exception $e) {
            Mage::log(__CLASS__ . ": " . $e->getMessage(), null, 'iwd_related_products.log');
        }

        $this->setForm($form);
    }
}
