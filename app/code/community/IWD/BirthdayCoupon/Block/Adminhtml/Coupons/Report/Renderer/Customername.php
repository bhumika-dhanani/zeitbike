<?php
class IWD_BirthdayCoupon_Block_Adminhtml_Coupons_Report_Renderer_Customername extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        return $row["customer_firstname"] . " " . $row["customer_lastname"];
    }
}