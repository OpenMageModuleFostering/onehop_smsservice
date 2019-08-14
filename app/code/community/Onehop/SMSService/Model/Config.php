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
 * Config Model
 *
 * @category    Onehop
 * @package     Onehop_SMSService
 * @author      Screen-Magic Mobile Media Inc. (info@onehop.co)
 */

class Onehop_SMSService_Model_Config
{
    /**
    * use curl call to check $apikey is valid or not
    * 
    * @param string $apikey
    * @return json
    */
    public function getValidAPIKey($apikey)
    {
        $urllink = "http://api.onehop.co/v1/api_key/validate/";
        // Header
        $headers = array(
            'Accept: ',
            'apiKey:' . $apikey
        );
        
        // Send request
        $iClient = new Varien_Http_Client();
             $iClient->setUri($urllink)
            ->setMethod('GET')
            ->setConfig(array(
                    'maxredirects'=>0,
                    'timeout'=>30,
            ));
        $iClient->setHeaders($headers);
        $response = $iClient->request();
        $output = json_decode($response->getBody());
        return $output;
    }

    /**
     * Determine whether unicode is allowed or not.
     *
     * @param int $storeId
     */
    public function isUnicodeAllowed($storeId = null)
    {
        return (bool) Mage::getStoreConfig("smsservice/general/unicode", $storeId);
    }

    /**
     * Sanitize number.
     *
     * Add dial prefix of local country if needed (if local country
     * is not specified there will be used country from general settings).
     *
     * Whitespaces in $number will be automaticaly removed.
     *
     * @param string $number
     * @return string
     */
    public function sanitizeNumber($number, $storeId = null)
    {
        $length   = Mage::getStoreConfig("smsservice/general/min_length_with_prefix", $storeId);
        $local    = Mage::getStoreConfig("smsservice/general/local_country", $storeId);
        $trimzero = Mage::getStoreConfig("smsservice/general/trim_zero", $storeId);

        $prefix = $this->getDialPrefix($local);

        $number = str_replace(array(" ", "\t"), array("", ""), $number);
        $number = ltrim($number, ($trimzero ? "+0" : "+"));

        if (strlen($number) <= $length)
        $number = $prefix.$number;

        return $number;
    }

    /**
     * Extract dial prefix from $localCode.
     *
     * $localCode has format CODE,DIAL_PREFIX.
     *
     * @param string $localCode
     * @return string
     */
    public function getDialPrefix($localCode)
    {
        $parts = explode(',', $localCode);

        return (count($parts)==2) ? trim($parts[1]) : '';
    }
}
