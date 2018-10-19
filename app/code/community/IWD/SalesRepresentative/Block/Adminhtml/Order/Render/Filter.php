<?php
class IWD_SalesRepresentative_Block_Adminhtml_Order_Render_Filter extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select {
	

	
	protected $_options = false;
	
	public function _getOptions(){

		if(!$this->_options) {
            $only_this_user = false;
            $user = Mage::getSingleton('admin/session')->getUser();
            if($user){
                $item = Mage::getModel('salesrep/users')->load($user->getId(),'iwd_user_id');
                if($item){
                    if($item->getIwdShowUserOrdersOnly()){
                        if($user->getRole()->getId() != Mage::getStoreConfig('salesrep/order/skip_restrict')){
                            $only_this_user = true;
                        }
                    }
                }
            }
            if(!$only_this_user){
                $users = Mage::getSingleton ( 'core/session' )->getGridUsers ();
                if (!$users){

                    $userList = Mage::getModel('admin/user')->getCollection()->addFieldToFilter('is_active', array('eq'=>1));
                    $users = array();
                    foreach($userList as $user){
                        $users[$user->getId()] = $user->getFirstname() . ' ' . $user->getLastname();
                    }

                    Mage::getSingleton('core/session')->setGridUsers($users);
                }


                $methods = array();
                $methods[] = array(
                    'value' =>  '',
                    'label' =>  ''
                );

                foreach($users as $id=>$user){
                    $methods[] = array(
                        'value' =>  $id,
                        'label' =>  $user
                    );
                }
            }else{
                $methods[] = array(
                    'value' =>  $user->getId(),
                    'label' =>  $user->getFirstname() . ' ' . $user->getLastname()
                );
            }
			$this->_options = $methods;
		}

		return $this->_options;
	}
	
}