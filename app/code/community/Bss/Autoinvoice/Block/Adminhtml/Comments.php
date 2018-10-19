<?php
/**
* BssCommerce Co.
*
* NOTICE OF LICENSE
*
* This source file is subject to the EULA
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://bsscommerce.com/Bss-Commerce-License.txt
* @category   BSS
* @package    Bss_Autoinvoice
* @author     Trung <kiutisuperking@gmail.com>
* @copyright  Copyright (c) 2014-2018 BssCommerce Co. (http://bsscommerce.com)
* @license    http://bsscommerce.com/Bss-Commerce-License.txt
*/
class Bss_Autoinvoice_Block_Adminhtml_Comments extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = '<tr id="row_deferjs_general_help" attr="bss">
        <td  colspan="2" style="padding: 20px 5px;">
            <p>Explore 40 useful <a href="https://bsscommerce.com/magento-extensions.html" target="_blank" style="color:#ea7601;">Magento 1 extensions</a> for all websites!</p><p>More Free Magento Resources from BSS:</p>
			 <p><a href="http://bsscommerce.com/confluence/" target="_blank" style="color:#ea7601;">Magento Tutorial</a></p>
			<p><a href="https://bsscommerce.com/blog" target="_blank" style="color:#ea7601;">Magento Blog</a></p>
        </td>
        
    </tr>';

    return $html;
}
}