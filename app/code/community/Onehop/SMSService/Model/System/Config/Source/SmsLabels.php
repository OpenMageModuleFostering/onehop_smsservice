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
class Onehop_SMSService_Model_System_Config_Source_SmsLabels
{
    public static $labelUrl = 'http://api.onehop.co/v1/labels/';
    /**
     * get label list using lable API URL with valid API Key
     *
     * @return array
     */
    public function getLableslist()
    {
        $config = Mage::getSingleton('smsservice/config');

        $options = array();

        $apiKey = $this->getApikey();

        // No option
        $options[] = array(
            'value' => '',
            'label' => Mage::helper('smsservice')->__('Select Label'),
        );

        // Header
        $headers = array(
            'Accept: ',
            'apiKey:' . $apiKey,
        );

        // Send request
        $iClient = new Varien_Http_Client();
        $iClient->setUri(self::$labelUrl)->setMethod('GET')->setConfig(array(
            'maxredirects' => 0,
            'timeout' => 30,
        ));
        $iClient->setHeaders($headers);
        $response = $iClient->request();
        $output   = json_decode($response->getBody());

        $labelArr  = array();
        $labelInfo = array();
        if ($output && isset($output) && isset($output->labelsList) && $output->labelsList) {
            foreach ($output->labelsList as $labelVal) {
                $options[] = array(
                    'value' => $labelVal,
                    'label' => Mage::helper('smsservice')->__('%s', $labelVal),
                );
            }
        } elseif ($output->message) {
            $options[] = array(
                'value' => '',
                'label' => Mage::helper('smsservice')->__('Label not available'),
                'disabled' => 'disabled',
            );
        }

        return $options;
    }

    /**
     * save all values and label of list in $placeholders
     *
     * @return array
     */
    public function getAllPlaceholders()
    {

        $options      = array();
        $placeholders = array(
            'Customer Firstname',
            'Customer Lastname',
            'Order ID',
            'Product Name',
            'Transaction ID',
            'Invoice',
        );
        // No option
        $options[]    = array(
            'value' => '',
            'label' => Mage::helper('smsservice')->__('Select Label'),
        );
        foreach ($placeholders as $palceVal) {
            $options[] = array(
                'value' => '{{' . strtolower($palceVal) . '}}',
                'label' => Mage::helper('smsservice')->__('%s', $palceVal),
            );
        }

        return $options;
    }
    /**
     * retrive API Key from confuguration table
     *
     * @return string
     */
    public function getApikey()
    {
        return Mage::getStoreConfig('smsservice/general/sms_onehop_apiKey');
    }
}
