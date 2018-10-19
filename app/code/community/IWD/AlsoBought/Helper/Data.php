<?php
class IWD_AlsoBought_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_version = 'CE';

    public function isAvailableVersion(){
        $mage = new Mage();
        if (!is_callable(array($mage, 'getEdition'))){
            $edition = 'Community';
        }else{
            $edition = Mage::getEdition();
        }
        unset($mage);

        if ($edition=='Enterprise' && $this->_version=='CE'){
            return false;
        }
        return true;
    }
}
