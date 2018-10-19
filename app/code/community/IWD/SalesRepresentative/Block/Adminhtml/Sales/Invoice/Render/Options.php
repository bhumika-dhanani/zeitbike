<?php

class IWD_SalesRepresentative_Block_Adminhtml_Sales_Invoice_Render_Options extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    protected static $_statuses;

    protected $_options = false;

    public function render(Varien_Object $row)
    {
        return Mage::helper('salesrep')->__($this->getUsername($row));
    }

    public static function getUsername($row)
    {

        $name = $row->getUsername();
        $name = trim($name);
        if (empty($name)) {
            return 'N/A';
        }

        $id = $row->getIwdUserId();

        $userCollection = Mage::getModel('admin/user')
            ->getCollection()
            ->addFieldToFilter('main_table.user_id', $id);
        $userCollection->getSelect()->joinLeft(array('link_user' => $userCollection->getTable('salesrep/users')), 'link_user.iwd_user_id=main_table.user_id', array("IF(link_user.iwd_name IS NULL or link_user.iwd_name = '', CONCAT(main_table.firstname,' ', main_table.lastname), link_user.iwd_name) as username"));
        $name = $userCollection->getFirstItem()->getUsername();

        if (!empty ($id)) {

            $user = Mage::getModel('salesrep/users')->getCollection()->addFieldToFilter('iwd_user_id', array('eq' => $id))->getFirstItem();
            $color = $user->getIwdColor();

            if ($user->getId() && !empty($color)) {

                return '<span class="salesrep-notice" style="background-color:#' . $color . '"><span>' . $name . '</span></span>';
            } else {
                return '<span class="grid-severity-notice"><span>' . $name . '</span></span>';
            }
        } else {
            return 'N/A';
        }
    }
}