<?php

class IWD_SalesRepresentative_Block_Adminhtml_Users_List_Edit_Tab_Settings extends Mage_Adminhtml_Block_Widget_Form
{

	protected function _prepareLayout()
	{
		$this->setChild('continue_button', $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
			'label' => Mage::helper('salesrep')->__('Continue'),
			'onclick' => "setSettings('" . $this->getContinueUrl() . "','attribute_set_id','product_type')",
			'class' => 'save'
		)));
		return parent::_prepareLayout();
	}

	protected function _prepareForm()
	{

		$model = Mage::registry('current_user');

		/*if ($model->getId ()) {
            $limit_items = explode(',', $model->getLimitItems());

            $model->setLimitItems($limit_items);
        }*/

		$isElementDisabled = false;

		$form = new Varien_Data_Form ();

		$form->setHtmlIdPrefix('salerep_');

		$fieldset = $form->addFieldset('base_fieldset', array(
			'legend' => Mage::helper('salesrep')->__('User Details')
		));

		if ($model->getId()) {
			$fieldset->addField('iwd_entity_id', 'hidden', array(
				'name' => 'iwd_entity_id'
			));
		}
		$fieldset->addField('iwd_adminname', 'text', array(
			'name' => 'iwd_adminname',
			'label' => Mage::helper('salesrep')->__('Admin Name'),
			'title' => Mage::helper('salesrep')->__('Admin Name'),
			'disabled' => true,
			'class' => 'disabled'
		));

		$fieldset->addField('iwd_name', 'text', array(
			'name' => 'iwd_name',
			'label' => Mage::helper('salesrep')->__('Display Name'),
			'title' => Mage::helper('salesrep')->__('Display Name'),
			'disabled' => $isElementDisabled,
			'after_element_html' => '<p class="note">This name will appear in reports, order grid and select boxes</p>'
		));

		$fieldset->addField('iwd_color', 'text', array(
			'name' => 'iwd_color',
			'label' => Mage::helper('salesrep')->__('Highlight'),
			'title' => Mage::helper('salesrep')->__('Highlight'),
			'disabled' => $isElementDisabled
		));


		$fieldset->addField('iwd_notify', 'select', array(
			'name' => 'iwd_notify',
			'label' => Mage::helper('salesrep')->__('Notify user after order assigned'),
			'title' => Mage::helper('salesrep')->__('Use Global Rate Only'),
			'disabled' => $isElementDisabled,
			'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray()
		));

		$fieldset->addField('iwd_show_user_orders_only', 'select', array(
			'name' => 'iwd_show_user_orders_only',
			'label' => Mage::helper('salesrep')->__('Show only current user orders'),
			'title' => Mage::helper('salesrep')->__('Show only current user orders'),
			'disabled' => $isElementDisabled,
			'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray()
		));


		/** PRODUCT RATE **/
		$fieldset = $form->addFieldset('product_fieldset', array(
			'legend' => Mage::helper('salesrep')->__('Product Rate')
		));

		$fieldset->addField('iwd_rate_type', 'select', array(
			'name' => 'iwd_rate_type',
			'label' => Mage::helper('salesrep')->__('Rate Type'),
			'title' => Mage::helper('salesrep')->__('Rate Type'),
			'disabled' => $isElementDisabled,
			'options' => $model->getAvailableTypes()
		));

		$fieldset->addField('iwd_percent_rate', 'text', array(
			'name' => 'iwd_percent_rate',
			'label' => '% ' . Mage::helper('salesrep')->__('Per Sold Product'),
			'disabled' => $isElementDisabled
		));


		$currency_code = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();

		$fieldset->addField('iwd_fixed_rate', 'text', array(
			'name' => 'iwd_fixed_rate',
			'label' => $currency_code . ' ' . Mage::helper('salesrep')->__('Per Sold Product'),
			'disabled' => $isElementDisabled
		));

		$fieldset->addField('iwd_global', 'select', array(
			'name' => 'iwd_global',
			'label' => Mage::helper('salesrep')->__('Use Global Rate Only'),
			'title' => Mage::helper('salesrep')->__('Use Global Rate Only'),
			'disabled' => $isElementDisabled,
			'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
			'after_element_html' => '<p class="note">Disable ability to setup free per specific product</p>'
		));


		/** ORDER RATE **/
		$fieldset = $form->addFieldset('iwd_order_fieldset', array(
			'legend' => Mage::helper('salesrep')->__('Order Rate')
		));

		$fieldset->addField('iwd_rate_type_order', 'select', array(
			'name' => 'iwd_rate_type_order',
			'label' => Mage::helper('salesrep')->__('Rate Type'),
			'title' => Mage::helper('salesrep')->__('Rate Type'),
			'disabled' => $isElementDisabled,
			'options' => $model->getAvailableTypes()
		));

		$fieldset->addField('iwd_percent_rate_order', 'text', array(
			'name' => 'iwd_percent_rate_order',
			'label' => '% ' . Mage::helper('salesrep')->__('Per Order'),
			'disabled' => $isElementDisabled
		));


		$currency_code = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();

		$fieldset->addField('iwd_fixed_rate_order', 'text', array(
			'name' => 'iwd_fixed_rate_order',
			'label' => $currency_code . ' ' . Mage::helper('salesrep')->__('Per Order'),
			'disabled' => $isElementDisabled
		));


		$fieldset = $form->addFieldset('report_fieldset', array(
			'legend' => Mage::helper('salesrep')->__('Report Notification')
		));


		$fieldset->addField('iwd_order_notification_report', 'select', array(
			'name' => 'iwd_order_notification_report',
			'label' => Mage::helper('salesrep')->__('Order Report'),
			'title' => Mage::helper('salesrep')->__('Order Report'),
			'disabled' => $isElementDisabled,
			'values' => $model->getAvailablePeriods(),
			'after_element_html' => '<p class="note">In order to schedule emailed reports, please setup a cron.</p>'
		));

		$fieldset->addField('iwd_product_notification_report', 'select', array(
			'name' => 'iwd_product_notification_report',
			'label' => Mage::helper('salesrep')->__('Product Report'),
			'title' => Mage::helper('salesrep')->__('Product Report'),
			'disabled' => $isElementDisabled,
			'values' => $model->getAvailablePeriods(),
			'after_element_html' => '<p class="note">In order to schedule emailed reports, please setup a cron.</p>'
		));

		$fieldset = $form->addFieldset('limits_fieldset', array(
			'legend' => Mage::helper('salesrep')->__('Customers')
		));


		$fieldset->addField('iwd_limit_users', 'select', array(
			'name' => 'iwd_limit_users',
			'label' => Mage::helper('salesrep')->__('Limit by Customer'),
			'title' => Mage::helper('salesrep')->__('Limit by Customer'),
			'disabled' => $isElementDisabled,
			'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
		));


		$fieldset->addField('iwd_limit_items', 'multiselect', array(
			'name' => 'iwd_limit_items',
			'label' => Mage::helper('salesrep')->__('Limit in Grid'),
			'title' => Mage::helper('salesrep')->__('Limit in Grid'),
			'disabled' => $isElementDisabled,
			'values' => $model->getAvailableLimitTypes()
		));

		$fieldset->addField('iwd_autoasssign_user', 'select', array(
			'name' => 'iwd_autoasssign_user',
			'label' => Mage::helper('salesrep')->__('Auto Assign user to representative'),
			'title' => Mage::helper('salesrep')->__('Limit by Customer'),
			'disabled' => $isElementDisabled,
			'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
			'after_element_html' => '<p class="note">if Yes, then when a new customer account is created from Admin panel it will be assigned to this sales rep.</p>'
		));


		$data = $model->getData();

		$form->setValues($model->getData());
		$this->setForm($form);
	}

	public function getContinueUrl()
	{
		return $this->getUrl('*/*/new', array(
			'_current' => true,
			'set' => '{{attribute_set}}',
			'type' => '{{type}}'
		));
	}
}
