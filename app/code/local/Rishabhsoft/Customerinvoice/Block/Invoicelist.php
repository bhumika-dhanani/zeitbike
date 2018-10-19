<?php

class Rishabhsoft_Customerinvoice_Block_Invoicelist extends Mage_Core_Block_Template
{

    public function __construct()
    {
        parent::__construct();

        $orders = Mage::getResourceModel('sales/order_collection')
            ->addFieldToSelect('*')
            ->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
            ->addFieldToFilter('state', array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()))
            ->setOrder('created_at', 'desc')
        ;

        $this->setOrders($orders);

        Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('root')->setHeaderTitle(Mage::helper('sales')->__('My Orders'));
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('page/html_pager', 'sales.order.history.pager')
            ->setCollection($this->getOrders());
        $this->setChild('pager', $pager);
        $this->getOrders()->load();
        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getInvoicePrintUrl($invoice)
    {
        return $this->getUrl('*/*/printinvoice', array('invoice_id' => $invoice->getId()));
    }

    public function getBackUrl()
    {
        return $this->getUrl('customer/account/');
    }

    public function getInvoices($orderId)
    {
        //echo "c";exit;
        
        $orderObject = Mage::getModel('sales/order')->load($orderId);
        return $orderObject->getInvoiceCollection();
    }
    public function getCreditmemos($orderId)
    { 
        $orderObject = Mage::getModel('sales/order')->load($orderId);
        return $orderObject->getCreditmemosCollection();
    }
    public function getCreditMemoPrintUrl($creditmemo)
    {
        return $this->getUrl('*/*/printcreditmemo', array('creditmemo_id' => $creditmemo->getId()));
    }
}
