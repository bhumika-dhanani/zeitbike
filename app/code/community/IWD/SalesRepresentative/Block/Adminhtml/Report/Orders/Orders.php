<?php
class IWD_SalesRepresentative_Block_Adminhtml_Report_Orders_Orders extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'adminhtml_report_orders_orders';
        $this->_blockGroup = 'salesrep';
        $this->_headerText = Mage::helper('salesrep')->__('Report By Orders');
        parent::__construct();
        $this->setTemplate('report/grid/container.phtml');
        $this->_removeButton('add');
        $this->addButton('filter_form_submit', array(
            'label'     => Mage::helper('reports')->__('Show Report'),
            'onclick'   => 'filterFormSubmit()'
        ));
    }

    public function getFilterUrl()
    {
        $this->getRequest()->setParam('filter', null);
        return $this->getUrl('*/iwd_salesrep_reports_orders/index', array('_current' => true));
    }
}
