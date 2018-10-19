<?php
class IWD_StockNotification_JsonController extends Mage_Core_Controller_Front_Action
{
	
	
	public function requestNotificationAction(){

		$request = $this->getRequest()->getParams();
		
		try{
			$result = Mage::helper('stocknotification')->saveRequest($request);
			
			
			$response = array(
					'error'=>false,
					'message'=>$this->__('Thank you for your interest in this item. You will be notified by email when it becomes available.')
			);
			
		}catch(Exception $e){
				$response = array(
						'error'=>true,
						'message'=>$e->getMessage()
				);
		}
		
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
		
	}
}