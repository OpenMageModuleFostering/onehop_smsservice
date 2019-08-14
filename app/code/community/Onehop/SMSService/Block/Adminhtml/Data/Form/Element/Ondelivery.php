<?php
/**
 * SMS SMSService
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (GPL2)
 * It is available through the world-wide-web at this URL:
 * https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @category    Onehop
 * @package     Onehop_SMSService
 * @copyright   Copyright (c) 2016 Onehop (http://www.onehop.co)
 * @license     https://www.gnu.org/licenses/gpl-2.0.html  Open Software License (GPL2)
 */

/**
 * data element 
 *
 * @category    Onehop
 * @package     Onehop_SMSService
 * @author      Screen-Magic Mobile Media Inc. (info@onehop.co)
 */

class Onehop_SMSService_Block_Adminhtml_Data_Form_Element_Ondelivery extends Varien_Data_Form_Element_Multiselect
{
    /**
     * bind html in a variable
     * 
     * @return html
     */
    public function getElementHtml()
    {
        $html = '<b>Send Order Completion notification to Buyer when you Submit Invoice.</b>';
        return '<tr>' . $html . '</tr>';
    }
}
