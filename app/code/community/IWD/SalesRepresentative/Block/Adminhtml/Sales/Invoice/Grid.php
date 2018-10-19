<?php

class IWD_SalesRepresentative_Block_Adminhtml_Sales_Invoice_Grid extends Mage_Adminhtml_Block_Sales_Invoice_Grid
{

    protected $_options = false;

    protected function _prepareColumns()
    {


        $this->addColumn('iwd_username', array(
            'header' => Mage::helper('salesrep')->__('Representative'),
            'index' => 'iwd_username',
            'type' => 'options',
            'filter_index' => 'user.user_id',
            'sortable' => false,
            'width' => '150px',
            'options' => $this->_getOptions(),
            'renderer' => 'IWD_SalesRepresentative_Block_Adminhtml_Sales_Invoice_Render_Options'
        ));
        return parent::_prepareColumns();
    }

    protected function _getOptions()
    {

        if (!$this->_options) {
            $users = Mage::getSingleton('core/session')->getGridUsers();
            if (!$users) {
                $userList = Mage::getModel('admin/user')->getCollection()->addFieldToFilter('is_active', array('eq' => 1));
                $users = array();
                foreach ($userList as $user) {
                    $users[$user->getId()] = $user->getFirstname() . ' ' . $user->getLastname();
                }
                Mage::getSingleton('core/session')->setGridUsers($users);
            }

            $methods = array();


            foreach ($users as $id => $user) {
                $methods[$id] = $user;
            }
            $this->_options = $methods;
        }

        return $this->_options;
    }

}
