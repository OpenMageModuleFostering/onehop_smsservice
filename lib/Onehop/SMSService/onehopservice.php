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
 * PHP other library
 *
 * @category    Onehop
 * @package     Onehop_SMSService
 * @author      Screen-Magic Mobile Media Inc. (info@onehop.co)
 */

/**
 * Allows simple access to ONEHOP SMS Services API
 */
class OnehopService
{
    /**
     * ONEHOP SMS Service API URL
     */
    public static $serviceUrl = 'http://api.onehop.co/v1/sms/send/';

    /**
     * to push error messange
     *
     * @var array
     */
    public $errors = array();

    /**
     *
     * @var array
     */

    public static $instance = null;

    /**
     * public constructor for singleton pattern
     */
    public function __construct()
    {
    }

    /**
     * Returns a singleton instance of OnehopService class
     */
    public static function getInstance()
    {

        if (is_null(self::$instance)) {
            self::$instance = new OnehopService();
        }

        return self::$instance;
    }

    /**
     * Adds error to the list of errors
     */
    protected function setError($error)
    {
        $this->errors[] = $error;
    }

    /**
     * Returns the last error message
     */
    public function getError()
    {

        return end($this->errors);
    }

    /**
     * Returns all the errors concatenated with the $newline string
     */
    public function getErrors($newline = "\n")
    {
        return implode($newline, $this->errors);
    }

    /**
     * Sends an SMS message from user's account
     *
     * @param string $username SMS Service API username
     * @param string $apiKey SMS Service API key
     * @param string $to Recipient phone number in international format, max 16 characters, no spaces and no leading plus or zeroes
     * @param string $text Text of the message have 700 characters limit for typing message. we are displayed left characters count underneath of message
     * message box. uesr not be able to write more than 700 characters.
     * @return bool True on success or false on error
     */
    public function sendMessage($apiKey, $to, $text, $label, $senderid, $source)
    {
        if (empty($apiKey) || empty($to) || empty($text) || empty($label) || empty($senderid)) {
            $this->setError('Some parameters are not set.');
            return false;
        }

        // Fix the phone number
        $to = ltrim($to, '+0');
        $to = str_replace(' ', '', $to);

        // Validate the phone number
        if ((strlen($to) > 16)) {
            $this->setError('Invalid phone number format.');
            return false;
        }
        // Header
        $headers = array(
            'Accept: ',
            'apiKey:' . $apiKey,
        );
        // Prepare request
        $data    = array(
            'label' => $label,
            'sms_text' => $text,
            'source' => $source,
            'sender_id' => $senderid,
            'mobile_number' => $to,
        );

        // Send request
        $iClient = new Varien_Http_Client();
        $iClient->setUri(self::$serviceUrl)->setMethod('POST')->setConfig(array(
            'maxredirects' => 0,
            'timeout' => 30,
        ));
        $iClient->setHeaders($headers);
        $iClient->setRawData(json_encode(array(
            'label' => $label,
            'sms_text' => $text,
            'source' => $source,
            'sender_id' => $senderid,
            'mobile_number' => $to,
        )), 'application/json;charset=UTF-8');
        $response = $iClient->request();

        $output = json_decode($response->getBody());

        if ($output->status != 'submitted' || ! $output) {
            $this->setError('Could not send the SMS.');
            return false;
        }
        // Message sent successfully
        return true;
    }
}
