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
 * Service Model
 *
 * @category    Onehop
 * @package     Onehop_SMSService
 * @author      Screen-Magic Mobile Media Inc. (info@onehop.co)
 */

/**
 * Include extern library
 */
include_once Mage::getBaseDir() . DS . 'lib' . DS . 'Onehop' . DS . 'SMSService' . DS . 'onehopservice.php';

class Onehop_SMSService_Model_Service
{
    /**
     * Send $smsdata.
     *
     * If $smsdata is not set or not instance of Onehop_SMSService_Model_SMS then
     * method logs WARNING and returns false.
     *
     * If there is not set username, apikey, or number is not set or is invalid
     * method generates the event 'smsservice_error' and returns false.
     *
     * If all is right method generates the event 'smsservice_before_sending'
     * and tries to send SMS.
     *
     * If SMS was sent method generates the event 'smsservice_after_sending'
     * and returns true, otherwise generates 'smsservice_error' and return false.
     *
     * @param Onehop_SMSService_Model_SMS $smsdata
     * @return bool
     */
    public function send($smsdata)
    {
        if (! $smsdata || ! ($smsdata instanceof Onehop_SMSService_Model_SMS)) {
            Mage::log(__CLASS__ . ':' . __METHOD__ . ': SMS is not set or is not instance
            of Onehop_SMSService_Model_SMS.', null, 'smsservice.log');
            return false;
        }

        $apikey = $this->getApikey();

        if (! $apikey) {
            $smsdata->addCustomData('error_message', $this->_helper()->__('API Key is not set.
            Check it in the configuration, please.'));
            Mage::dispatchEvent('smsservice_error', array(
                'sms' => $smsdata,
            ));
            return false;
        }

        if (! $smsdata->getNumber()) {
            $smsdata->addCustomData('error_message', $this->_helper()->__('Mobile Number is not set.'));
            Mage::dispatchEvent('smsservice_error', array(
                'sms' => $smsdata,
            ));
            return false;
        }

        if (! preg_match('/^[0-9]{1,16}$/', $smsdata->getNumber())) {
            $smsdata->addCustomData('error_message', $this->_helper()->__("Mobile Number '%s' is not valid.", $smsdata->getNumber()));
            Mage::dispatchEvent('smsservice_error', array(
                'sms' => $smsdata,
            ));
            return false;
        }

        Mage::dispatchEvent('smsservice_before_sending', array(
            'sms' => $smsdata,
        ));

        $service = OnehopService::getInstance();

        $result = $service->sendMessage($apikey, $smsdata->getNumber(), $smsdata->getText(), $smsdata->getLabel(), $smsdata->getSenderId(), $smsdata->getSource());

        if ($result) {
            $smsdata->addCustomData('error_message', null);
            Mage::dispatchEvent('smsservice_after_sending', array(
                'sms' => $smsdata,
            ));
            return true;
        } else {
            $smsdata->addCustomData('error_message', $service->getError());
            Mage::dispatchEvent('smsservice_error', array(
                'sms' => $smsdata,
            ));
            return false;
        }
    }

    /**
     * Determine whether API key is valid or not.
     *
     * @return string
     */
    public function isvalidAPIKey()
    {
        $apikey = $this->getApikey();
        if (! $apikey) {
            return $this->_helper()->__('API Key is not set.');
        } else {
            return '';
        }
    }

    /**
     * retrive ruleset data from database
     *
     * @param string $key
     * @return string
     */
    public function getRuleset($key)
    {
        $connection        = Mage::getSingleton('core/resource')->getConnection('core_read');
        $Rulesettablename  = Mage::getSingleton('core/resource')->getTableName('onehop_sms_rulesets');
        $Templatetablename = Mage::getSingleton('core/resource')->getTableName('onehop_sms_templates');
        $selectRule        = $connection->select()->from(array(
            'rule' => $Rulesettablename,
            ), array(
            'rule.*'
        ))->joinLeft(array(
            'temp' => $Templatetablename,
            ), ' temp.smstemplates_id = rule.template', array(
            'temp.temp_body'
        ))->where('rule.rule_name = "' . $key . '"')->where('rule.active = "1"');
        $isRule            = $connection->query($selectRule);
        $getrulesets       = $isRule->fetch();

        return $getrulesets;
    }

    /**
     * replace placeholders with param values
     *
     * @param array $order
     * @param array $smsbody
     * @return string
     */
    public function replacePlaceholders($order, $smsbody)
    {
        // Order Info
        $orderId     = $order->getId();
        $incrementId = $order->getIncrementId();

        $order = Mage::getModel('sales/order')->loadByIncrementId($incrementId);
        if (! $order || empty($order)) {
            return false;
        }

        // Customer Info
        $custFirstName = $order->getCustomerFirstname();
        $custLastName  = $order->getCustomerLastname();
        $email         = $order->getCustomerEmail();

        // Shipment info
        $shippingAddress = '';
        $mobile          = '';
        $address         = $order->getShippingAddress();
        if (is_object($address)) {
            $shipcustName    = $address->getName();
            $shipcustAddr    = $address->getStreetFull();
            $shipcustCity    = $address->getCity();
            $shipregion      = $address->getRegion();
            $shipcountry     = $address->getCountry();
            $shipPin         = $address->getPostcode();
            $mobile          = trim($address->getTelephone());
            $shippingAddress = $shipcustName . ', ' . $shipcustAddr . ' ' . $shipcustCity . ', ' . $shipregion . ', ' . $shipcountry . ' ' . $shipPin;
        }
        // Product Info
        $orderedItems = $order->getAllVisibleItems();
        $idarray      = array();
        $allids       = '';
        $namearray    = array();
        $allnames     = '';

        $productDisc = 0;
        foreach ($orderedItems as $item) {
            $pid         = $item->getData('product_id');
            $idarray[]   = $pid;
            $namearray[] = $item->getData('name');
            $productDisc += $this->getProductDiscount($pid, $item);
        }

        $orderDiscount = abs($order->getDiscountAmount());
        if ($productDisc > 0) {
            $orderDiscount += $productDisc;
        }

        $currencyCode = '';
        $currency     = $order->getOrderCurrency(); // $order object
        if (is_object($currency)) {
            $currencyCode = $currency->getCurrencyCode();
        }

        $orderPrice    = $currencyCode . ' ' . (float) $order->getGrandTotal();
        $orderDiscount = $currencyCode . ' ' . $orderDiscount;

        $allids   = implode(',', $idarray);
        $allnames = implode(',', $namearray);

        $trackNumber    = array();
        $allTrackNumber = '';
        foreach ($order->getTracksCollection() as $track) {
            $trackNumber[] = $track->getNumber();
        }
        $allTrackNumber = implode(',', $trackNumber);

        $allInvoiceNumbers = '';
        if ($order->hasInvoices()) {
            foreach ($order->getInvoiceCollection() as $invoice) {
                $invoiceIncId[] = $invoice->getIncrementId();
            }
            $allInvoiceNumbers = implode(',', $invoiceIncId);
        }

        $paymentTransactionId = '';
        if (is_object($order->getPayment())) {
            $paymentTransactionId = $order->getPayment()->getLastTransId();
        }

        $smsbody = str_replace('{Firstname}', $custFirstName, $smsbody);
        $smsbody = str_replace('{Lastname}', $custLastName, $smsbody);
        $smsbody = str_replace('{Email}', $email, $smsbody);
        $smsbody = str_replace('{Mobile}', $mobile, $smsbody);
        $smsbody = str_replace('{Order ID}', $incrementId, $smsbody);
        $smsbody = str_replace('{Transaction ID}', $paymentTransactionId, $smsbody);
        $smsbody = str_replace('{Tracking ID}', $allTrackNumber, $smsbody);
        $smsbody = str_replace('{Invoice}', $allInvoiceNumbers, $smsbody);
        $smsbody = str_replace('{Price}', $orderPrice, $smsbody);
        $smsbody = str_replace('{Discount}', $orderDiscount, $smsbody);
        $smsbody = str_replace('{Shipping_Address}', $shippingAddress, $smsbody);
        $smsbody = str_replace('{Product ID}', $allids, $smsbody);
        $smsbody = str_replace('{Product Name}', $allnames, $smsbody);

        return $smsbody;
    }

    /**
     * calculate product discount
     *
     * @param int $pid
     * @param int $quantity
     *
     * @return float
     */
    public function getProductDiscount($pid, $item)
    {
        $productDisc = 0;

        $product      = Mage::getModel('catalog/product')->load($pid);
        $regularPrice = (float) $product->getPrice();
        $quantity     = (int) $item->getData('qty_ordered');

        if ($regularPrice > 0 && $quantity > 0) {
            $salePrice = (float) $item->getData('price');
            if ($salePrice > 0) {
                $productDisc = ($regularPrice - $salePrice) * $quantity;
            }
        }
        return $productDisc;
    }

    /**
     * Get templates list
     *
     * @return array
     */
    public function getTemplatesList()
    {

        $tablename  = Mage::getSingleton('core/resource')->getTableName('onehop_sms_templates');
        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');

        $selecttemp = $connection->select()->from(array(
            $tablename
            ), array(
            'smstemplates_id',
            'temp_name',
        ))->order('smstemplates_id');
        $query      = $connection->query($selecttemp);

        $rows = array();
        while ($row = $query->fetch()) {
            array_push($rows, $row);
        }

        return $rows;
    }

    /**
     * Get template body
     *
     * @param int $templateid
     *
     * @return array
     */
    public function getTemplateBody($templateid)
    {
        $connection   = Mage::getSingleton('core/resource')->getConnection('core_read');
        $tablename    = Mage::getSingleton('core/resource')->getTableName('onehop_sms_templates');
        $selectBody   = $connection->select()->from(array(
            'template' => $tablename,
            ), array(
            'template.temp_body'
        ))->where('template.smstemplates_id = ' . $templateid);
        $prepareQuery = $connection->query($selectBody);
        $gettemplate  = $prepareQuery->fetch();
        return $gettemplate;
    }

    /**
     * Save template data
     *
     * @param string $templatename
     * @param string $templatebody
     */
    public function savetemplatesAction($templatename, $templatebody)
    {
        $dbwrite      = Mage::getSingleton('core/resource')->getConnection('core_write');
        $tablename    = Mage::getSingleton('core/resource')->getTableName('onehop_sms_templates');
        if ($templatename && $templatebody) {
            $dbwrite->insert($tablename, array(
                'temp_name' => $templatename,
                'temp_body' => $templatebody,
                'submitdate' => 'NOW()',
            ));
        }
    }

    /**
     * Update template data
     *
     * @param int    $templateid
     * @param string $templatename
     * @param string $templatebody
     */
    public function updatetemplateAction($templateid, $templatename, $templatebody)
    {
        $dbwrite      = Mage::getSingleton('core/resource')->getConnection('core_write');
        $tablename    = Mage::getSingleton('core/resource')->getTableName('onehop_sms_templates');

        if ($templateid && $templatename && $templatebody) {
            $Updatequery = 'UPDATE ' . $tablename . " SET temp_name = :templatename, temp_body = :templatebody WHERE smstemplates_id = '" . $templateid . "'";
            $bindData    = array(
                'templatename' => $templatename,
                'templatebody' => $templatebody,
            );
            $dbwrite->query($Updatequery, $bindData);
        }
    }

    /**
     * Delete template data
     *
     * @param int $templateid
     *
     * @return array
     */
    public function deletetemplateAction($templateid)
    {
        $Templatetablename = Mage::getSingleton('core/resource')->getTableName('onehop_sms_templates');
        $rulesettablename  = Mage::getSingleton('core/resource')->getTableName('onehop_sms_rulesets');
        $connection        = Mage::getSingleton('core/resource')->getConnection('core_read');
        $dbwrite           = Mage::getSingleton('core/resource')->getConnection('core_write');

        $selectrule = $connection->select()->from(array(
            'ruleset' => $rulesettablename,
            ), array(
            'ruleset.ruleid'
        ))->where('template = ' . $templateid);
        $isruleset  = $connection->query($selectrule);
        $getruleset = $isruleset->fetch();

        if ($getruleset) {
            $deleArr = array(
                'allready_assigned' => 1,
            );
        } else {
            $dbwrite->delete($Templatetablename, "smstemplates_id = '" . $templateid . "'");
            $deleArr = array(
                'deleted' => 1,
            );
        }
        return $deleArr;
    }

    /**
     * Add, update SMS ruleset data.
     *
     * @param array $ruledataArr
     * @return true value
     */
    public function smsautomationRuleset($ruledataArr)
    {

        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
        $dbwrite    = Mage::getSingleton('core/resource')->getConnection('core_write');

        $tablename  = Mage::getSingleton('core/resource')->getTableName('onehop_sms_rulesets');
        $selectRule = $connection->select()->from(array(
            $tablename
            ), array(
            'ruleid'
        ))->where('rule_name = "' . $ruledataArr['rule_name'] . '"');
        $isRule     = $connection->query($selectRule);
        $getRule    = $isRule->fetch();
        if ($getRule) {
            $dbwrite->update($tablename, array(
                'template' => $ruledataArr['template'],
                'label' => $ruledataArr['label'],
                'senderid' => $ruledataArr['senderid'],
                'active' => $ruledataArr['active'],
            ), "rule_name = '" . $ruledataArr['rule_name'] . "'");
        } else {
            $dbwrite->insert($tablename, array(
                'rule_name' => $ruledataArr['rule_name'],
                'template' => $ruledataArr['template'],
                'label' => $ruledataArr['label'],
                'senderid' => $ruledataArr['senderid'],
                'active' => $ruledataArr['active'],
            ));
        }
        return true;
    }

    /**
     * @return string
     */
    public function getUsername()
    {

        return Mage::getStoreConfig('smsservice/credentials/username');
    }

    /**
     * @return string
     */
    public function getApikey()
    {

        return Mage::getStoreConfig('smsservice/general/sms_onehop_apiKey');
    }
    /**
     * @return string
     */
    public function getAdminMobile()
    {

        return Mage::getStoreConfig('smsservice/mobile/sms_onehop_admin_mobile');
    }

    /**
     * Get standard config.
     *
     * @return Onehop_SMSService_Model_Config
     */
    protected function _getConfig()
    {

        return Mage::getSingleton('smsservice/config');
    }

    /**
     * Get standard helper.
     *
     * @return Onehop_SMSService_Helper_Data
     */
    protected function _helper()
    {

        return Mage::helper('smsservice');
    }
}
