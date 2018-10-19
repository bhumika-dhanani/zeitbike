<?php
class IWD_SalesRepresentative_Block_Adminhtml_Users_List_Render_Rate extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract{
	protected static $_statuses;
	
	public function __construct() {		
		parent::__construct ();
	}
	
	public function render(Varien_Object $row) {
		

		$userId = Mage::app()->getRequest()->getParam('id');
		
		$current = Mage::getModel('salesrep/users')->load($userId);
		$useGlobal = $current->getGlobal();
		
		
		$collection = Mage::getModel('salesrep/link')->getCollection()
						->addFieldToFilter('iwd_user_id',array('eq'=>$userId))
						->addFieldToFilter('iwd_linked_product_id',array('eq'=>$row->getId()));
		$item = $collection->getFirstItem();
		
		if (!$item->getId()){
			// if current item not assigned to user return empty data			
			return '';
		}else{
			// if current item assign to user
			$user = Mage::registry('current_user');
			$rateType = $item->getProductRateType();
	
			if (($rateType!=1 && $rateType!=2) || $user->getGlobal()){
				
				//Percent
				if ($user->getRateType()==1){
					$html = $user->getPercentRate() . '% per item <small class="notice">(GLOBAL)</small>';
					
					if (!$useGlobal){
                        $html .=  '<a data-user="' . $user->getId() . '" data-product="' . $row->getId() . '" class="update-product-rate" href=""><i class="fa fa-pencil-square-o"></i> ' . Mage::helper ( 'salesrep' )->__ ( 'Update' ) . '</a>';
					}
		  			
		  			return $html;
				}
				//Fixed
				if ($user->getRateType()==2){
					$currency_code = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
					$html = $user->getFixedRate() .  $currency_code . ' per item <small class="notice">(GLOBAL)</small>';

					if (!$useGlobal){
                        $html .= '<a data-user="' . $user->getId() . '" data-product="' . $row->getId() . '" class="update-product-rate" href=""><i class="fa fa-pencil-square-o"></i> ' . Mage::helper ( 'salesrep' )->__ ( 'Update' ) . '</a>';
					}
					return $html;
				}
				
			}else{
				
				//Percent
				if ($rateType==1){
					$html = $item->getProductPercentRate() . '% per item ';
					if (!$useGlobal){
					   $html .= '<a data-user="' . $user->getId() . '" data-product="' . $row->getId() . '" class="update-product-rate" href=""><i class="fa fa-pencil-square-o"></i> ' . Mage::helper ( 'salesrep' )->__ ( 'Update' ) . '</a>';
					}
					return $html;
				}
				
				//Fixed
				if ($rateType==2){
					$currency_code = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
					$html = $item->getProductFixedRate() .  $currency_code . ' per item ';
					if (!$useGlobal){
					   $html .= '<a data-user="' . $user->getId() . '" data-product="' . $row->getId() . '" class="update-product-rate" href=""><i class="fa fa-pencil-square-o"></i> ' . Mage::helper ( 'salesrep' )->__ ( 'Update' ) . '</a>';
					}
					return $html;
				}
				
			}			
		}
	}
	
	
}