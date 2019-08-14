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
 * Gateway
 *
 * @category    Onehop
 * @package     Onehop_SMSService
 * @author      Screen-Magic Mobile Media Inc. (info@onehop.co)
 */
class Onehop_SMSService_Model_System_Config_Source_Smsautomation
{
    /**
     * retrive rule set data from database
     *
     * @return array
     */
    public function getDBsmsAutomation()
    {
        $config     = Mage::getSingleton('smsservice/config');
        $tablename  = Mage::getSingleton('core/resource')->getTableName('onehop_sms_rulesets');
        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');

        $selectRules = $connection->select()->from(array(
            $tablename
            ), array(
            '*'
        ));
        $query       = $connection->query($selectRules);
        $rows        = array();
        while ($row = $query->fetch()) {
            array_push($rows, $row);
        }

        return $rows;
    }

    /**
     * get actual data from $mainarray array
     * using $automationkey and $fieldname variables
     *
     * @param array  $mainarray
     * @param string $automationkey
     * @param string $fieldname
     *
     * @return string
     */
    public function getValue($mainarray, $automationkey, $fieldname)
    {
        $returnval = '';
        foreach ($mainarray as $automation) {
            if ($automation['rule_name'] == $automationkey) {
                $returnval = $automation[ $fieldname ];
                break;
            }
        }
        return $returnval;
    }
}
