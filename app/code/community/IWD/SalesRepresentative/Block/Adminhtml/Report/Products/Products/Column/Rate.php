<?php
class IWD_SalesRepresentative_Block_Adminhtml_Report_Products_Products_Column_Rate 
extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text{
	
	public function render(Varien_Object $row) {

			// if current item assign to user
			$rateType = $row->getProductRateType();
	
			if (($rateType!=1 && $rateType!=2) || $row->getGlobal()){
	
				//Percent
				if ($row->getRateType()==1){
					return $row->getPercentRate() . '% per item <small class="notice">(GLOBAL)</small>';
				}
				//Fixed
				if ($row->getRateType()==2){
					$currency_code = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
					return $row->getFixedRate() .  $currency_code . ' per item <small class="notice">(GLOBAL)</small>';
				}
	
			}else{
	
				//Percent
				if ($rateType==1){
					return $row->getProductPercentRate() . '% per item';
				}
	
				//Fixed
				if ($rateType==2){
					$currency_code = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
					return $row->getProductFixedRate() .  $currency_code . ' per item';
				}
	
			}

	}

}