<?php
class IWD_AutoRelatedProducts_Adminhtml_BlocksController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('system')
            ->_title($this->__('IWD - Auto Related Products'));

        $this->_addBreadcrumb(
            Mage::helper('iwd_autorelatedproducts')->__('Auto Related Products'),
            Mage::helper('iwd_autorelatedproducts')->__('Auto Related Products')
        );

        return $this;
    }

    public function indexAction()
    {
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('iwd_autorelatedproducts/adminhtml_blocks'));
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('system')
            ->_title($this->__('IWD - Add Block'));

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->_addContent($this->getLayout()->createBlock('iwd_autorelatedproducts/adminhtml_blocks_edit'))
            ->_addLeft($this->getLayout()->createBlock('iwd_autorelatedproducts/adminhtml_blocks_edit_tabs'));

        $this->renderLayout();
    }

    public function editAction()
    {
        $block_id = $this->getRequest()->getParam('id');
        if (empty($block_id)) {
            $this->_redirect('*/*/index');
            return;
        }

        $block = Mage::getModel('iwd_autorelatedproducts/blocks')->load($block_id);

        $block->getRelatedConditions()->getConditions()->setJsFormObject('r_rule_conditions_fieldset');
        $block->getCurrentConditions()->getConditions()->setJsFormObject('c_rule_conditions_fieldset');
        $block->getShoppingCartConditions()->getConditions()->setJsFormObject('cart_rule_conditions_fieldset');

        Mage::register('iwd_related_products_data', $block);

        $this->loadLayout()
            ->_setActiveMenu('system')
            ->_title($this->__('IWD - Edit Block'));

        $this->_addContent($this->getLayout()->createBlock('iwd_autorelatedproducts/adminhtml_blocks_edit'))
            ->_addLeft($this->getLayout()->createBlock('iwd_autorelatedproducts/adminhtml_blocks_edit_tabs'));

        $this->renderLayout();
    }

    protected function _saveBlock($params)
    {
        $block = Mage::getModel('iwd_autorelatedproducts/blocks');

        /** new/edit **/
        if (isset($params['id']) && !empty($params['id'])) {
            $block = $block->load($params['id']);
        } else {
            unset($params['id']);
        }

        $block->setPostData($params);

        //var_dump($block->getData()); die('===');

        $block->save();

        Mage::register('iwd_related_products_data', $block);

        return $block;
    }

    protected function _successRedirectAfterSave($block)
    {
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('iwd_autorelatedproducts')->__('Block was successfully saved'));
        Mage::getSingleton('adminhtml/session')->setFormData(false);

        if ($this->getRequest()->getParam('back')) {
            $this->_redirect('*/*/edit', array('id' => $block->getId()));
            return;
        }

        $this->_redirect('*/*/');
    }

    protected function _errorRedirectAfterSave($error)
    {
        Mage::getSingleton('adminhtml/session')->addError($error->getMessage());
        if ($block_id = $this->getRequest()->getParam('id')) {
            $data = Mage::getModel('iwd_autorelatedproducts/blocks')->load($block_id);
            Mage::getSingleton('adminhtml/session')->setFormData($data->getData());
            $this->_redirect('*/*/edit', array('id' => $block_id));
            return;
        }

        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('iwd_autorelatedproducts')->__('Sorry, I can not save the block.'));
        $this->_redirect('*/*/');
    }

    public function saveAction()
    {
        /*** STEP 1. SELECTED TYPE. GO TO STEP 2 ***/
        if ($this->getRequest()->getParam('next_step')) {
            $block_type = $this->getRequest()->getParam('block_type');
            $this->_redirect('*/*/new', array('block_type' => $block_type));
            return;
        }

        /*** STEP2. SAVE BLOCK ***/
        try {
            $params = $this->getRequest()->getParams();

            $block = $this->_saveBlock($params);

            $this->_successRedirectAfterSave($block);
            return;
        } catch (Exception $e) {
            $this->_errorRedirectAfterSave($e);
            return;
        }

        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('iwd_autorelatedproducts')->__('Sorry, I can not save the block.'));
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        $block_id = $this->getRequest()->getParam('id');

        if(!empty($block_id) && is_numeric($block_id))
        {
            try {
                $block = Mage::getModel('iwd_autorelatedproducts/blocks')->load($block_id);
                if(!empty($block))
                    $block->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')
                    ->__('Block was successfully deleted'));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massDeleteAction()
    {
        $blocks = $this->getRequest()->getParam('blocks');

        if (empty($blocks) || !is_array($blocks)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($blocks as $id) {
                    $block = Mage::getModel('iwd_autorelatedproducts/blocks')->load($id);
                    $block->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')
                    ->__('Total of %d record(s) were successfully deleted', count($blocks)));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');    }

    public function massStatusAction()
    {
        $blocks = $this->getRequest()->getParam('blocks');
        $status = $this->getRequest()->getParam('status');

        if (empty($blocks) || !is_array($blocks)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($blocks as $id) {
                    $block = Mage::getModel('iwd_autorelatedproducts/blocks')->load($id);
                    $block->setStatus($status)->save();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')
                    ->__('Total of %d record(s) were successfully updated', count($blocks)));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Blocks grid (for getGridUrl)
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('iwd_autorelatedproducts/adminhtml_blocks_grid')->toHtml()
        );
    }


    /**
     * Featured Products edit grid (for getGridUrl)
     */
    public function grid_featured_productsAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('iwd_autorelatedproducts/adminhtml_blocks_edit_grid_featured')->toHtml()
        );
    }

    /**
     * Related Products edit grid (for getGridUrl)
     */
    public function grid_related_productsAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('iwd_autorelatedproducts/adminhtml_blocks_edit_grid_related')->toHtml()
        );
    }

    /**
     * Related Products edit grid (for getGridUrl)
     */
    public function grid_current_productsAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('iwd_autorelatedproducts/adminhtml_blocks_edit_grid_current')->toHtml()
        );
    }

    public function newRelatedConditionHtmlAction()
    {
        $this->newConditionsHtml('related_conditions');
    }

    public function newCurrentConditionHtmlAction()
    {
        $this->newConditionsHtml('current_conditions');
    }

    public function newCartConditionHtmlAction()
    {
        $this->newConditionsHtml('conditions');
    }

    protected function newConditionsHtml($prefix)
    {
        $id = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];

        $model = Mage::getModel($type)
            ->setId($id)
            ->setType($type)
            ->setRule(Mage::getModel('catalogrule/rule'))
            ->setPrefix($prefix);
        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        if ($model instanceof Mage_Rule_Model_Condition_Abstract) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        $this->getResponse()->setBody($html);
    }
    /*iwd fix*/
    protected function _isAllowed()
    {
        return true;
    }
}