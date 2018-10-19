<?php
class IWD_SalesRepresentative_Block_Adminhtml_Users_List_Edit_Rate extends Mage_Adminhtml_Block_Widget_Form{

	/**
     * Init form
     */
    public function __construct(){
        parent::__construct();
        $this->setId('block_form');
        $this->setTitle(Mage::helper('cms')->__('Block Information'));
    }
    
	protected function _prepareForm() {
		
		$request = $this->getRequest();
		$userId  = $request->getParam('user');
		$productId  = $request->getParam('product');
		
		$collection = Mage::getModel('salesrep/link')->getCollection()
						->addFieldToFilter('iwd_user_id',array('eq'=>$userId))
						->addFieldToFilter('iwd_linked_product_id',array('eq'=>$productId));
		$item = $collection->getFirstItem();
		if (!$item->getId()){
			$item = Mage::getModel('salesrep/link');
	
    		$item->setData('iwd_user_id', $userId);
    		$item->setData('iwd_linked_product_id', $productId);
    		try{
    			$item->save();
    		}catch(Exception $e){
    			$responseData['error'] = $e->getMessage();
    			Mage::logException($e);
    		}
			
		}
		
		
		
		$model = Mage::registry ( 'current_user' );
		
		$isElementDisabled = false;
		
		$form = new Varien_Data_Form ();
		$form = new Varien_Data_Form(array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post'));
		
		$form->setHtmlIdPrefix('salerep_');
		
		$fieldset = $form->addFieldset ( 'base_fieldset', array (
				'legend' => Mage::helper ( 'salesrep' )->__ ( 'Product Rate Details' ) 
		) );
		
		
		

		$fieldset->addField ( 'product_rate_type', 'select', array (
				'name' => 'product_rate_type',
				'label' => Mage::helper ( 'salesrep' )->__ ( 'Rate Type' ),
				'title' => Mage::helper ( 'salesrep' )->__ ( 'Rate Type' ),
				'disabled' => $isElementDisabled,
				'options' => $model->getAvailableTypes () 
		) );
		
		$fieldset->addField ( 'product_percent_rate', 'text', array (
				'name' => 'product_percent_rate',
				'label' => '% ' . Mage::helper ( 'salesrep' )->__ ( 'Per Sold Product' ),
				'disabled' => $isElementDisabled 
		) );
		
		
		$currency_code = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
		
		$fieldset->addField ( 'product_fixed_rate', 'text', array (
				'name' => 'product_fixed_rate',
				'label' => $currency_code . ' ' . Mage::helper ( 'salesrep' )->__ ( 'Per Sold Product' ),
				'disabled' => $isElementDisabled 
		) );
		
		
		
		$data = $item->getData ();
		
		$form->setValues ( $item->getData () );
		$this->setForm ( $form );
		return parent::_prepareForm();
	}
}