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
 * SMS automation form.
 *
 * @category    Onehop
 * @package     Onehop_SMSService
 * @author      Screen-Magic Mobile Media Inc. (info@onehop.co)
 */

class Onehop_SMSService_Block_Adminhtml_Smsautomation extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Onehop Constructor.
     */
    public function __construct()
    {
        $this->_objectId   = 'page_id';
        $this->_blockGroup = 'smsservice';
        $this->_controller = 'adminhtml';
        
        parent::__construct();
        
        $this->_removeButton('reset');
        $this->_removeButton('save');
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('smsservice')->__('SMS Automation');
    }
}
