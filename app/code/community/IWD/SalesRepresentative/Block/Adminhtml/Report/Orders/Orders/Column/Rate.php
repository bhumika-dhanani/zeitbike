<?php
class IWD_SalesRepresentative_Block_Adminhtml_Report_Orders_Orders_Column_Rate 
extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text{
	
	public function render(Varien_Object $row) {

			// if current item assign to user
			$rateType = $row->getRateTypeOrder();
	
			//Percent
			if ($rateType==1){
				return $row->getPercentRateOrder() . '% per order';
			}

			//Fixed
			if ($rateType==2){
				$currency_code = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
				return $row->getFixedRateOrder() .  $currency_code . ' per order';
			}
	
			

	}

}