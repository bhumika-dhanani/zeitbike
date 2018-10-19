<?php
class IWD_StockNotification_CustomerController extends Mage_Core_Controller_Front_Action
{
	public function subscriptionsAction()
	{
		
		if (!Mage::getSingleton('customer/session')->isLoggedIn()){
			$this->_redirect('/*/*');
		}
		
		$this->loadLayout();
		$this->_initLayoutMessages('customer/session');
		
		$this->renderLayout();
	}
	
	
	
	
 	public function removeAction()
    {
    	$_customer = Mage::getSingleton('customer/session')->getCustomer();
        $id = (int) $this->getRequest()->getParam('item');
        $item = Mage::getModel('stocknotification/notice')->load($id);

        if($item->getCustomerId() == $_customer->getId()) {
            try {
                $item->delete();    
                Mage::getSingleton('customer/session')->addSuccess(
                			$this->__('Subscription has been removed successfully.')
                );
            }
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('customer/session')->addError(
                    $this->__('An error occurred while deleting the item from subscriptions: %s', $e->getMessage())
                );
            }
            catch(Exception $e) {
                Mage::getSingleton('customer/session')->addError(
                    $this->__('An error occurred while deleting the item from subscriptions.')
                );
            }
        }

      

        $this->_redirectReferer(Mage::getUrl('*/*'));
    }
    
    protected function _redirectReferer($defaultUrl=null)
    {
    
    	$refererUrl = $this->_getRefererUrl();
    	if (empty($refererUrl)) {
    		$refererUrl = empty($defaultUrl) ? Mage::getBaseUrl() : $defaultUrl;
    	}
    
    	$this->getResponse()->setRedirect($refererUrl);
    	return $this;
    }
}