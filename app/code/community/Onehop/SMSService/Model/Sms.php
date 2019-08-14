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
 * SMS Model
 *
 * @category    Onehop
 * @package     Onehop_SMSService
 * @author      Screen-Magic Mobile Media Inc. (info@onehop.co)
 */

class Onehop_SMSService_Model_Sms extends Varien_Object
{
    /**
     * Store label.
     *
     * @var string
     */
    protected $_label = '';

    /**
     * Store senderid.
     *
     * @var string
     */
    protected $_senderid = 0;

    /**
     * Phone number where SMS will be sent.
     *
     * @var int
     */
    protected $_number = '';

    /**
     * Source source.
     *
     * @var integer
     */
    protected $_source = '';

    /**
     * ISO2 code of country.
     * This field is optional not all SMS has to filled.
     *
     * @var string
     */
    protected $_country = '';

    /**
     * Fullmeaning text of SMS, thus without any replacement {{ ... }}.
     *
     * @var string
     */
    protected $_text = '';

    /**
     * A custom data. Here can be stored a related customer or a related order
     * or whatever you want.
     *
     * @var Varien_Object
     */
    protected $_customData = null;

    /**
    * @param string $label
    * 
    * @return Onehop_SMSService_Model_Sms
    */
    public function setLabel($label)
    {
        $this->_label = $label;

        return $this;
    }

    /**
     * Get current type of SMS.
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->_label;
    }

    /**
     * Set number where SMS will be sent.
     *
     * Number should be max 16 chars length and should
     * contains only digits [0-9].
     *
     * @param string $number
     * @return Onehop_SMSService_Model_Sms
     */
    public function setNumber($number)
    {
        $this->_number = $number;

        return $this;
    }

    /**
     * Set number where SMS will be sent.
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->_number;
    }

    /**
     * Set source number.
     *
     * @param string $source
     * @return Onehop_SMSService_Model_Sms
     */
    public function setSource($source)
    {
        $this->_source = $source;

        return $this;
    }

    /**
     * Set source number.
     *
     * @return string
     */
    public function getSource()
    {
        return $this->_source;
    }

    /**
     * Set country where SMS will be sent.
     *
     * Country code should be ISO2
     *
     * @param string $country
     * @return Onehop_SMSService_Model_Sms
     */
    public function setCountry($country)
    {
        $this->_country = $country;

        return $this;
    }

    /**
     * Get country where SMS will be sent.
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->_country;
    }

    /**
     * Set text of SMS.
     *
     * @param string $text
     * @return Onehop_SMSService_Model_Sms
     */
    public function setText($text)
    {
        $this->_text = $text;

        return $this;
    }

    /**
     * Get text of SMS.
     *
     * @return string
     */
    public function getText()
    {
        return $this->_text;
    }

    /**
     * Set Sender id.
     *
     * @param string $senderId
     * @return Onehop_SMSService_Model_Sms
     */
    public function setSenderId($senderId)
    {
        $this->_senderid = $senderId;

        return $this;
    }

    /**
     * Get Sender id.
     *
     * @return string
     */
    public function getSenderId()
    {
        return $this->_senderid;
    }

    /**
     * Add $data to customData as $key.
     *
     * If there is a data as $key, then data will
     * be overwritten.
     *
     * Method returns $this for kepp the influence interface.
     *
     * @param string $key
     * @param mixed $data
     * @return Onehop_SMSService_Model_Sms
     */
    public function addCustomData($key, $data)
    {
        if (is_null($this->_customData)) {
            $this->_customData = new Varien_Object();
        }
        $this->_customData->setData($key, $data);

        return $this;
    }

    /**
     * Get custom data.
     *
     * @return Varien_Object|null
     */
    public function getCustomData($key = null)
    {
        if ($key) {
            $data = $this->_customData->getData($key);    
        } else {
            $data = $this->_customData->getData();    
        }
        return $data;
    }
}
