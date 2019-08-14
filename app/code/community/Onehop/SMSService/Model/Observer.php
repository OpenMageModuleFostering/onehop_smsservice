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
 * Observer Model
 *
 * @category    Onehop
 * @package     Onehop_SMSService
 * @author      Screen-Magic Mobile Media Inc. (info@onehop.co)
 */

class Onehop_SMSService_Model_Observer
{
    /**
     * Send SMS whenever order will place
     *
     * @param orderSuccess $observer
     *
     * @return null when Activate Feature is not active for order place
     */
    public function orderSuccess()
    {
        $service           = $this->_getService();
        $isOrderConfActive = $service->getRuleset('orderConfirm');
        if (! $isOrderConfActive) {
            return;
        }

        $order = Mage::getModel('sales/order')->load(Mage::getSingleton('checkout/session')->getLastOrderId());

        $smsbody = $service->replacePlaceholders($order, $isOrderConfActive['temp_body']);

        $address = $order->getShippingAddress();
        $mobile  = is_object($address) ? trim($address->getTelephone()) : '';

        $mobilenumber = $mobile;
        $text         = $smsbody;
        $senderid     = $isOrderConfActive['senderid'];
        $smslabel     = $isOrderConfActive['label'];
        $source       = '23000';

        $sendSms = Mage::getModel('smsservice/sms');
        $sendSms->setLabel($smslabel);
        $sendSms->setSenderId($senderid);
        $sendSms->setSource($source);
        $sendSms->setNumber($mobilenumber);
        $sendSms->setText($text);

        if ($service->send($sendSms)) {
            Mage::log('Order Confirmation : MOBILE : ' . $mobilenumber . ' SENDER ID : ' . $senderid . ' Label : ' . $smslabel . ' SOURCE : ' . $source . ' MESSAGE BODY : ' . $text, null, 'smsservice.log');
        }

        $orderedItems = $order->getAllVisibleItems();
        foreach ($orderedItems as $item) {
            $pid = $item->getData('product_id');
            $this->processOutofStock($pid);
        }
    }

    /**
     * Send SMS whenever order shipped or processing
     *
     * @param orderShipment $observer
     *
     * @return null when Activate Feature is not active for order shipment
     */
    public function orderShipment(Varien_Event_Observer $observer)
    {
        $service          = $this->_getService();
        $isShipConfActive = $service->getRuleset('shipmentConfirm');

        if (! $isShipConfActive) {
            return;
        }

        $shipment = $observer->getEvent()->getShipment();
        $order    = $shipment->getOrder();

        $smsbody = $service->replacePlaceholders($order, $isShipConfActive['temp_body']);

        $address = $order->getShippingAddress();
        $mobile  = is_object($address) ? trim($address->getTelephone()) : '';

        $mobilenumber = $mobile;
        $text         = $smsbody;
        $senderid     = $isShipConfActive['senderid'];
        $smslabel     = $isShipConfActive['label'];
        $source       = '23000';

        $sendSms = Mage::getModel('smsservice/sms');
        $sendSms->setLabel($smslabel);
        $sendSms->setSenderId($senderid);
        $sendSms->setSource($source);
        $sendSms->setNumber($mobilenumber);
        $sendSms->setText($text);

        if ($service->send($sendSms)) {
            Mage::log('Shipement Confirmation : MOBILE : ' . $mobilenumber . ' SENDER ID : ' . $senderid . ' Label : ' . $smslabel . ' SOURCE : ' . $source . ' MESSAGE BODY : ' . $text, null, 'smsservice.log');
        }
    }

    /**
     * Send SMS whenever order delivered
     *
     * @param orderComplete $observer
     *
     * @return null when Activate Feature is not active for order on delivery
     */
    public function orderComplete(Varien_Event_Observer $observer)
    {
        $service                = $this->_getService();
        $isOnDeliveryConfActive = $service->getRuleset('onDelivery');

        if (! $isOnDeliveryConfActive) {
            return;
        }

        $_event   = $observer->getEvent();
        $_invoice = $_event->getInvoice();
        $order    = $_invoice->getOrder();

        $smsbody = $service->replacePlaceholders($order, $isOnDeliveryConfActive['temp_body']);

        $address = $order->getShippingAddress();
        $mobile  = is_object($address) ? trim($address->getTelephone()) : '';

        $mobilenumber = $mobile;
        $text         = $smsbody;
        $senderid     = $isOnDeliveryConfActive['senderid'];
        $smslabel     = $isOnDeliveryConfActive['label'];
        $source       = '23000';

        $sendSms = Mage::getModel('smsservice/sms');
        $sendSms->setLabel($smslabel);
        $sendSms->setSenderId($senderid);
        $sendSms->setSource($source);
        $sendSms->setNumber($mobilenumber);
        $sendSms->setText($text);

        if ($service->send($sendSms)) {
            Mage::log('Delivery Confirmation : MOBILE : ' . $mobilenumber . ' SENDER ID : ' . $senderid . ' Label : ' . $smslabel . ' SOURCE : ' . $source . ' MESSAGE BODY : ' . $text, null, 'smsservice.log');
        }
    }

    /**
     * get items and call to saveProductCommitAfter function
     *
     * @param saveProduct $observer
     */
    public function saveProductCommitAfter(Varien_Event_Observer $observer)
    {
        $productId = $observer->getProduct()->getId();

        $product = Mage::getModel('catalog/product')->load($productId);

        if (! $product || empty($product)) {
            return;
        }

        $oldStock = (int) $product->getOrigData('stock_item')->getOrigData('is_in_stock');
        $newStock = (int) $product->getStockItem()->getData('is_in_stock');
        if ($newStock != $oldStock) {
            $this->processOutofStock($productId);
        }
    }

    /**
     * Send SMS to admin whenever product went out of stock
     *
     * @param processOutofStock $productId
     *
     * @return null when Activate feature is not active for out of stock
     * admin mobile is not set
     * available product quntity is greater than notify out of stock quantity
     */
    public function processOutofStock($productId)
    {
        $product = Mage::getModel('catalog/product')->load($productId);
        if (! $product || empty($product)) {
            return;
        }

        if ($product->getStockItem()->getManageStock() && ! $product->getIsInStock()) {
            $productName      = $product->getStockItem()->getProductName();
            $service          = $this->_getService();
            $isoutStockActive = $service->getRuleset('outStock');
            if (! $isoutStockActive) {
                return;
            }

            $mobile = $service->getAdminMobile();
            if (! $mobile) {
                return;
            }

            $smsbody = $isoutStockActive['temp_body'];
            $smsbody = str_replace('{Product ID}', $productId, $smsbody);
            $smsbody = str_replace('{Product Name}', $productName, $smsbody);

            $senderid = $isoutStockActive['senderid'];
            $smslabel = $isoutStockActive['label'];
            $source   = '23000';

            $sendSms = Mage::getModel('smsservice/sms');
            $sendSms->setLabel($smslabel);
            $sendSms->setSenderId($senderid);
            $sendSms->setSource($source);
            $sendSms->setNumber($mobile);
            $sendSms->setText($smsbody);

            if ($service->send($sendSms)) {
                Mage::log('Out Of Stock : ADMIN MOBILE : ' . $mobile . ' SENDER ID : ' . $senderid . ' Label : ' . $smslabel . ' SOURCE : ' . $source . ' MESSAGE BODY : ' . $smsbody, null, 'smsservice.log');
            }
        }
    }

    /**
     * Send SMS to admin whenever admin close or refund order
     *
     * @param orderRefund $observer
     *
     * @return null when Activate feature is not active for order close ruleset
     */
    public function orderRefund(Varien_Event_Observer $observer)
    {

        $service            = $this->_getService();
        $isOrderCloseActive = $service->getRuleset('orderClose');
        if (! $isOrderCloseActive) {
            return;
        }

        $creditmemo = $observer->getEvent()->getCreditmemo();
        $order      = $creditmemo->getOrder();

        $smsbody = $service->replacePlaceholders($order, $isOrderCloseActive['temp_body']);

        $address = $order->getShippingAddress();
        $mobile  = is_object($address) ? trim($address->getTelephone()) : '';

        $mobilenumber = $mobile;
        $text         = $smsbody;
        $senderid     = $isOrderCloseActive['senderid'];
        $smslabel     = $isOrderCloseActive['label'];
        $source       = '23000';

        $sendSms = Mage::getModel('smsservice/sms');
        $sendSms->setLabel($smslabel);
        $sendSms->setSenderId($senderid);
        $sendSms->setSource($source);
        $sendSms->setNumber($mobilenumber);
        $sendSms->setText($text);

        if ($service->send($sendSms)) {
            Mage::log('Order Close : MOBILE : ' . $mobilenumber . ' SENDER ID : ' . $senderid . ' Label : ' . $smslabel . ' SOURCE : ' . $source . ' MESSAGE BODY : ' . $text, null, 'smsservice.log');
        }
    }

    /**
     * Get standard configuration model.
     *
     * @return Onehop_SMSService_Helper_Model_Config
     */
    protected function _getConfig()
    {

        return Mage::getSingleton('smsservice/config');
    }

    /**
     * Get standard service.
     *
     * @return Onehop_SMSService_Model_Service
     */
    protected function _getService()
    {

        return Mage::getSingleton('smsservice/service');
    }

    /**
     * Get backend session.
     *
     * @return Mage_Adminhtml_Model_Session
     */
    protected function _getSession()
    {

        return Mage::getSingleton('adminhtml/session');
    }
}
