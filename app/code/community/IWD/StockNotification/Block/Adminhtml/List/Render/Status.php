<?php
class IWD_StockNotification_Block_Adminhtml_List_Render_Status extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{


    public function __construct()
    {
        parent::__construct ();
    }

    public function render(Varien_Object $row)
    {

        $_status = $row->getIsNotified ();

        if ($_status){
            return $this->helper('stocknotification')->__('Notified');
        }else{
            return $this->helper('stocknotification')->__('Waiting');
        }

    }

}