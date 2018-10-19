<?php
class IWD_SalesRepresentative_Block_Adminhtml_Users_List_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs{
	

    public function __construct(){
        parent::__construct();
        $this->setId('product_info_tabs');
        $this->setDestElementId('product_edit_form');
        $this->setTitle(Mage::helper('salesrep')->__('User Information'));
    }

    protected function _prepareLayout(){
    	
        $user = $this->getUser();

        $this->addTab('set', array(
        		'label'     => Mage::helper('salesrep')->__('Settings'),
        		'content'   => $this->_translateHtml($this->getLayout()->createBlock('salesrep/adminhtml_users_list_edit_tab_settings')->toHtml()),
        		'active'    => true
        ));
           
		$this->addTab('related', array(
			'label'     => Mage::helper('salesrep')->__('Products'),
			'url'       => $this->getUrl('*/*/related', array('_current' => true)),
			'class'     => 'ajax',
		));
		
		$this->addTab('customer', array(
				'label'     => Mage::helper('salesrep')->__('Customers'),
				'url'       => $this->getUrl('*/*/customers', array('_current' => true)),
				'class'     => 'ajax',
		));

        return parent::_prepareLayout();
    }

    /**
     * Retrive product object from object if not from registry
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getUser()
    {
        if (!($this->getData('user') instanceof IWD_SalesRepresentative_Model_Users)) {
            $this->setData('user', Mage::registry('user'));
        }
        return $this->getData('user');
    }

    

    /**
     * Translate html content
     *
     * @param string $html
     * @return string
     */
    protected function _translateHtml($html)
    {
        Mage::getSingleton('core/translate_inline')->processResponseBody($html);
        return $html;
    }
}
