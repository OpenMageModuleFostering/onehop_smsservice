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
 * Preapare send SMS form 
 *
 * @category    Onehop
 * @package     Onehop_SMSService
 * @author      Screen-Magic Mobile Media Inc. (info@onehop.co)
 */

class Onehop_SMSService_Block_Adminhtml_Send_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare form.
     */
    protected function _prepareForm()
    {
        $templateurl = $this->getUrl('smsservice/adminhtml/index/');
        $form        = new Varien_Data_Form(array(
            'name' => 'send_form',
            'id' => 'edit_form',
            'action' => $this->getData('action'),
            'method' => 'post'
        ));

        $fieldset = $form->addFieldset('sendsms_fieldset', array(
            'legend' => Mage::helper('smsservice')->__('Send SMS'),
            'class' => 'fieldset'
        ));

        $fieldset->addField('mobilenumber', 'text', array(
            'name' => 'mobilenumber',
            'label' => Mage::helper('smsservice')->__('Mobile Number'),
            'title' => Mage::helper('smsservice')->__('Mobile Number'),
            'required' => true
        ));

        $fieldset->addField('senderid', 'text', array(
            'name' => 'senderid',
            'label' => Mage::helper('smsservice')->__('Sender Id'),
            'title' => Mage::helper('smsservice')->__('Sender Id'),
            'required' => true
        ));

        $fieldset->addField('smslabel', 'selectonehop', array(
            'name' => 'smslabel',
            'label' => Mage::helper('smsservice')->__('Select Label'),
            'required' => true,
            'options' => Mage::getModel('smsservice/system_config_source_smsLabels')->getLableslist()
        ));

        $fieldset->addField('smstemplate', 'selectonehop', array(
            'name' => 'smstemplate',
            'label' => Mage::helper('smsservice')->__('Select Template'),
            'options' => Mage::getModel('smsservice/system_config_source_smsTemplates')->getTemplatesList(),
            'onchange' => 'getTemplate()'
        ));

        $fieldset->addType('smstextarea', 'Varien_Data_Form_Element_Smsservicetextarea');
        $fieldset->addField('sms_text', 'smstextarea', array(
            'name' => 'sms_text',
            'label' => Mage::helper('smsservice')->__('Message Body'),
            'required' => true
        ));

        $form->setUseContainer(true);
        $this->setForm($form);
        
        return parent::_prepareForm();
    }
}
