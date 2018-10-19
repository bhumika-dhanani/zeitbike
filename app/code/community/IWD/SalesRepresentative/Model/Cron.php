<?php
class IWD_SalesRepresentative_Model_Cron{
	
	const DAILY_TYPE = 1;
	const WEEKLY_TYPE = 2;
	const MONTHLY_TYPE = 3;
	
	
	const XML_PATH_EMAIL_SENDER     = 'stocknotification/email/sender_email_identity';
	const XML_PATH_EMAIL_TEMPLATE   = 'stocknotification/email/email_template';

	
	public function daily(){
		$this->orders(self::DAILY_TYPE);
		$this->products(self::DAILY_TYPE);
	}
	
	public function weekly(){
		$this->orders(self::WEEKLY_TYPE);
		$this->products(self::WEEKLY_TYPE);
	}
	
	public function monthly(){
		$this->orders(self::MONTHLY_TYPE);
		$this->products(self::MONTHLY_TYPE);
	}
	
	
	protected function orders($type) {
		$collection = Mage::getModel ( 'salesrep/users' )->getCollection ()->addFieldToFilter ( 'order_notification_report', array (
				'eq' => $type 
		) );
		$collection->getSelect ()->joinLeft ( array (
				'user_table' => $collection->getTable ( 'admin/user' ) 
		), 'main_table.iwd_user_id=user_table.user_id', array (
				"CONCAT(`firstname`,' ', `lastname`) as username",
				"email" 
		) );
		
		foreach ( $collection as $user ) {
			;
			$fileExport = $this->exportSalesCsvOrders ( $type, $user );
			
			switch ($type) {
				case 1 :
					$this->sendEmailNotification ( $user, $fileExport, 'salerep_report_order_daily' );
					break;
				
				case 2 :
					$this->sendEmailNotification ( $user, $fileExport, 'salerep_report_order_weekly' );
					break;
				
				case 3 :
					$this->sendEmailNotification ( $user, $fileExport, 'salerep_report_order_monthly' );
					break;
			}
		}
	}
	
	
	
	protected function products($type) {
		$collection = Mage::getModel ( 'salesrep/users' )->getCollection ()->addFieldToFilter ( 'product_notification_report', array (
				'eq' => $type
		) );
		$collection->getSelect ()->joinLeft ( array (
				'user_table' => $collection->getTable ( 'admin/user' )
		), 'main_table.iwd_user_id=user_table.user_id', array (
				"CONCAT(`firstname`,' ', `lastname`) as username",
				"email"
		) );
	
		foreach ( $collection as $user ) {
	
			$fileExport = $this->exportSalesCsvProducts ( $type, $user );
				
			switch ($type) {
				case 1 :
					$this->sendEmailNotification ( $user, $fileExport, 'salerep_report_product_daily' );
					break;
	
				case 2 :
					$this->sendEmailNotification ( $user, $fileExport, 'salerep_report_product_weekly' );
					break;
	
				case 3 :
					$this->sendEmailNotification ( $user, $fileExport, 'salerep_report_product_monthly' );
					break;
			}
		}
	}

	
	public function exportSalesCsvOrders($type, $user){
		
		$grid       = Mage::app()->getLayout()->createBlock('salesrep/adminhtml_report_orders_orders_grid');

		$this->_initReportOrders($grid, $type, $user);		
		return $fileExport = $grid->getCsvFile();
		
	}
	
	public function exportSalesCsvProducts($type, $user){
	
		$grid       = Mage::app()->getLayout()->createBlock('salesrep/adminhtml_report_products_products_grid');
	
		$this->_initReportProducts($grid, $type, $user);
		return $fileExport = $grid->getCsvFile();
	
	}
	
	private function getDataFrom($type){
		switch ($type){
			case 1:
				$date = new Zend_Date();
				$date->subDay(1);
				return date('Y-m-d', $date->getTimestamp());
			break;
			
			case 2:
				$date = new Zend_Date();
				$date->subWeek(1);
				return date('Y-m-d', $date->getTimestamp());
			break;
			
			case 3:
				$date = new Zend_Date();
				$date->subMonth(1);
				return date('Y-m-d', $date->getTimestamp());
			break;
				
				
		}
	}
	
	private function getDataTo($type){
		switch ($type){
			case 1:
				$date = new Zend_Date();
				$date->subDay(1);
				return date('Y-m-d', $date->getTimestamp());
			break;
					
			case 2:
				$date = new Zend_Date();
				$date->subDay(1);
				return date('Y-m-d', $date->getTimestamp()) ;
				break;
					
			case 3:
				$date = new Zend_Date();
				$date->subDay(1);
				return date('Y-m-d', $date->getTimestamp());
			break;

		}
	}
	
	public function _initReportOrders($blocks, $type, $user){
		if (!is_array($blocks)) {
			$blocks = array($blocks);
		}
		
		$requestData = array(
					
					'from'	=>$this->getDataFrom($type),
					'to'	=>$this->getDataTo($type),				
		);
		Mage::getSingleton('admin/session')->setUserRepresentative($user->getUserId());
	
		$params = new Varien_Object();
	
		foreach ($requestData as $key => $value) {
			if (!empty($value)) {
				$params->setData($key, $value);
			}
		}
	
		foreach ($blocks as $block) {
			if ($block) {
				$block->setPeriodType($params->getData('period_type'));
				$block->setFilterData($params);
			}
		}
	
		return $this;
	}
	
	public function _initReportProducts($blocks, $type, $user){
		if (!is_array($blocks)) {
			$blocks = array($blocks);
		}
	
		$requestData = array(
					
				'from'	=>$this->getDataFrom($type),
				'to'	=>$this->getDataTo($type),
		);
		
		Mage::getSingleton('admin/session')->setUserRepresentative($user->getId());
	
		$params = new Varien_Object();
	
		foreach ($requestData as $key => $value) {
			if (!empty($value)) {
				$params->setData($key, $value);
			}
		}
	
		foreach ($blocks as $block) {
			if ($block) {
				$block->setPeriodType($params->getData('period_type'));
				$block->setFilterData($params);
			}
		}
	
		return $this;
	}
	
	
	protected function sendEmailNotification($user, $fileExport, $template){
	
		$translate = Mage::getSingleton('core/translate');
		/* @var $translate Mage_Core_Model_Translate */
		$translate->setTranslateInline(false);
	
		$postObject = new Varien_Object();
		$postObject->setData(array());
	
		$mailTemplate = Mage::getModel('core/email_template');
		/* @var $mailTemplate Mage_Core_Model_Email_Template */
		$mailTemplate->setDesignConfig(array('area' => 'frontend'));
		
		$mailTemplate->getMail()
				        ->createAttachment(
				            file_get_contents($fileExport['value']),
				            Zend_Mime::TYPE_OCTETSTREAM,
				            Zend_Mime::DISPOSITION_ATTACHMENT,
				            Zend_Mime::ENCODING_BASE64,
				            basename('orders.csv')
				        );
		
		
		$mailTemplate->sendTransactional(
					$template,
					'sales',
					$user->getEmail(),					
					null,
					array('data' => $postObject)
		);
	
		if (!$mailTemplate->getSentSuccess()) {
			throw new Exception();
		}
	
		
		$ioAdapter = new Varien_Io_File();
		$ioAdapter->open(array('path' => $ioAdapter->dirname($fileExport['value'])));
		$ioAdapter->rm($fileExport['value']);
		
		$translate->setTranslateInline(true);
		
	}	
	
}