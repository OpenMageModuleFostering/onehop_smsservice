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
 * @category  Onehop
 * @package   Onehop_SMSService
 * @copyright Copyright (c) 2016 Onehop (http://www.onehop.co)
 * @license   https://www.gnu.org/licenses/gpl-2.0.html  Open Software License (GPL2)
 */

/**
 * Preapare Add template form
 *
 * @category Onehop
 * @package  Onehop_SMSService
 * @author   Screen-Magic Mobile Media Inc. (info@onehop.co)
 */

class Onehop_SMSService_Block_Adminhtml_Addtemplate_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare form.
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(
            array(
            'name' => 'addtemplate_form',
            'id' => 'edit_form',
            'action' => $this->getUrl('smsservice/adminhtml/savetemplates'),
            'method' => 'post'
            )
        );

        $fieldset = $form->addFieldset(
            'addtemplate_fieldset', array(
            'legend' => Mage::helper('smsservice')->__('Add New Template'),
            'class' => 'fieldset'
            )
        );

        $fieldset->addField(
            'templatename', 'text', array(
            'name' => 'templatename',
            'label' => Mage::helper('smsservice')->__('Template Name'),
            'required' => true
            )
        );
        $fieldset->addType('smstextarea', 'Varien_Data_Form_Element_Smsservicetextarea');
        $fieldset->addField(
            'templatebody', 'smstextarea', array(
            'name' => 'templatebody',
            'label' => Mage::helper('smsservice')->__('Template Body'),
            'required' => true
            )
        );
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
