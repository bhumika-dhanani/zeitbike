<?php
class IWD_StockNotification_Adminhtml_Stocknotification_ProductController extends Mage_Adminhtml_Controller_Action{

	protected function _initProduct()
	{
		$productId  = (int) $this->getRequest()->getParam('id');
		$product    = Mage::getModel('catalog/product')->setStoreId($this->getRequest()->getParam('store', 0));
	
		if (!$productId) {
			if ($setId = (int) $this->getRequest()->getParam('set')) {
				$product->setAttributeSetId($setId);
			}
	
			if ($typeId = $this->getRequest()->getParam('type')) {
				$product->setTypeId($typeId);
			}
		}
	
		$product->setData('_edit_mode', true);
		if ($productId) {
			try {
				$product->load($productId);
			} catch (Exception $e) {
				$product->setTypeId(Mage_Catalog_Model_Product_Type::DEFAULT_TYPE);
				Mage::logException($e);
			}
		}

		Mage::register('product', $product);
		Mage::register('current_product', $product);
		Mage::getSingleton('cms/wysiwyg_config')->setStoreId($this->getRequest()->getParam('store'));
		return $product;
	}

 	
    public function listAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->getLayout()->getBlock('catalog.product.edit.tab.stocknotification');           
        $this->renderLayout();
    }

   
    public function gridAction()
    {
    	$this->_initProduct();
        $this->loadLayout();
        $this->getLayout()->getBlock('catalog.product.edit.tab.stocknotification');
        $this->renderLayout();
    }

	protected function _isAllowed()
	{
		return Mage::getSingleton('admin/session')->isAllowed('system/iwdall/stocknotification');
	}
}