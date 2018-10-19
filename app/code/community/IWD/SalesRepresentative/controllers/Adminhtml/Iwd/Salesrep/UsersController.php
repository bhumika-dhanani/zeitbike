<?php

class IWD_SalesRepresentative_Adminhtml_Iwd_Salesrep_UsersController extends Mage_Adminhtml_Controller_Action
{

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/sales/users');
    }

    public function _initAction()
    {
        $this->loadLayout()
            ->_addBreadcrumb(Mage::helper('salesrep')->__('Sales'), Mage::helper('salesrep')->__('Sales'))
            ->_addBreadcrumb(Mage::helper('salesrep')->__('Sales'), Mage::helper('salesrep')->__('Sales Representative'));
        return $this;
    }


    public function indexAction()
    {

        $this->_updateList();
        $this->_title($this->__('Sales'))->_title($this->__('Sales Representative'))->_title($this->__('Users'));

        $this->_initAction()
            ->_setActiveMenu('sales/salesrep/user')
            ->_addBreadcrumb(Mage::helper('salesrep')->__('Sales Report'), Mage::helper('salesrep')->__('Sales Report'));


        $this->renderLayout();
    }


    protected function _updateList()
    {

        $collection = Mage::getModel('admin/user')->getCollection();

        foreach ($collection as $user) {

            if (!$this->_isExist($user)) {

                $model = Mage::getModel('salesrep/users');
                $model->setData('iwd_user_id', $user->getId());
                try {
                    $model->save();
                } catch (Exception $e) {
                    echo $e->getMessage();
                    die();
                    Mage::logException($e);
                }
            }
        }

        //if user not exist - mark as inactive
        $collection = Mage::getModel('salesrep/users')->getCollection();

        foreach ($collection as $user) {

            $userAdmin = Mage::getModel('admin/user')->load($user->getIwdUserId());


            if (!$userAdmin->getId()) {

                $user->setIwdStatus('0');
                try {
                    $user->save();

                } catch (Exception $e) {
                    echo $e->getMessage();
                    die();
                    Mage::logException($e);
                }
            }
        }
    }

    protected function _isExist($user)
    {
        $user = Mage::getModel('salesrep/users')->load($user->getId(), 'iwd_user_id');
        if ($user->getId() == null) {
            return false;
        }
        return true;
    }


    protected function _initUser()
    {
        $this->_title($this->__('Sales Representative'))->_title($this->__('Manage Users'));

        $userId = (int)$this->getRequest()->getParam('id');
        $user = Mage::getModel('salesrep/users');
        if ($userId) {
            try {
                $user->load($userId);
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }

        Mage::register('user', $user);
        Mage::register('current_user', $user);
        return $user;
    }

    public function editAction()
    {
        $userId = (int)$this->getRequest()->getParam('id');
        $user = $this->_initUser();

        if ($userId && !$user->getId()) {
            $this->_getSession()->addError(Mage::helper('salesrep')->__('This user no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }
        $this->_title($user->getIwdAdminname());


        $this->loadLayout(array(
            'default',
            strtolower($this->getFullActionName()),
            'adminhtml_catalog_product_' . $user->getTypeId()
        ));

        $this->_setActiveMenu('sales/salesrep');

        $this->renderLayout();
    }

    /**
     * Get related products grid and serializer block
     */
    public function relatedAction()
    {

        $this->_initUser();
        $this->loadLayout();
        $this->getLayout()->getBlock('salesrep.product.edit.tab.related');
        $this->renderLayout();
    }

    /**
     * Get related products grid
     */
    public function relatedGridAction()
    {
        $this->_initUser();
        $this->loadLayout();
        $this->getLayout()->getBlock('salesrep.product.edit.tab.related');
        $this->renderLayout();
    }


    /**
     * Get related customers grid and serializer block
     */
    public function customersAction()
    {
        $this->_initUser();
        $this->loadLayout();
        $this->getLayout()->getBlock('salesrep.product.edit.tab.related');
        $this->renderLayout();
    }


    /**
     * Get related customers grid
     */
    public function customersGridAction()
    {
        $this->_initUser();
        $this->loadLayout();
        $this->getLayout()->getBlock('salesrep.product.edit.tab.related');
        $this->renderLayout();
    }

    public function saverelatedAction()
    {
        $responseData = array();
        $params = $this->getRequest()->getParams();
        $id = $params['product'];
        $add = $params['checked'];
        $idUser = $params['id'];

        if ($add) {
            $model = Mage::getModel('salesrep/link');
            $model->setData('iwd_user_id', $idUser);
            $model->setData('iwd_linked_product_id', $id);
            try {
                $model->save();


            } catch (Exception $e) {
                $responseData['error'] = $e->getMessage();
                Mage::logException($e);
            }
        } else {
            $collection = Mage::getModel('salesrep/link')->getCollection()
                ->addFieldToFilter('iwd_user_id', array('eq' => $idUser))
                ->addFieldToFilter('iwd_linked_product_id', array('eq' => $id));
            foreach ($collection as $item) {
                try {
                    $item->delete();
                } catch (Exception $e) {
                    $responseData['error'] = $e->getMessage();
                    Mage::logException($e);
                }
            }
        }

        $this->_initUser();
        $responseData['id'] = $id;
        $responseData['cell'] = Mage::helper('salesrep')->getGridCellRate($idUser, $id);
        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($responseData));

    }

    public function savecustomerAction()
    {
        $responseData = array();
        $params = $this->getRequest()->getParams();
        $id = $params['customer'];
        $add = $params['checked'];
        $idUser = $params['id'];

        if ($add) {
            $model = Mage::getModel('salesrep/customers');
            $model->setData('iwd_user_id', $idUser);
            $model->setData('iwd_linked_customer_id', $id);

            try {
                $model->save();
            } catch (Exception $e) {
                $responseData['error'] = $e->getMessage();
                Mage::logException($e);
            }
        } else {
            $collection = Mage::getModel('salesrep/customers')->getCollection()
                ->addFieldToFilter('iwd_user_id', array('eq' => $idUser))
                ->addFieldToFilter('iwd_linked_customer_id', array('eq' => $id));

            foreach ($collection as $item) {
                try {
                    $item->delete();
                } catch (Exception $e) {
                    $responseData['error'] = $e->getMessage();
                    Mage::logException($e);
                }
            }
        }

        $this->_initUser();
        $responseData['id'] = $id;
        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($responseData));

    }


    public function saveAction()
    {
        // check if data sent
        $data = $this->getRequest()->getPost();
        if ($data) {


            //init model and set data
            $model = Mage::getModel('salesrep/users');

            $id = $this->getRequest()->getParam('id');

            if ($id) {
                $model->load($id);
            }

            if (isset($data['iwd_limit_items'])) {
                $data['iwd_limit_items'] = implode(',', $data['iwd_limit_items']);
            } else {
                $data['iwd_limit_items'] = '';
            }

            // try to save it
            unset($data['id']);
            try {

                $model->setData($data);

                // save the data
                $model->save();

                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('salesrep')->__('The user data have been saved succesfully.'));

                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {

                    $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                    return;
                }


                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (Mage_Core_Exception $e) {

                $this->_getSession()->addError($e->getMessage());

            } catch (Exception $e) {

                $this->_getSession()->addException($e, Mage::helper('salesrep')->__('An error occurred while saving the user information.'));
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            return;
        }
        $this->_redirect('*/*/');
    }

    public function ratedetailsAction()
    {
        $responseData = array();

        $this->_initUser();
        $this->loadLayout();
        $this->getLayout()->getBlock('salesrep.product.edit.rate');
        $output = $this->getLayout()->getOutput();
        $responseData['content'] = $output;

        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($responseData));
    }

    public function saveratedetailsAction()
    {
        $responseData = array();
        $this->_initUser();
        $data = $this->getRequest()->getParams();
        unset($data['form_key']);
        unset($data['id']);
        unset($data['key']);
        $collection = Mage::getModel('salesrep/link')->getCollection()
            ->addFieldToFilter('iwd_user_id', array('eq' => $data['user_id']))
            ->addFieldToFilter('iwd_linked_product_id', array('eq' => $data['iwd_linked_product_id']));
        $item = $collection->getFirstItem();

        if (!$item->getId()) {
            $item = Mage::getModel('salesrep/link');
            $item->addData($data);
        } else {

            $item = Mage::getModel('salesrep/link')->load($item->getId());
            $item->setData('iwd_product_fixed_rate', $data['product_fixed_rate']);
            $item->setData('iwd_product_percent_rate', $data['product_percent_rate']);
            $item->setData('iwd_product_rate_type', $data['product_rate_type']);
        }


        try {
            $item->save();
            $responseData['cell'] = Mage::helper('salesrep')->getGridCellRate($this->getRequest()->getParam('iwd_user_id'), $this->getRequest()->getParam('iwd_linked_product_id'));
        } catch (Exception $e) {
            Mage::logException($e);
        }

        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($responseData));
    }
}