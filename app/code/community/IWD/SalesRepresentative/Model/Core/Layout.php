<?php
class IWD_SalesRepresentative_Model_Core_Layout extends Mage_Core_Model_Layout{


    
    protected function _generateAction($node, $parent)
    {
        if (isset($node['ifconfig']) && ($configPath = (string)$node['ifconfig'])) {
        	if ($configPath=='salesrep/order/representative')
            if (!Mage::getStoreConfigFlag($configPath)) {
                return $this;
            }else{
            	$limit = Mage::getStoreConfig('salesrep/order/limit');
            	if (!empty($limit)){
            		$limit = explode(',', $limit);
            		$_currentUser = Mage::getSingleton('admin/session')->getUser()->getId();
            		if (in_array($_currentUser, $limit)){
            			return $this;
            		}
            	}
            }
        }

 		parent::_generateAction($node, $parent);
    }
}