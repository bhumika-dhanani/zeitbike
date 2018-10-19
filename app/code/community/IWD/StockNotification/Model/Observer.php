<?php
class IWD_StockNotification_Model_Observer{

	const XML_PATH_EMAIL_SENDER     = 'stocknotification/email/sender_email_identity';
	const XML_PATH_EMAIL_TEMPLATE   = 'stocknotification/email/email_template';
	const XML_PATH_ENABLED          = 'stocknotification/default/status';
	
	public function verifyStockStatus($observer) {
		
		if( !Mage::getStoreConfigFlag(self::XML_PATH_ENABLED) ) {
			return;
		}
		
		$_product  = $observer->getProduct();
		
		if($_product->_isObjectNew){
			return;
		}
		
		
		
		$status = $_product->getStatus();	
		if ($status==2){
			return;
		}
		
		$stockItem = $_product->getStockItem();		
		if (empty($stockItem)){
			return;
		}
		if (!$stockItem->getIsInStock()){ 
		 	return;
		}
		
		$collection = Mage::getModel('stocknotification/notice')->getCollection()
								->addFieldToFilter('product_id', array('eq'=>$_product->getId()))
								->addFieldToFilter('is_notified', array('eq'=>'0'));
	

		foreach($collection as $_item){
			try{
				$this->sendEmailNotification($_item);
				$this->updateStatus($_item);
			}catch(Exception $e){
				Mage::logException($e);
			}

		}
		
		
		
		
	}
	
	
	public function verifyStockStatusBundleProduct(){
		
		$collection = Mage::getModel('stocknotification/notice')->getCollection()		
								->addFieldToFilter('is_notified', array('eq'=>'0'))
								->addFieldToFilter('parent_id', array('null'=>true));
		
		foreach($collection as $_item){
			$model  = Mage::getModel('catalog/product')->setStoreId($_item->getStoreId());
			$product = $model->load($_item->getProductId());
			if ($product->getTypeId()=='bundle'){
				if ($product->isSaleable()){
					try{
						$this->sendEmailNotification($_item);
						$this->updateStatus($_item);
					}catch(Exception $e){
						Mage::logException($e);
					}
				}
			}
		}
	}
	
	
	protected function updateStatus($_item){
		$model = Mage::getModel('stocknotification/notice')->load($_item->getId());
		$model->setData('is_notified','1');
		try{
			$model->save();
		}catch(Exception $e){			
			Mage::logException($e);
		}
	}
	
	protected function sendEmailNotification($_item){
		
		$productData = $this->prepareProductData($_item);
		
		$translate = Mage::getSingleton('core/translate');
		/* @var $translate Mage_Core_Model_Translate */
		$translate->setTranslateInline(false);
		
		$postObject = new Varien_Object();
		$postObject->setData($productData);
		
		
		$mailTemplate = Mage::getModel('core/email_template');
		/* @var $mailTemplate Mage_Core_Model_Email_Template */
		$mailTemplate->setDesignConfig(array('area' => 'frontend'))
						->setReplyTo(Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER))
						->sendTransactional(
								Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
								Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
								$_item->getEmail(),
								null,
								array('data' => $postObject)
						);
		
		if (!$mailTemplate->getSentSuccess()) {
			throw new Exception();
		}
		
		$translate->setTranslateInline(true);
	}
	
	private function prepareProductData($item){
		$data = array();
		$model  = Mage::getModel('catalog/product')->setStoreId($item->getStoreId());
		$product = $model->load($item->getProductId());
		$data['name'] = $product->getName();
		
		if ($item->getParentId()){
            $model  = Mage::getModel('catalog/product')->setStoreId($item->getStoreId());
			$parent = $model->load($item->getParentId());
			$data['url'] = $parent->getProductUrl();
		}else{
			$data['url'] = $product->getProductUrl();
		}
		
		return $data;
	}
	
	
	public function checkRequiredModules(){
		$cache = Mage::app()->getCache();
		if (Mage::getSingleton('admin/session')->isLoggedIn()) {
			if (!Mage::getConfig()->getModuleConfig('IWD_All')->is('active', 'true')){			
					
					if ($cache->load("iwd_outofstock")===false){
						$message = 'Important: Please setup IWD_ALL in order to finish <strong>IWD Out of Stock</strong>  installation.<br />
									Please download <a href="http://iwdextensions.com/media/modules/iwd_all.tgz" target="_blank">IWD_ALL</a> and setup it via Magento Connect.<br />
									Please refer to installation <a href="https://docs.google.com/document/d/1fm4ImbXIpR_RPhKsybkptIEXE7mvhScQFn0rUXWQI5I/edit" target="_blank">guide</a>';
					
						Mage::getSingleton('adminhtml/session')->addNotice($message);
						$cache->save('true', 'iwd_outofstock', array("iwd_outofstock"), $lifeTime=3);
					}
					
			}
		}
	}
	
}