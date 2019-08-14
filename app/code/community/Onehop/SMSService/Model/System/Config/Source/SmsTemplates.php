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
class Onehop_SMSService_Model_System_Config_Source_SmsTemplates
{
    /**
     * Get templates list
     *
     * @return array
     */
    public function getTemplatesList()
    {
        $config  = Mage::getSingleton('smsservice/config');
        $options = array();

        $tablename  = Mage::getSingleton('core/resource')->getTableName('onehop_sms_templates');
        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');

        $selecttemp = $connection->select()->from(array(
            $tablename
            ), array(
            'smstemplates_id',
            'temp_name',
        ))->order('temp_name');
        $query      = $connection->query($selecttemp);
        // $template = $query->fetchAll();
        // No option
        $options[] = array(
            'value' => '0',
            'label' => Mage::helper('smsservice')->__('Select Template'),
        );
        while ($row = $query->fetch()) {
            $options[] = array(
                'value' => $row['smstemplates_id'],
                'label' => Mage::helper('smsservice')->__('%s', $row['temp_name']),
            );
        }

        return $options;
    }

    /**
     * get single template info
     *
     * @return array
     */
    public function gettemplateInfoByID($templateid)
    {
        $tablename  = Mage::getSingleton('core/resource')->getTableName('onehop_sms_templates');
        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
        $selecttemp = $connection->select()->from(array(
            $tablename
            ), array(
            '*'
        ))->where('smstemplates_id = ' . $templateid);
        $query      = $connection->query($selecttemp);
        $rows       = $query->fetch();
        return $rows;
    }
    /**
     * get template name or template body
     *
     * @param array  $templateInfo
     * @param string $fieldname
     *
     * @return string
     */
    public function getTemplateFeilds($templateInfo, $fieldname)
    {
        $returnVal = '';
        if ($fieldname == 'templatename') {
            $returnVal = $templateInfo['temp_name'];
        }
        if ($fieldname == 'templatebody') {
            $returnVal = $templateInfo['temp_body'];
        }
        return $returnVal;
    }
}
