<?php
class IWD_AlsoBought_Model_Observer
{
    public function checkRequiredModules($observer){
        $cache = Mage::app()->getCache();

        if (Mage::getSingleton('admin/session')->isLoggedIn()) {
            if (!Mage::getConfig()->getModuleConfig('IWD_All')->is('active', 'true')){
                if ($cache->load("iwd_also_bought")===false){
                    $message = 'Important: Please setup IWD_ALL in order to finish <strong>IWD Also Bought</strong>  installation.<br />
					Please download <a href="http://iwdextensions.com/media/modules/iwd_all.tgz" target="_blank">IWD_ALL</a> and setup it via Magento Connect.<br />
					Please refer to <a href="https://docs.google.com/document/d/1V-YnTuqptoyaM0Q-fzTWv60Fs-5fEIZwoLNMG_zsrWc/" target="_blank">installation guide</a>';

                    Mage::getSingleton('adminhtml/session')->addNotice($message);
                    $cache->save('true', 'iwd_also_bought', array("iwd_also_bought"), $lifeTime=5);
                }
            }
        }
    }
}
