<?php
require_once "Mage/Cms/controllers/PageController.php";
class Rishabhsoft_Cms_PageController extends Mage_Cms_PageController
{
    /**
     * View CMS page action
     *
     */

    public function viewAction()
    {
        $pageId = $this->getRequest()
            ->getParam('page_id', $this->getRequest()->getParam('id', false));
        if (!Mage::helper('cms/page')->renderPage($this, $pageId)) {
            $this->_forward('noRoute');
        }
        /* If customer is not logged in as wholesale then they are not able to visit speed order page */
        /*To do task : We need to find out alternate way to display error page if customer is not logged in as wholesale*/
        if($pageId == 11){
            $login = Mage::getSingleton( 'customer/session' )->isLoggedIn();
            if($login) {
                $groupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
                if ($groupId != 2) {
                    $url=str_replace('wholesale/','',Mage::getUrl('fast-order'));
                    $this->_redirect($url);

                }else{
                    $url = Mage::getUrl('fast-order');
                    if(strpos($url,'wholesale') === false) {
                        $url = str_replace('fast-order', 'wholesale/fast-order', Mage::getUrl('fast-order'));
                        Mage::app()->getFrontController()->getResponse()->setRedirect($url);
                    }
                }
            }else{
                $url = Mage::getUrl('fast-order');
                if(strpos($url,'wholesale') !== false) {
                    $url = str_replace('wholesale/', '', Mage::getUrl('fast-order'));
                    Mage::app()->getFrontController()->getResponse()->setRedirect($url);
                }
            }
        }

    }
}
