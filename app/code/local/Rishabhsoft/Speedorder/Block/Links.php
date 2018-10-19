<?php
class Rishabhsoft_Speedorder_Block_Links extends Mage_Core_Block_Template
{

    public function getSpeedorderUrl(){
        $parentBlock = $this->getParentBlock();
        $text = $this->__('Speed Order');
        if($parentBlock){
            $login = Mage::getSingleton( 'customer/session' )->isLoggedIn();
            if($login) {
                $groupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
                if ($groupId == 2) {
                    $parentBlock->addLink($text, 'fast-order', $text, true, array('_secure' => true), 260, null, 'class="top-link-checkout"');
                }
            }
        }
        return $this;


    }
}