<?php
/**
 *
 * @category    Onehop
 * @package     Onehop_SMSService
 * @author      Screen-Magic Mobile Media Inc. (info@onehop.co)
 * @license     https://www.gnu.org/licenses/gpl-2.0.html  Open Software License (GPL2)
 */


$installer = $this;

$installer->startSetup();
Mage::helper('smsservice/mysql4_install')->prepareForDb();

Mage::helper('smsservice/mysql4_install')->attemptQuery($installer, "
    DROP TABLE IF EXISTS {$this->getTable('onehop_smstemplates')};
    CREATE TABLE IF NOT EXISTS `{$this->getTable('onehop_smstemplates')}` (
      `smstemplates_id` int(10) NOT NULL auto_increment,
      `temp_name` varchar(50) NOT NULL,
      `temp_body` varchar(500) NOT NULL,
      `submitdate` datetime,
      PRIMARY KEY  (`smstemplates_id`)      
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    
    DROP TABLE IF EXISTS {$this->getTable('sms_onehop_rulesets')};
    CREATE TABLE IF NOT EXISTS `{$this->getTable('sms_onehop_rulesets')}` (
      `ruleid` int(10) NOT NULL auto_increment,
      `rule_name` varchar(50) NOT NULL,
      `template` int(10) NOT NULL,
      `label` varchar(50) NOT NULL,
      `senderid` varchar(50) NOT NULL,
      `active` enum('1', '0') default 1,
      PRIMARY KEY  (`ruleid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

Mage::helper('smsservice/mysql4_install')
->createInstallNotice("Onehop SMSService was installed successfully.",
"Onehop SMSService has been installed successfully. Go to the system configuration
section of your Magento admin to configure Onehop SMSService and
get it up and running.");

$installer->endSetup();
