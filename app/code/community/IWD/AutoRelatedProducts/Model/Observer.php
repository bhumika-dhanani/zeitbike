<?php
class IWD_AutoRelatedProducts_Model_Observer
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
                    $cache->save('true', 'iwd_auto_related_products', array("iwd_auto_related_products"), $lifeTime=5);
                }
            }
        }
    }

    const XML_PATH_BUTTON_TEXT = 'iwd_autorelatedproducts/design/addbutton_textcolor';
    const XML_PATH_BUTTON_TEXT_HOVER = 'iwd_autorelatedproducts/design/addbutton_textcolor_hover';
    const XML_PATH_BUTTON_BG = 'iwd_autorelatedproducts/design/addbutton_background';
    const XML_PATH_BUTTON_BG_HOVER = 'iwd_autorelatedproducts/design/addbutton_background_hover';

    public function generateCssFile()
    {
        $button_text       = trim(Mage::getStoreConfig(self::XML_PATH_BUTTON_TEXT), '#');
        $button_text_hover = trim(Mage::getStoreConfig(self::XML_PATH_BUTTON_TEXT_HOVER), '#');
        $button_bg         = trim(Mage::getStoreConfig(self::XML_PATH_BUTTON_BG), '#');
        $button_bg_hover   = trim(Mage::getStoreConfig(self::XML_PATH_BUTTON_BG_HOVER), '#');

        $file = Mage::getBaseDir('skin').'/frontend/base/default/css/iwd/autorelatedproducts/style_generated.css';
        $handle = fopen($file, 'w');
        $data = "/* This css file was generate automatically by IWD Auto Related Products extension, please dont change it. File regenerated each time admin IWDAutoRelatedProducts  settings changed*/";

        $data.="
.iwd-auto-related-products-slider .button.btn-cart.add-to-cart-design{background:#".$button_bg." !important;}
.iwd-auto-related-products-slider .button.btn-cart.add-to-cart-design:hover{background:#".$button_bg_hover." !important;}
.iwd-auto-related-products-slider .button.btn-cart.add-to-cart-design span{color:#".$button_text." !important;}
.iwd-auto-related-products-slider .button.btn-cart.add-to-cart-design:hover span{color:#".$button_text_hover." !important;}
	 	";
        fwrite($handle, $data);
        fclose($handle);
    }
}
