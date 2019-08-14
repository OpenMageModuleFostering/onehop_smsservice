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

$installer->run("DROP TABLE IF EXISTS {$this->getTable('onehop_sms_templates')}");
$table = $installer->getConnection()
    ->newTable($installer->getTable('onehop_sms_templates'))
    ->addColumn('smstemplates_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'smstemplates_id')
    ->addColumn('temp_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 50, array(
        'nullable'  => false,
        ), 'temp_name')
    ->addColumn('temp_body', Varien_Db_Ddl_Table::TYPE_VARCHAR, 500, array(
        'nullable'  => false,
        ), 'temp_body')
    ->addColumn('submitdate', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'nullable'  => false,
        ), 'submitdate');
$installer->getConnection()->createTable($table);

$installer->run("DROP TABLE IF EXISTS {$this->getTable('onehop_sms_rulesets')}");
$table = $installer->getConnection()
    ->newTable($installer->getTable('onehop_sms_rulesets'))
    ->addColumn('ruleid', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'ruleid')
    ->addColumn('rule_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 50, array(
        'nullable'  => false,
        ), 'rule_name')
    ->addColumn('template', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        ), 'template')
    ->addColumn('label', Varien_Db_Ddl_Table::TYPE_VARCHAR, 50, array(
        'nullable'  => false,
        ), 'label')
    ->addColumn('senderid', Varien_Db_Ddl_Table::TYPE_VARCHAR, 50, array(
        'nullable'  => false,
        ), 'senderid')
    ->addColumn('active', Varien_Db_Ddl_Table::TYPE_VARCHAR, 1, array(
        'nullable'  => false,
        'default'   => '1',
        ), 'active');
$installer->getConnection()->createTable($table);

$installer->endSetup();
