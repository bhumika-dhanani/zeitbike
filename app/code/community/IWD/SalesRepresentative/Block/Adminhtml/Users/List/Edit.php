<?php

class IWD_SalesRepresentative_Block_Adminhtml_Users_List_Edit extends Mage_Adminhtml_Block_Widget{
	
    public function __construct(){
        parent::__construct();
        $this->setTemplate('salesrep/edit.phtml');
        $this->setId('user_edit');
    }

    /**
     * Retrieve currently edited product object
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getUser(){
        return Mage::registry('current_user');
    }

    protected function _prepareLayout(){
       
            $this->setChild('back_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(array(
                        'label'     => Mage::helper('salesrep')->__('Back'),
                        'onclick'   => 'setLocation(\''.$this->getUrl('*/*/').'\')',
                        'class' => 'back'
                    ))
            );
        


            $this->setChild('save_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(array(
                        'label'     => Mage::helper('salesrep')->__('Save'),
                        'onclick'   => 'editForm._submit()',
                        'class' => 'save'
                    ))
            );
      

      
          
            $this->setChild('save_and_edit_button',
                    $this->getLayout()->createBlock('adminhtml/widget_button')
                        ->setData(array(
                            'label'     => Mage::helper('salesrep')->__('Save and Continue Edit'),
                            'onclick'   => 'saveAndContinueEdit(\''.$this->getSaveAndContinueUrl().'\')',
                            'class' => 'save'
                        ))
                );
           


        return parent::_prepareLayout();
    }

    public function getBackButtonHtml(){
        return $this->getChildHtml('back_button');
    }

    public function getCancelButtonHtml(){
        return $this->getChildHtml('reset_button');
    }

    public function getSaveButtonHtml(){
        return $this->getChildHtml('save_button');
    }

    public function getSaveAndEditButtonHtml(){
        return $this->getChildHtml('save_and_edit_button');
    }

    public function getDeleteButtonHtml(){
        return $this->getChildHtml('delete_button');
    }


    public function getSaveUrl(){
        return $this->getUrl('*/*/save', array('_current'=>true, 'back'=>null));
    }

    public function getSaveAndContinueUrl(){
        return $this->getUrl('*/*/save', array(
            '_current'   => true,
            'back'       => 'edit'            
        ));
    }

    public function getUserId(){
        return $this->getUser()->getId();
    }

 
    

    public function getDeleteUrl(){
        return $this->getUrl('*/*/delete', array('_current'=>true));
    }



    public function getHeader(){
        $header = '';
        if ($this->getUser()->getId()) {
            $header = '<i class="fa fa-users"></i> ' . $this->htmlEscape($this->getUser()->getIwdAdminname());
        }else {
            $header = Mage::helper('salesrep')->__('New');
        }
        
        return $header;
    }


    public function getSelectedTabId(){
        return addslashes(htmlspecialchars($this->getRequest()->getParam('tab')));
    }
    
    public function getSaveAjaxUrlProduct(){
    	return $this->getUrl('adminhtml/iwd_salesrep_users/saverelated/',array('_current'=>true));
    }
    
    public function getSaveAjaxUrlCustomer(){
    	return $this->getUrl('adminhtml/iwd_salesrep_users/savecustomer/',array('_current'=>true));
    }
    
    public function getProductRateUrl(){
    	return $this->getUrl('adminhtml/iwd_salesrep_users/ratedetails/',array('_current'=>true));
    }
    
    public function getSaveRetailsRateUrl(){
    	return $this->getUrl('adminhtml/iwd_salesrep_users/saveratedetails/',array('_current'=>true));
    }
}
