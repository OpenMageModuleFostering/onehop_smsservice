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
 * Preapare Edit template form 
 *
 * @category    Onehop
 * @package     Onehop_SMSService
 * @author      Screen-Magic Mobile Media Inc. (info@onehop.co)
 */

class Onehop_SMSService_Block_Adminhtml_Edittemplate_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare form.
     */
    protected function _prepareForm()
    {
        $templateid   = $this->getRequest()->getParam('smstemplateid');
        $templateinfo = Mage::getModel('smsservice/system_config_source_smsTemplates')->gettemplateInfoByID($templateid);
        $form         = new Varien_Data_Form(array(
            'name' => 'addtemplate_form',
            'id' => 'edit_form',
            'action' => $this->getUrl('smsservice/adminhtml/updatetemplate'),
            'method' => 'post'
        ));

        $fieldset = $form->addFieldset('addtemplate_fieldset', array(
            'legend' => Mage::helper('smsservice')->__('Edit Template'),
            'class' => 'fieldset'
        ));

        $fieldset->addField('templateid', 'hidden', array(
            'name' => 'templateid',
            'value' => $templateid
        ));
        $fieldset->addField('templatename', 'text', array(
            'name' => 'templatename',
            'label' => Mage::helper('smsservice')->__('Template Name'),
            'value' => Mage::getModel('smsservice/system_config_source_smstemplates')->getTemplateFeilds($templateinfo, 'templatename'),
            'required' => true
        ));
        $fieldset->addType('smstextarea', 'Varien_Data_Form_Element_Smsservicetextarea');
        $fieldset->addField('templatebody', 'smstextarea', array(
            'name' => 'templatebody',
            'label' => Mage::helper('smsservice')->__('Template Body'),
            'value' => Mage::getModel('smsservice/system_config_source_smstemplates')->getTemplateFeilds($templateinfo, 'templatebody'),
            'required' => true
        ));

        $form->setUseContainer(true);
        $this->setForm($form);
        
        return parent::_prepareForm();
    }
}
