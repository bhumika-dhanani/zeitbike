<?php
class IWD_SalesRepresentative_Block_Adminhtml_Order_View extends  Mage_Core_Block_Template{
	
	
	public function __construct(){

		parent::__construct();

		$this->setTemplate('salesrep/order/view/edit_sales.phtml');
	}
	
	public function getRepresentative(){

		$orderId = Mage::app()->getRequest()->getParam('order_id');
		$related = Mage::getModel('salesrep/sales')->getCollection()->addFieldToFilter('iwd_order_id', array('eq'=>$orderId));

		if ($related->getSize()==0){
			return 'N/A';
		}

		$item = $related->getFirstItem();
        $userCollection = Mage::getModel('admin/user')
            ->getCollection()
            ->addFieldToFilter('main_table.user_id', $item->getIwdUserId());
        $userCollection->getSelect()->joinLeft ( array ('link_user' => $userCollection->getTable ( 'salesrep/users' )), 'link_user.iwd_user_id=main_table.user_id', array("IF(link_user.iwd_name IS NULL or link_user.iwd_name = '', CONCAT(main_table.firstname,' ', main_table.lastname), link_user.iwd_name) as username") );
        $name = $userCollection->getFirstItem()->getUsername();
        if (empty($name)){
            $name = 'N/A';
        }
        return $name;
	}
}