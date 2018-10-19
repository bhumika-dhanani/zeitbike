<?php
class IWD_Integracoreb_IndexController extends Mage_Core_Controller_Front_Action
{

    public function indexAction()
    {
        $integracoreb = Mage::getModel('iwdintegracoreb/observer');
        $integracoreb->runShipping();
    }
}