<?php
class IWD_SalesRepresentative_Block_Adminhtml_Report_Filter_Form extends Mage_Adminhtml_Block_Report_Filter_Form{
    
    
    protected function _prepareForm(){
    	parent::_prepareForm();
    	$form = $this->getForm();
    	$htmlIdPrefix = $form->getHtmlIdPrefix();
    	/** @var Varien_Data_Form_Element_Fieldset $fieldset */
    	$fieldset = $this->getForm()->getElement('base_fieldset');
    	$fieldset->removeField('report_type');
    	$fieldset->removeField('period_type');
    	$fieldset->removeField('show_empty_rows');
    	$fieldset->removeField('show_actual_columns');
    
    
    	if (is_object($fieldset) && $fieldset instanceof Varien_Data_Form_Element_Fieldset) {
    
    		$statuses = Mage::getModel('sales/order_config')->getStatuses();
    		$values = array();
    		foreach ($statuses as $code => $label) {
    			if (false === strpos($code, 'pending')) {
    				$values[] = array(
    						'label' => Mage::helper('salesrep')->__($label),
    						'value' => $code
    				);
    			}
    		}
    
    		$adminUserModel = Mage::getModel('admin/user');
    		$userCollection = $adminUserModel->getCollection()->load();
    
    		$users = array();
    		$users[] = array(
    				'label' =>'',
    				'value' =>''
    		);
    		foreach($userCollection as $user){
    			$users[] = array(
    					'label' =>$user->getFirstname() . ' ' .$user->getLastname(),
    					'value' => $user->getId()
    			);
    		}
    
    
    		$fieldset->addField('show_order_statuses', 'select', array(
    				'name'      => 'show_order_statuses',
    				'label'     => Mage::helper('reports')->__('Order Status'),
    				'options'   => array(
    						'0' => Mage::helper('reports')->__('Any'),
    						'1' => Mage::helper('reports')->__('Specified'),
    				),
    				'note'      => Mage::helper('reports')->__('Applies to Any of the Specified Order Statuses'),
    		), 'to');
    
    		$fieldset->addField('order_statuses', 'multiselect', array(
    				'name'      => 'order_statuses',
    				'values'    => $values,
    				'display'   => 'none'
    		), 'show_order_statuses');
    
    		$fieldset->addField('users', 'select', array(
    				'label'     => Mage::helper('salesrep')->__('Representative'),
    				'name'      => 'users',
    				'values'    => $users,
    				'required'	=> true
    		), 'users');
    
    		// define field dependencies
    		if ($this->getFieldVisibility('show_order_statuses') && $this->getFieldVisibility('order_statuses')) {
    			$this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
    					->addFieldMap("{$htmlIdPrefix}show_order_statuses", 'show_order_statuses')
    					->addFieldMap("{$htmlIdPrefix}order_statuses", 'order_statuses')
    					->addFieldDependence('order_statuses', 'show_order_statuses', '1')
    			);
    		}
    	}
    
    	return $this;
    }
}
