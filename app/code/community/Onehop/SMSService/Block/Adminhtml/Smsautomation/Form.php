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
 * Preapare SMS automation form 
 *
 * @category    Onehop
 * @package     Onehop_SMSService
 * @author      Screen-Magic Mobile Media Inc. (info@onehop.co)
 */

class Onehop_SMSService_Block_Adminhtml_Smsautomation_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare form.
     */
    protected function _prepareForm()
    {
        $smsautomation = Mage::getModel('smsservice/system_config_source_smsautomation')->getDBsmsAutomation();
        $key           = array(
            'orderConfirm',
            'shipmentConfirm',
            'onDelivery',
            'outStock',
            'orderClose'
        );
        $form          = new Varien_Data_Form(array(
            'name' => 'send_form',
            'id' => 'edit_form',
            'action' => $this->getUrl('smsservice/adminhtml/saveautomation'),
            'method' => 'post'
        ));
        // SMS Automation for order confirmation
        $fieldset      = $form->addFieldset('sendsms_fieldset', array(
            'legend' => Mage::helper('smsservice')->__('ORDER CONFIRMATION'),
            'class' => 'fieldset'
        ));
        $fieldset->addType('customtype', 'Onehop_SMSService_Block_Adminhtml_Data_Form_Element_Orderconfirm');
        $fieldset->addField('orderConfirmdescr', 'customtype', array(
            'name' => 'orderConfirmdescr'
        ));
        $fieldset->addField('orderactivateFeature', 'checkbox', array(
            'name' => 'orderactivateFeature',
            'value' => '1',
            'checked' => Mage::getModel('smsservice/system_config_source_smsautomation')->getValue($smsautomation, $key[0], 'active') == '1' ? 'checked' : '',
            'label' => Mage::helper('smsservice')->__('Activate Feature'),
            'title' => Mage::helper('smsservice')->__('Activate Feature')
        ));
        $fieldset->addField('ordersmstemplate', 'selectonehop', array(
            'name' => 'ordersmstemplate',
            'label' => Mage::helper('smsservice')->__('Select Template'),
            'options' => Mage::getModel('smsservice/system_config_source_smsTemplates')->getTemplatesList(),
            'value' => Mage::getModel('smsservice/system_config_source_smsautomation')->getValue($smsautomation, $key[0], 'template')
        ));
        $fieldset->addField('ordersmslabel', 'selectonehop', array(
            'name' => 'ordersmslabel',
            'label' => Mage::helper('smsservice')->__('Select Label'),
            
            'options' => Mage::getModel('smsservice/system_config_source_smsLabels')->getLableslist(),
            'value' => Mage::getModel('smsservice/system_config_source_smsautomation')->getValue($smsautomation, $key[0], 'label')
        ));
        $fieldset->addField('ordersenderid', 'text', array(
            'name' => 'ordersenderid',
            'label' => Mage::helper('smsservice')->__('Sender Id'),
            'title' => Mage::helper('smsservice')->__('Sender Id'),
            'value' => Mage::getModel('smsservice/system_config_source_smsautomation')->getValue($smsautomation, $key[0], 'senderid')
        ));
        $fieldset->addField('btnorderconfirm', 'submit', array(
            'name' => 'btnorderconfirm',
            'value' => 'Save',
            'class' => 'save'
        ));
        // SMS Automation for Shipment confirmation
        $fieldset2 = $form->addFieldset('shipment_fieldset', array(
            'legend' => Mage::helper('smsservice')->__('SHIPMENT CONFIRMATION'),
            'class' => 'fieldset-shipment'
        ));
        $fieldset2->addType('customtype', 'Onehop_SMSService_Block_Adminhtml_Data_Form_Element_Shipmentconfirm');
        $fieldset2->addField('shipmentConfirmdescr', 'customtype', array(
            'name' => 'shipmentConfirmdescr'
        ));
        $fieldset2->addField('shipactivateFeature', 'checkbox', array(
            'name' => 'shipactivateFeature',
            'value' => '1',
            'checked' => Mage::getModel('smsservice/system_config_source_smsautomation')->getValue($smsautomation, $key[1], 'active') == '1' ? 'checked' : '',
            'label' => Mage::helper('smsservice')->__('Activate Feature'),
            'title' => Mage::helper('smsservice')->__('Activate Feature')
        ));
        $fieldset2->addField('shipsmstemplate', 'selectonehop', array(
            'name' => 'shipsmstemplate',
            'label' => Mage::helper('smsservice')->__('Select Template'),
            'options' => Mage::getModel('smsservice/system_config_source_smsTemplates')->getTemplatesList(),
            'value' => Mage::getModel('smsservice/system_config_source_smsautomation')->getValue($smsautomation, $key[1], 'template')
        ));
        $fieldset2->addField('shipsmslabel', 'selectonehop', array(
            'name' => 'shipsmslabel',
            'label' => Mage::helper('smsservice')->__('Select Label'),
            'options' => Mage::getModel('smsservice/system_config_source_smsLabels')->getLableslist(),
            'value' => Mage::getModel('smsservice/system_config_source_smsautomation')->getValue($smsautomation, $key[1], 'label')
        ));
        $fieldset2->addField('shipsenderid', 'text', array(
            'name' => 'shipsenderid',
            'label' => Mage::helper('smsservice')->__('Sender Id'),
            'title' => Mage::helper('smsservice')->__('Sender Id'),
            'value' => Mage::getModel('smsservice/system_config_source_smsautomation')->getValue($smsautomation, $key[1], 'senderid')
        ));
        $fieldset2->addField('btnshipmentconfirm', 'submit', array(
            'name' => 'btnshipmentconfirm',
            'value' => 'Save',
            'class' => 'save'
        ));
        // SMS Automation for Delivery confirmation
        $fieldset3 = $form->addFieldset('delivery_fieldset', array(
            'legend' => Mage::helper('smsservice')->__('ORDER COMPLETION'),
            'class' => 'fieldset-delivery'
        ));
        $fieldset3->addType('customtype', 'Onehop_SMSService_Block_Adminhtml_Data_Form_Element_Ondelivery');
        $fieldset3->addField('onDeliverydescr', 'customtype', array(
            'name' => 'onDeliverydescr'
        ));
        $fieldset3->addField('deliveryactivateFeature', 'checkbox', array(
            'name' => 'deliveryactivateFeature',
            'value' => '1',
            'checked' => Mage::getModel('smsservice/system_config_source_smsautomation')->getValue($smsautomation, $key[2], 'active') == '1' ? 'checked' : '',
            'label' => Mage::helper('smsservice')->__('Activate Feature'),
            'title' => Mage::helper('smsservice')->__('Activate Feature')
        ));
        $fieldset3->addField('deliverysmstemplate', 'selectonehop', array(
            'name' => 'deliverysmstemplate',
            'label' => Mage::helper('smsservice')->__('Select Template'),
            'options' => Mage::getModel('smsservice/system_config_source_smsTemplates')->getTemplatesList(),
            'value' => Mage::getModel('smsservice/system_config_source_smsautomation')->getValue($smsautomation, $key[2], 'template')
        ));
        $fieldset3->addField('deliverysmslabel', 'selectonehop', array(
            'name' => 'deliverysmslabel',
            'label' => Mage::helper('smsservice')->__('Select Label'),
            'options' => Mage::getModel('smsservice/system_config_source_smsLabels')->getLableslist(),
            'value' => Mage::getModel('smsservice/system_config_source_smsautomation')->getValue($smsautomation, $key[2], 'label')
        ));
        $fieldset3->addField('deliverysenderid', 'text', array(
            'name' => 'deliverysenderid',
            'label' => Mage::helper('smsservice')->__('Sender Id'),
            'title' => Mage::helper('smsservice')->__('Sender Id'),
            'value' => Mage::getModel('smsservice/system_config_source_smsautomation')->getValue($smsautomation, $key[2], 'senderid')
        ));
        $fieldset3->addField('btnondelivery', 'submit', array(
            'name' => 'btnondelivery',
            'value' => 'Save'
        ));
        // SMS Automation for Order close
        $fieldset5 = $form->addFieldset('close_fieldset', array(
            'legend' => Mage::helper('smsservice')->__('REFUND PROCESSED ALERT'),
            'class' => 'fieldset-close'
        ));
        $fieldset5->addType('customtype', 'Onehop_SMSService_Block_Adminhtml_Data_Form_Element_Orderclose');
        $fieldset5->addField('closeactivatedescr', 'customtype', array(
            'name' => 'closeactivatedescr'
        ));
        $fieldset5->addField('closeactivateFeature', 'checkbox', array(
            'name' => 'closeactivateFeature',
            'value' => '1',
            'checked' => Mage::getModel('smsservice/system_config_source_smsautomation')->getValue($smsautomation, $key[4], 'active') == '1' ? 'checked' : '',
            'label' => Mage::helper('smsservice')->__('Activate Feature'),
            'title' => Mage::helper('smsservice')->__('Activate Feature')
        ));
        $fieldset5->addField('backsmstemplate', 'selectonehop', array(
            'name' => 'closesmstemplate',
            'label' => Mage::helper('smsservice')->__('Select Template'),
            'options' => Mage::getModel('smsservice/system_config_source_smsTemplates')->getTemplatesList(),
            'value' => Mage::getModel('smsservice/system_config_source_smsautomation')->getValue($smsautomation, $key[4], 'template')
        ));
        $fieldset5->addField('backsmslabel', 'selectonehop', array(
            'name' => 'closesmslabel',
            'label' => Mage::helper('smsservice')->__('Select Label'),
            'options' => Mage::getModel('smsservice/system_config_source_smsLabels')->getLableslist(),
            'value' => Mage::getModel('smsservice/system_config_source_smsautomation')->getValue($smsautomation, $key[4], 'label')
        ));
        $fieldset5->addField('backsenderid', 'text', array(
            'name' => 'closesenderid',
            'label' => Mage::helper('smsservice')->__('Sender Id'),
            'title' => Mage::helper('smsservice')->__('Sender Id'),
            'value' => Mage::getModel('smsservice/system_config_source_smsautomation')->getValue($smsautomation, $key[4], 'senderid')
        ));
        $fieldset5->addField('btnorderclose', 'submit', array(
            'name' => 'btnorderclose',
            'value' => 'Save'
        ));
        // SMS Automation for Out of stock
        $fieldset4 = $form->addFieldset('out_fieldset', array(
            'legend' => Mage::helper('smsservice')->__('OUT OF STOCK ALERT'),
            'class' => 'fieldset-out'
        ));
        $fieldset4->addField('outactivateFeature', 'checkbox', array(
            'name' => 'outactivateFeature',
            'value' => '1',
            'checked' => Mage::getModel('smsservice/system_config_source_smsautomation')->getValue($smsautomation, $key[3], 'active') == '1' ? 'checked' : '',
            'label' => Mage::helper('smsservice')->__('Activate Feature'),
            'title' => Mage::helper('smsservice')->__('Activate Feature')
        ));
        $fieldset4->addField('outsmstemplate', 'selectonehop', array(
            'name' => 'outsmstemplate',
            'label' => Mage::helper('smsservice')->__('Select Template'),
            'options' => Mage::getModel('smsservice/system_config_source_smsTemplates')->getTemplatesList(),
            'value' => Mage::getModel('smsservice/system_config_source_smsautomation')->getValue($smsautomation, $key[3], 'template')
        ));
        $fieldset4->addField('outsmslabel', 'selectonehop', array(
            'name' => 'outsmslabel',
            'label' => Mage::helper('smsservice')->__('Select Label'),
            'options' => Mage::getModel('smsservice/system_config_source_smsLabels')->getLableslist(),
            'value' => Mage::getModel('smsservice/system_config_source_smsautomation')->getValue($smsautomation, $key[3], 'label')
        ));
        $fieldset4->addField('outsenderid', 'text', array(
            'name' => 'outsenderid',
            'label' => Mage::helper('smsservice')->__('Sender Id'),
            'title' => Mage::helper('smsservice')->__('Sender Id'),
            'value' => Mage::getModel('smsservice/system_config_source_smsautomation')->getValue($smsautomation, $key[3], 'senderid')
        ));
        $fieldset4->addField('btnoutstock', 'submit', array(
            'name' => 'btnoutstock',
            'value' => 'Save'
        ));

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
