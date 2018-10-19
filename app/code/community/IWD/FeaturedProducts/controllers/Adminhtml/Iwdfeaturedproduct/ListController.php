<?php
class IWD_FeaturedProducts_Adminhtml_Iwdfeaturedproduct_ListController extends Mage_Adminhtml_Controller_Action {
	
	public function indexAction() {
		$this->loadLayout ();
		$this->renderLayout ();
	}
	
	public function gridAction() {
		
		$this->getResponse ()->setBody ( $this->getLayout ()->createBlock ( 'iwdfeaturedproduct/adminhtml_list_grid' )->toHtml () );
	
	}
	
	public function saveAction() {
		$data = $this->getRequest ()->getPost ();
		$collection = Mage::getModel ( 'catalog/product' )->getCollection ();
		$storeId = $this->getRequest ()->getParam ( 'store', 0 );
		
		parse_str ( $data ['featured_products'], $featured_products );
		
		$collection->addIdFilter ( array_keys ( $featured_products ) );
		
		try {
			
			foreach ( $collection->getItems () as $product ) {
				
				$product->setData ( 'iwd_featured_product', $featured_products [$product->getEntityId ()] );
				$product->setStoreId ( $storeId );
				$product->save ();
			}
			
			$this->_getSession ()->addSuccess ( $this->__ ( 'Featured product was successfully saved.' ) );
			$this->_redirect ( '*/*/index', array ('store' => $this->getRequest ()->getParam ( 'store' ) ) );
		
		} catch ( Exception $e ) {
			$this->_getSession ()->addError ( $e->getMessage () );
			$this->_redirect ( '*/*/index', array ('store' => $this->getRequest ()->getParam ( 'store' ) ) );
		}
	
	}
	
	protected function _validateSecretKey() {
		return true;
	}
	
	protected function _isAllowed() {
		return Mage::getSingleton ( 'admin/session' )->isAllowed ( 'admin/catalog/featuredproduct' );
	}
}