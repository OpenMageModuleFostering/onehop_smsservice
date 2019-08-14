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
 * Controller
 *
 * @category    Onehop
 * @package     Onehop_SMSService
 * @author      Screen-Magic Mobile Media Inc. (info@onehop.co)
 */

class Onehop_SMSService_AdminhtmlController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Display form for sending a message.
     *
     * If API Key is not configured properly then user
     * will be redirect to configuration page.
     */
    public function indexAction()
    {
        if ($error = $this->_getService()->isvalidAPIKey()) {
            $this->_getSession()->addError($error);
            $this->_redirect('adminhtml/system_config/edit/section/smsservice');
            return;
        }
            $templateid = $this->getRequest()->getParam('templateid');
            
        if ($templateid != '') {
            $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
            $tablename  = Mage::getSingleton('core/resource')->getTableName('onehop_smstemplates');
            $selectBody = $connection->select()
                                ->from(array('template' => $tablename), array('template.temp_body'));
            $prepareQuery = $connection->query($selectBody);
            $gettemplate = $prepareQuery->fetch();
            return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($gettemplate));
        }
           
        $this->loadLayout();
        $this->renderLayout();
    }
   
    /**
     * Display list of created templates.
     *
     * If API Key is not configured properly then user
     * will be redirect to configuration page.
     */
    public function templateAction()
    {
        if ($error = $this->_getService()->isvalidAPIKey()) {
            $this->_getSession()->addError($error);
            $this->_redirect('adminhtml/system_config/edit/section/smsservice');
            return;
        }

        $this->loadLayout();
        $this->renderLayout();
    }
    /**
     * Display Welcome page layout.
     *
     * Add necessasry css and js
     * Show the get started information of onehop on Magento
     */
    public function welcomeAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    /**
     * Display SMS Ruleset settings layout.
     *
     * Retrieve required data from database
     * 
     * User can manage ruleset settings from this page
     * If API Key is not configured properly then user
     * will be redirect to configuration page.
     */
    public function automationAction()
    {
        if ($error = $this->_getService()->isvalidAPIKey()) {
            $this->_getSession()->addError($error);
            $this->_redirect('adminhtml/system_config/edit/section/smsservice');
            return;
        }
        $this->loadLayout();
        $this->renderLayout();
    }
    /**
     * Display form for Add Template .
     *
     * If API Key is not configured properly then user
     * will be redirect to configuration page.
     */
    public function addtemplateAction()
    {
        if ($error = $this->_getService()->isvalidAPIKey()) {
            $this->_getSession()->addError($error);
            $this->_redirect('adminhtml/system_config/edit/section/smsservice');
            return;
        }
        $this->loadLayout();
        $this->renderLayout();
    }
    /**
     * Save template data
     *
     * Fetch post data of add template and save to database
     *
     * Will redirect on manage template page after save template
     */
    public function savetemplatesAction()
    {
        $dbwrite = Mage::getSingleton("core/resource")->getConnection("core_write");
        $templatename = $this->getRequest()->getPost('templatename');
        $templatebody = $this->getRequest()->getPost('templatebody');
        $tablename  = Mage::getSingleton('core/resource')->getTableName('onehop_smstemplates');
        if ($templatename && $templatebody) {
            $dbwrite->insert($tablename, array("temp_name" => $templatename, "temp_body" => $templatebody, "submitdate" =>  'NOW()'));
        }
        $this->_redirect('smsservice/adminhtml/template');
    }
    /**
     * Display form for edit template
     *
     * Retrive data from database by template id
     *
     * If API Key is not configured properly then user
     * will be redirect to configuration page.
     */
    public function edittemplateAction()
    {
        if ($error = $this->_getService()->isvalidAPIKey()) {
            $this->_getSession()->addError($error);
            $this->_redirect('adminhtml/system_config/edit/section/smsservice');
            return;
        }
        $this->loadLayout();
        $this->renderLayout();
    }
    /**
     * Update template data
     *
     * fetch post data and update to database
     *
     * Will redirect on manage template page after update template
     */
    public function updatetemplateAction()
    {
        $dbwrite = Mage::getSingleton("core/resource")->getConnection("core_write");
        $templateid = $this->getRequest()->getPost('templateid');
        $templatename = $this->getRequest()->getPost('templatename');
        $templatebody = $this->getRequest()->getPost('templatebody');
        $tablename  = Mage::getSingleton('core/resource')->getTableName('onehop_smstemplates');

        if ($templateid && $templatename && $templatebody) {
            $Updatequery = "UPDATE " . $tablename . " SET temp_name = :templatename, temp_body = :templatebody WHERE smstemplates_id = '" . $templateid . "'";
            $bindData = array(
                        'templatename' => $templatename,
                        'templatebody' => $templatebody
                        );
            $dbwrite->query($Updatequery, $bindData);
        }
        $this->_redirect('smsservice/adminhtml/template');
    }
    /**
     * AJAX callback function for deleting template on template list page.
     */
    public function ajaxdeleteAction()
    {
        $templateid = $this->getRequest()->getParam('smstemplateid');
        $Templatetablename  = Mage::getSingleton('core/resource')->getTableName('onehop_smstemplates');
        $rulesettablename  = Mage::getSingleton('core/resource')->getTableName('sms_onehop_rulesets');
        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
        $dbwrite = Mage::getSingleton("core/resource")->getConnection("core_write");
        
        $selectrule = $connection->select()
                                ->from(array('ruleset' => $rulesettablename), array('ruleset.ruleid'))
                                ->where('template = ' . $templateid);
        $isruleset = $connection->query($selectrule);
        $getruleset = $isruleset->fetch();
        
        if ($getruleset) {
            $deleArr = array('allready_assigned'=>1);
        } else {
            $dbwrite->delete($Templatetablename, "smstemplates_id = '" . $templateid . "'");
            $deleArr = array('deleted'=>1);
        }
        return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($deleArr));
    }
    /**
     * Add and update ruleset data
     *
     * Manage validation for all SMS rulesets
     *
     * fetch post data and update to database     
     */
    public function saveautomationAction()
    {
        $helper  = Mage::helper('smsservice');
        $key = array('orderConfirm','shipmentConfirm','onDelivery','outStock','orderClose');
        $orderbtn = $this->getRequest()->getPost('btnorderconfirm');
        $shipmentbtn = $this->getRequest()->getPost('btnshipmentconfirm');
        $ondeliverybtn = $this->getRequest()->getPost('btnondelivery');
        $outstockbtn = $this->getRequest()->getPost('btnoutstock');
        $orderclosebtn = $this->getRequest()->getPost('btnorderclose');
        $postdata = $this->getRequest()->getPost();
        $errormessage = '';
        $success = false;
        
        if ($orderbtn) {
            $success = $this->saveOrderData($postdata, $key[0], $errormessage);
        } else if ($shipmentbtn) {
            $success = $this->saveShipmentData($postdata, $key[1], $errormessage);
        } else if ($ondeliverybtn) {
            $success = $this->saveDeliveryData($postdata, $key[2], $errormessage);
        } else if ($outstockbtn) {
            $success = $this->saveOutStockData($postdata, $key[3], $errormessage);
        } else if ($orderclosebtn) {
            $success = $this->saveOrderCloseData($postdata, $key[4], $errormessage);
        } 
        if ($success) {
            $this->_getSession()
            ->addSuccess($helper->__("Rule set saved successfully."));
        } else {
            $this->_getSession()->addError($helper->__($errormessage));           
        }
        $this->_redirectReferer();
    }

    /**
    * save order ruleset
    *
    * @param array $orderdata
    * @param string $key
    * @param string $errormessage
    *
    * @return bool
    */
    public function saveOrderData($orderdata, $key, &$errormessage)
    {
        $orderfeature = $orderdata['orderactivateFeature'];
        $ordertemp = $orderdata['ordersmstemplate'];
        $orderlabel = $orderdata['ordersmslabel'];
        $ordersenderid = $orderdata['ordersenderid'];
        if (!$ordertemp) {
            $errormessage = 'Please select template for order confirmation.';
            return;
        } elseif (!$orderlabel) {
            $errormessage = 'Label is required for order confirmation.';
            return;
        } elseif (!$ordersenderid) {
            $errormessage = 'Sender id is required for order confirmation.';
            return;
        }

        if ($ordertemp && $orderlabel && $ordersenderid) {
            $orderfeature == '1' ? '1' : '0';
            $orderdataArr['active'] = $orderfeature;
            $orderdataArr['rule_name'] = $key;
            $orderdataArr['template'] = $ordertemp;
            $orderdataArr['label'] = $orderlabel;
            $orderdataArr['senderid'] = $ordersenderid;

            $isorderConfirm = $this->smsautomationRuleset($orderdataArr);
            if ($isorderConfirm) {
                return true;
            }
        }
    }

    /**
    * save shipment ruleset
    *
    * @param array $shipmentdata
    * @param string $key
    * @param string $errormessage
    *
    * @return bool
    */
    public function saveShipmentData($shipmentdata, $key, &$errormessage)
    {
        $shipfeature = $shipmentdata['shipactivateFeature'];
        $shiptemp = $shipmentdata['shipsmstemplate'];
        $shiplabel = $shipmentdata['shipsmslabel'];
        $shipsenderid = $shipmentdata['shipsenderid'];
        if (!$shiptemp) {
            $errormessage = 'Please select template for shipment confirmation.';
            return;
        } elseif (!$shiplabel) {
            $errormessage = 'Label is required for shipment confirmation.';
            return;
        } elseif (!$shipsenderid) {
            $errormessage = 'Sender id is required for shipment confirmation.';
            return;
        }

        if ($shiptemp && $shiplabel && $shipsenderid) {
            $shipfeature == '1' ? '1' : '0';
            $shipdataArr['active'] = $shipfeature;
            $shipdataArr['rule_name'] = $key;
            $shipdataArr['template'] = $shiptemp;
            $shipdataArr['label'] = $shiplabel;
            $shipdataArr['senderid'] = $shipsenderid;

            $isshipConfirm = $this->smsautomationRuleset($shipdataArr);
            if ($isshipConfirm) {
                return true;
            }
        }
    }

    /**
    * save ondelivery ruleset
    *
    * @param array $deliverydata
    * @param string $key
    * @param string $errormessage
    *
    * @return bool
    */
    public function saveDeliveryData($deliverydata, $key, &$errormessage)
    {
        $ondeliveryfeature = $deliverydata['deliveryactivateFeature'];
        $ondeliverytemp = $deliverydata['deliverysmstemplate'];
        $ondeliverylabel = $deliverydata['deliverysmslabel'];
        $ondeliverysenderid = $deliverydata['deliverysenderid'];
        if (!$ondeliverytemp) {
            $errormessage = 'Please select template for On Delivery Followups.';
            return;
        } elseif (!$ondeliverylabel) {
            $errormessage = 'Label is required for On Delivery Followups.';
            return;
        } elseif (!$ondeliverysenderid) {
            $errormessage = 'Sender id is required for On Delivery Followups.';
            return;
        }

        if ($ondeliverytemp && $ondeliverylabel && $ondeliverysenderid) {
            $ondeliveryfeature == '1' ? '1' : '0';
            $ondeliverydataArr['active'] = $ondeliveryfeature;
            $ondeliverydataArr['rule_name'] = $key;
            $ondeliverydataArr['template'] = $ondeliverytemp;
            $ondeliverydataArr['label'] = $ondeliverylabel;
            $ondeliverydataArr['senderid'] = $ondeliverysenderid;

            $isonDelivery = $this->smsautomationRuleset($ondeliverydataArr);
            if ($isonDelivery) {
                return true;
            }
        }
    }

    /**
    * save out of stock ruleset
    *
    * @param array $outstockdata
    * @param string $key
    * @param string $errormessage
    *
    * @return bool
    */
    public function saveOutStockData($outstockdata, $key, &$errormessage)
    {
        $outstockfeature = $outstockdata['outactivateFeature'];
        $outstocktemp = $outstockdata['outsmstemplate'];
        $outstocklabel = $outstockdata['outsmslabel'];
        $outstocksenderid = $outstockdata['outsenderid'];
        $outstockadminmobile = $this->_getService()->getAdminMobile();
        if (!$outstocktemp) {
            $errormessage = 'Please select template for out of stock alerts.';
            return;
        } elseif (!$outstocklabel) {
            $errormessage = 'Label is required for out of stock alerts.';
            return;
        } elseif (!$outstocksenderid) {
            $errormessage = 'Sender id is required for out of stock alerts.';
            return;
        } elseif (!$outstockadminmobile) {
            $errormessage = 'Admin mobile is not set. Please check configuration';
            return;
        }

        if ($outstocktemp && $outstocklabel && $outstocksenderid && $outstockadminmobile) {
            $outstockfeature == '1' ? '1' : '0';
            $outstockdataArr['active'] = $outstockfeature;
            $outstockdataArr['rule_name'] = $key;
            $outstockdataArr['template'] = $outstocktemp;
            $outstockdataArr['label'] = $outstocklabel;
            $outstockdataArr['senderid'] = $outstocksenderid;
            $outstockdataArr['adminmobile'] = $outstockadminmobile;

            $isoutStock = $this->smsautomationRuleset($outstockdataArr);
            if ($isoutStock) {
                return true;
            }
        }
    }

    /**
    * save order close ruleset
    *
    * @param array $ordeclosekdata
    * @param string $key
    * @param string $errormessage
    *
    * @return bool
    */
    public function saveOrderCloseData($ordeclosekdata, $key, &$errormessage)
    {
        $closeorderfeature = $ordeclosekdata['closeactivateFeature'];
        $closeordertemp = $ordeclosekdata['closesmstemplate'];
        $closeorderlabel = $ordeclosekdata['closesmslabel'];
        $closeordersenderid = $ordeclosekdata['closesenderid'];
        if (!$closeordertemp) {
            $errormessage = 'Please select template for order close.';
            return;
        } elseif (!$closeorderlabel) {
            $errormessage = 'Label is required for order close.';
            return;
        } elseif (!$closeordersenderid) {
            $errormessage = 'Sender id is required for order close.';
            return;
        }

        if ($closeordertemp && $closeorderlabel && $closeordersenderid) {
            $closeorderfeature == '1' ? '1' : '0';
            $closeorderdataArr['active'] = $closeorderfeature;
            $closeorderdataArr['rule_name'] = $key;
            $closeorderdataArr['template'] = $closeordertemp;
            $closeorderdataArr['label'] = $closeorderlabel;
            $closeorderdataArr['senderid'] = $closeordersenderid;

            $isorderClose = $this->smsautomationRuleset($closeorderdataArr);
            if ($isorderClose) {
                return true;
            }
        }
    }

    /**
     * Send SMS action.
     *
     * If $text is not defined then action terminates with warning message.
     * If credentials (API key) is not configured properly action
     * terminates with warning message.
     *
     * If all is right then action tries to send SMS to customer number
     */
    public function saveAction()
    {
        $mobilenumber = $this->getRequest()->getParam('mobilenumber');
        $text       = $this->getRequest()->getParam('sms_text');
        $senderid   = $this->getRequest()->getParam('senderid');
        $smslabel   = $this->getRequest()->getParam('smslabel');
        $source = '23000';
        $helper  = Mage::helper('smsservice');
        $config  = $this->_getConfig();
        $storeId = Mage::app()->getStore()->getId();
        $service = $this->_getService();

        // check sms text, try to send message without text has no sense
        if (!$text) {
            $this->_getSession()->addError($helper->__('Message text is not defined.'));
            $this->_redirectReferer();
            return;
        }

        // check credentials
        if ($error = $service->isvalidAPIKey()) {
            $this->_getSession()->addError($error);
            $this->_redirectReferer();
            return;
        }

        // send SMS

        $sendSms = Mage::getModel('smsservice/sms');
        $sendSms->setLabel($smslabel);
        $sendSms->setSenderId($senderid);
        $sendSms->setSource($source);
        $sendSms->setNumber($mobilenumber);
        $sendSms->setText($text);

        if ($service->send($sendSms)) {
            $this->_getSession()->addSuccess($helper->__("SMS sent successfully."));
        } else {
            $this->_getSession()->addError($helper->__("Error comes to send SMS"));
        }

        $this->_redirectReferer();
    }

    /**
     * Add, update SMS ruleset data.
     *
     * @param array $ruledataArr
     * @return true value
     */
    protected function smsautomationRuleset($ruledataArr)
    {

        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
        $dbwrite = Mage::getSingleton("core/resource")->getConnection("core_write");

        $tablename  = Mage::getSingleton('core/resource')->getTableName('sms_onehop_rulesets');
        $selectRule = $connection->select()
                                ->from(array($tablename), array('ruleid'))
                                ->where('rule_name = "'.$ruledataArr['rule_name'].'"');
        $isRule = $connection->query($selectRule);
        $getRule = $isRule->fetch();        
        if ($getRule) {
            $dbwrite->update($tablename,
                        array(
                            "template" => $ruledataArr['template'],
                            "label" => $ruledataArr['label'],
                            "senderid" => $ruledataArr['senderid'],
                            "active" =>  $ruledataArr['active']
                        ), "rule_name = '" . $ruledataArr['rule_name'] . "'");
        } else {
            $dbwrite->insert($tablename,
                        array(
                            "rule_name" => $ruledataArr['rule_name'],
                            "template" => $ruledataArr['template'],
                            "label" => $ruledataArr['label'],
                            "senderid" => $ruledataArr['senderid'],
                            "active" =>  $ruledataArr['active']
                        ));
        }
        return true;
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
     * @return Onehop_SMSService_Helper_Model_Service
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
    
    /**
     * is allowed.
     *
     * @return bool
     */
    public function _isAllowed() {
        return true;
    }
}
