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
 * Form text element
 *
 * @category    Onehop
 * @package     Onehop_SMSService
 * @author      Screen-Magic Mobile Media Inc. (info@onehop.co)
 */
class Varien_Data_Form_Element_Smsservicetextarea extends Varien_Data_Form_Element_Textarea
{
    /**
     * Oneho Constructor
     *
     * @param array $attributes
     */
    public function __construct($attributes = array())
    {
        parent::__construct($attributes);
        $this->setType('textarea');
        $this->setExtType('textarea');
        $this->setRows(2);
        $this->setCols(20);
    }

    /**
     *
     * @return string
     */
    public function getName()
    {
        $form = $this->getForm();

        if ($form && $suffix = $this->getForm()->getFieldNameSuffix()) {
            $name = $this->getForm()->addSuffixToName($name, $suffix);
        } else {
            $name = $this->getData('name');
        }
        return $name;
    }

    /**
     *
     * @return string
     */
    public function getHtmlId()
    {
        $form = $this->getForm();

        if ($form) {
            return $form->getHtmlIdPrefix() . $this->getData('html_id') . $form->getHtmlIdSuffix();
        } else {
            return $this->getData('html_id');
        }
    }
    /**
     * @return array
     */
    public function getHtmlAttributes()
    {
        return array(
            'title',
            'class',
            'style',
            'onclick',
            'onchange',
            'rows',
            'cols',
            'readonly',
            'disabled',
            'onkeyup',
            'tabindex',
        );
    }
    /**
     * generate html for textarea field
     *
     * Add js functions
     *
     * @return string
     */
    public function getElementHtml()
    {
        $helper = Mage::helper('smsservice');

        $conurl = Mage::helper('adminhtml')->getUrl('adminhtml/system_config/edit/section/smsservice');

        $allowedUnicode = (int) Mage::getSingleton('smsservice/config')->isUnicodeAllowed();

        $id       = $this->getHtmlId();
        $remain   = $this->getHtmlId() . '-remain';
        $chars    = $this->getHtmlId() . '-chars';
        $messages = $this->getHtmlId() . '-messages';

        $selPlacehoder = array(
            'Customer',
            'Order',
            'Product',
        );

        $this->addClass('textarea');
        $html = '';

        $html .= '<p class="button-bar">';

        if ($id == 'templatebody') {
            $html .= '<select name="placeholders" id="placehoders" onchange="getplaceholderVal(this)"
                    style="float: left; width: 100px; margin-right: 10px;">';
            $html .= '<option value="">- Select Type -</option>';
            foreach ($selPlacehoder as $val) {
                $html .= '<option value="' . strtolower($val) . '">' . $val . '</option>';
            }
            $html .= '</select>';
            $html .= '<select name="placeholders" id="templateplaceholder" style="width:170px;">';
            $html .= '<option value="">Select Placeholder</option>';
            $html .= '</select>';
            $html .= '<span style="margin-left:10px;">
                    <a href="javascript:void(0)" onclick="insertPlaceholder()" 
                    class="insertbtn" title="' . $helper->__('Insert') . '" >Insert</a></span>';
        }

        $html .= '</p>';
        $html .= '<textarea id="' . $id . '" name="' . $this->getName() . '" ' . $this->serialize($this->getHtmlAttributes()) . ' maxlength="700">';
        $html .= $this->getEscapedValue();
        $html .= '</textarea> <br>';
        $html .= '<span class=""><b>You can write upto 700 characters.</b></span> <br>';
        $html .= '<span id="count"></span> ';
        $html .= $this->getAfterElementHtml();

        $html .= "
            <script type=\"text/javascript\">
            //<![CDATA[
                document.getElementById('$id').onkeyup = function () {
                    document.getElementById('count').innerHTML = 'Characters left: <b>' + (700 - this.value.length) + '</b>';
                };
                function getplaceholderVal(gettype) {
                    var selectedText = gettype.options[gettype.selectedIndex].innerHTML;
                    var selectedValue = gettype.value;
                    if(selectedValue == 'customer') {
                        var placeholderValues = ['Firstname', 'Lastname', 'Email', 'Mobile'];
                    } else if(selectedValue == 'order') {
                        var placeholderValues = ['Order ID',
                                                'Transaction ID',
                                                'Tracking ID',
                                                'Invoice',
                                                'Price',
                                                'Discount',
                                                'Shipping_Address'];                        
                    } else if(selectedValue == 'product') {
                        var placeholderValues = ['Product ID', 'Product Name'];
                    } else {
                        var placeholderValues = [];
                    }
                    var select = document.getElementById('templateplaceholder');
                    select.innerHTML = '';
                    if(placeholderValues.length > 0) {
                    for(var i = 0; i < placeholderValues.length; i++)
                    {
                        if(i == 0)
                        {
                            var opt = 'Select Placeholder';
                            var optVal = '';
                            var el = document.createElement('option');
                            el.textContent = opt;
                            el.value = '';
                            select.appendChild(el);
                        }
                        var opt = placeholderValues[i];
                        var optVal = placeholderValues[i];
                        var el = document.createElement('option');
                        el.textContent = opt;
                        el.value = '{'+optVal+'}';
                        select.appendChild(el);
                    }
                    } else {
                        var opt = 'Select Placeholder';
                        var optVal = '';
                        var el = document.createElement('option');
                        el.textContent = opt;
                        el.value = '';
                        select.appendChild(el);
                    }
                }
                function typeInTextarea(el, newText) {
                  var myTextarea = document.getElementById('$id');   
                  myTextarea.value += newText;
                }
                function insertPlaceholder() {
                    var charlimit = document.getElementById('$id').value;
                    var e = document.getElementById('templateplaceholder');
                    var Placestr = e.options[e.selectedIndex].value;
                    var PlaceholderAddLength = charlimit.length+Placestr.length;
                    if(PlaceholderAddLength > 700) {
                        document.getElementById('count').innerHTML = 'Characters left: <b>' + (700 - charlimit.length) + '</b> You can not insert this placeholder because this placeholder have more characters than which you have left.';
                    } else {
                        document.getElementById('count').innerHTML = 'Characters left: <b>' + (700 - PlaceholderAddLength) + '</b>';
                        typeInTextarea($('textarea'), Placestr)    
                    }                    
                }               
            //]]>
            </script>";

        return $html;
    }
}
