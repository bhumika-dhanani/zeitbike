<?php
class IWD_AAC_Block_Dialog extends Mage_Core_Block_Template {
	
	const XML_PATH_ENABLED = 'aac/global/status';
	
	const XML_PATH_SHOW_DROPDOWN = 'aac/default/show_dropdown';
	
	const XML_PATH_ITEMS = 'aac/dev/items';
	
	const XML_PATH_BACKGROUND = 'aac/dev/background';
	const XML_PATH_FOREGROUND = 'aac/dev/foreground';
	public function getJsonConfig() {
		$_scheme = Mage::app ()->getRequest ()->getScheme ();
		
		if ($_scheme == 'https') {
			$_secure = true;
		} else {
			$_secure = false;
		}
		
		$config = array ();
		
		$config ['baseUrl'] = $this->getUrl ( '', array (
				'_secure',
				$_secure 
		) );
		
		$config ['removeShoppingCartUrl'] = $this->getUrl ( 'aac/cart/remove', array (
				'_secure' => $_secure 
		) );
		
		$config ['isLoggedIn'] = ( int ) Mage::getSingleton ( 'customer/session' )->isLoggedIn ();
		
		$config ['addToWishlistUrl'] = $this->getUrl ( 'aac/wishlist/add', array (
				'_secure' => $_secure 
		) );
		
		$config ['removeWishlistUrl'] = $this->getUrl ( 'aac/wishlist/remove', array (
				'_secure' => $_secure 
		) );
		
		$config ['enabled'] = ( bool ) Mage::getStoreConfig ( self::XML_PATH_ENABLED );
		
		$config ['showDropdown'] = ( bool ) Mage::getStoreConfig ( self::XML_PATH_SHOW_DROPDOWN );
		
		$version =Mage::getVersionInfo();
		if ($version['minor']>=9){
		  $config ['useDefaultDropDown'] = true;
		}else{
		    $config ['useDefaultDropDown'] = false;
		}
		
		$items = Mage::getStoreConfig ( self::XML_PATH_ITEMS );
		$config['items'] = trim($items);
		return Mage::helper ( 'core' )->jsonEncode ( $config );
	}
	
	public function getBackground(){
		return Mage::getStoreConfig(self::XML_PATH_BACKGROUND);
	}
	
	public function getForeground(){
		return Mage::getStoreConfig(self::XML_PATH_FOREGROUND);
	}
	
}