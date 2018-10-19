<?php
class IWD_SalesRepresentative_Block_Adminhtml_Report_Products_Products extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'adminhtml_report_products_products';
        $this->_blockGroup = 'salesrep';
        $this->_headerText = Mage::helper('salesrep')->__('Report By Products');
        parent::__construct();
        $this->setTemplate('report/grid/container.phtml');
        $this->_removeButton('add');
        $this->addButton('filter_form_submit', array(
            'label'     => Mage::helper('salesrep')->__('Show Report'),
            'onclick'   => 'filterFormSubmit()'
        ));
    }

    public function getFilterUrl()
    {
        $this->getRequest()->setParam('filter', null);
        return $this->getUrl('*/iwd_salesrep_reports_products/index', array('_current' => true));
    }
}
