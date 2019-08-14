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
 * Standard Helper
 *
 * @category    Onehop
 * @package     Onehop_SMSService
 * @author      Screen-Magic Mobile Media Inc. (info@onehop.co)
 */

class Onehop_SMSService_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * Get standard configuration model.
     *
     * @return Onehop_SMSService_Helper_Model_Config
     */
    protected function _getConfig()
    {
        return Mage::getSingleton('smsservice/config');
    }
}
