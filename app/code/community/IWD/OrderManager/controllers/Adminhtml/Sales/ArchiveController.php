<?php
class IWD_OrderManager_Adminhtml_Sales_ArchiveController extends Mage_Adminhtml_Controller_Action
{
    /************************** DISPLAY GRIDS ****************************/
    public function indexAction()
    {
        $this->orderAction();
    }

    public function orderAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('sales')
            ->_title($this->__('IWD Order Manager - Archive - Orders'));

        $this->_addBreadcrumb(
            Mage::helper('iwd_ordermanager')->__('IWD Order Manager - Archive - Orders'),
            Mage::helper('iwd_ordermanager')->__('IWD Order Manager - Archive - Orders')
        );

        $this->_addContent($this->getLayout()->createBlock('iwd_ordermanager/adminhtml_sales_order_archive_orders'));
        $this->renderLayout();
    }

    public function invoiceAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('sales')
            ->_title($this->__('IWD Order Manager - Archive - Invoices'));

        $this->_addBreadcrumb(
            Mage::helper('iwd_ordermanager')->__('IWD Order Manager - Archive - Invoices'),
            Mage::helper('iwd_ordermanager')->__('IWD Order Manager - Archive - Invoices')
        );

        $this->_addContent($this->getLayout()->createBlock('iwd_ordermanager/adminhtml_sales_order_archive_invoices'));
        $this->renderLayout();
    }

    public function shipmentAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('sales')
            ->_title($this->__('IWD Order Manager - Archive - Shipments'));

        $this->_addBreadcrumb(
            Mage::helper('iwd_ordermanager')->__('IWD Order Manager - Archive - Shipments'),
            Mage::helper('iwd_ordermanager')->__('IWD Order Manager - Archive - Shipments')
        );

        $this->_addContent($this->getLayout()->createBlock('iwd_ordermanager/adminhtml_sales_order_archive_shipments'));
        $this->renderLayout();
    }

    public function creditmemoAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('sales')
            ->_title($this->__('IWD Order Manager - Archive - Credit Memo'));

        $this->_addBreadcrumb(
            Mage::helper('iwd_ordermanager')->__('IWD Order Manager - Archive - Credit Memo'),
            Mage::helper('iwd_ordermanager')->__('IWD Order Manager - Archive - Credit Memo')
        );

        $this->_addContent($this->getLayout()->createBlock('iwd_ordermanager/adminhtml_sales_order_archive_creditmemos'));
        $this->renderLayout();
    }
    /************************************************** end DISPLAY GRIDS */



    /**************************** ARCHIVE *********************************/
    public function archiveAction()
    {
        $order_ids = $this->getRequest()->getParam('order_ids');

        if (empty($order_ids)) {
            $this->_getSession()->addError($this->__('Please, select orders'));
        } else {
            $result = Mage::getModel('iwd_ordermanager/archive')->addSalesToArchiveByIds($order_ids);
            $this->_addResultOfArchiveToLog($result);
        }

        $this->_redirect('*/sales_order/');
        return;
    }

    public function archiveManuallyAction()
    {
        $result = Mage::getModel('iwd_ordermanager/archive')->addSalesToArchive();
        $this->_addResultOfArchiveToLog($result);
        $this->_redirect('adminhtml/system_config/edit/section/iwd_ordermanager');
        return;
    }

    protected function _addResultOfArchiveToLog($result)
    {
        $link_to_archive = ' (<a href="' . Mage::helper("adminhtml")->getUrl("*/sales_archive/order") . '" title="' . $this->__('Refer to Archive Orders') . '">' . $this->__('Refer to "Archive Orders"') . '</a>)';

        $error = $result->resultError();

        if (!empty($error)) {
            $this->_getSession()->addError($this->__('Error archive order(s)') . ": " . $error->getMessage() . $link_to_archive);
        } else {
            $this->_getSession()->addSuccess(sprintf($this->__('Orders have been archived successfully. %s'), $link_to_archive));
        }
        if (!empty($not_allowed_orders) && $not_allowed_orders > 0) {
            $message = '%d order(s) not was archived. Not allow archive this order(s). Please, check <a href="%s" target="_blank" title="System - Configuration - IWD Extensions - Order Manager - Archive Sales">configuration</a> of IWD OrderManager';
            $href = Mage::helper("adminhtml")->getUrl("adminhtml/system_config/edit", array("section" => "iwd_ordermanager"));
            $this->_getSession()->addNotice(sprintf($message, $not_allowed_orders, $href));
        }
    }
    /******************************************************** end ARCHIVE */



    /**************************** RESTORE *********************************/
    public function restoreAction()
    {
        $order_ids = $this->getRequest()->getParam('order_ids');

        if (empty($order_ids)) {
            $this->_getSession()->addError($this->__('Please, select orders'));
        } else {
            $result = Mage::getModel('iwd_ordermanager/archive')->restoreSalesFromArchiveByIds($order_ids);
            $this->_addResultOfRestoreToLog($result);
        }

        $this->_redirect('*/sales_archive/');
        return;
    }

    public function restoreManuallyAction()
    {
        $result = Mage::getModel('iwd_ordermanager/archive')->restoreSalesFromArchive();
        $this->_addResultOfRestoreToLog($result);
        $this->_redirect('adminhtml/system_config/edit/section/iwd_ordermanager');
        return;
    }

    protected function _addResultOfRestoreToLog($result)
    {
        $link_to_order_page = ' (<a href="' . Mage::helper("adminhtml")->getUrl("*/sales_order/") . '" title="' . $this->__('Refer to Orders') . '">' . $this->__('Refer to "Orders"') . '</a>)';

        $error = $result->resultError();

        if (!empty($error)) {
            $this->_getSession()->addError($this->__('Error restore order(s)') . ": " . $error->getMessage() . $link_to_order_page);
        } else {
            $this->_getSession()->addSuccess(sprintf($this->__('Orders have been restored successfully %s'), $link_to_order_page));
        }
    }
    /******************************************************** end RESTORE */



    /************************** EXPORTS GRIDS ******************************/
    public function exportCsvAction()
    {
        $sales_type = $this->getRequest()->getParam('type');
        $fileName = '';
        $grid = null;

        switch ($sales_type) {
            case 'order':
                $fileName = 'archived_orders.csv';
                $grid = $this->getLayout()->createBlock('iwd_ordermanager/adminhtml_sales_order_archive_orders_grid');
                break;
            case 'invoice':
                $fileName = 'archived_invoices.csv';
                $grid = $this->getLayout()->createBlock('iwd_ordermanager/adminhtml_sales_order_archive_invoices_grid');
                break;
            case 'creditmemo':
                $fileName = 'archive_credit_memos.csv';
                $grid = $this->getLayout()->createBlock('iwd_ordermanager/adminhtml_sales_order_archive_creditmemos_grid');
                break;
            case 'shipment':
                $fileName = 'archive_shipments.csv';
                $grid = $this->getLayout()->createBlock('iwd_ordermanager/adminhtml_sales_order_archive_shipments_grid');
                break;
        }

        if (empty($fileName) || empty($grid))
            return;

        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    public function exportExcelAction()
    {
        $sales_type = $this->getRequest()->getParam('type');
        $fileName = '';
        $grid = null;

        switch ($sales_type) {
            case 'order':
                $fileName = 'archived_orders.xml';
                $grid = $this->getLayout()->createBlock('iwd_ordermanager/adminhtml_sales_order_archive_orders_grid');
                break;
            case 'invoice':
                $fileName = 'archived_invoices.xml';
                $grid = $this->getLayout()->createBlock('iwd_ordermanager/adminhtml_sales_order_archive_invoices_grid');
                break;
            case 'creditmemo':
                $fileName = 'archive_credit_memos.xml';
                $grid = $this->getLayout()->createBlock('iwd_ordermanager/adminhtml_sales_order_archive_creditmemos_grid');
                break;
            case 'shipment':
                $fileName = 'archive_shipments.xml';
                $grid = $this->getLayout()->createBlock('iwd_ordermanager/adminhtml_sales_order_archive_shipments_grid');
                break;
        }

        if (empty($fileName) || empty($grid))
            return;

        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile());
    }
    /*************************************************** end EXPORTS GRIDS */



    /************************** MASS ACTIONS *******************************/
    public function massCancelAction()
    {
        $this->_forward('massCancel', 'sales_order', null, array('origin' => 'archive'));
    }

    public function massHoldAction()
    {
        $this->_forward('massHold', 'sales_order', null, array('origin' => 'archive'));
    }

    public function massUnholdAction()
    {
        $this->_forward('massUnhold', 'sales_order', null, array('origin' => 'archive'));
    }

    public function ordersGridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('iwd_ordermanager/adminhtml_sales_order_archive_orders_grid')->toHtml()
        );
    }
    /**************************************************** end MASS ACTIONS */



    /************************ GRIDS AJAX ACTIONS ***************************/
    public function invoicesGridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('iwd_ordermanager/adminhtml_sales_order_archive_invoices_grid')->toHtml()
        );
    }

    public function creditmemosGridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('iwd_ordermanager/adminhtml_sales_order_archive_creditmemos_grid')->toHtml()
        );
    }

    public function shipmentsGridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('iwd_ordermanager/adminhtml_sales_order_archive_shipments_grid')->toHtml()
        );
    }
    /********************************************** end GRIDS AJAX ACTIONS */
    /*iwd fix*/
    protected function _isAllowed()
    {
        return true;
    }
}