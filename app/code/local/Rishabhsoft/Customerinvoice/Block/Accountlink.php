<?php

class Rishabhsoft_Customerinvoice_Block_Accountlink extends Mage_Core_Block_Abstract
{
    /**
     * Adds Invoice link to customer account
     */
    public function addLink()
    {
        $parentBlock = $this->getParentBlock();
        if ($parentBlock instanceof Mage_Customer_Block_Account_Navigation) {
            $parentBlock->addLink('customerinvoice',
                'customerinvoice/index/list', $this->__('My Invoices'),
                array('_secure' => Mage::app()->getStore(true)->isCurrentlySecure())
            );
        }
    }
}