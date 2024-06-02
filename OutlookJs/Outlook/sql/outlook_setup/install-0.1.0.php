<?php
$installer = $this;
$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('ccc_outlook/configuration'))
    ->addColumn('configuration_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity' => true,
        'nullable' => false,
        'primary' => true,
    ], 'Configuration ID')
    ->addColumn('email_address', Varien_Db_Ddl_Table::TYPE_TEXT, null, [
        'nullable' => false,
    ], 'Email')
    ->addColumn('password', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, [
        'nullable' => false,
    ], 'Email')
    ->addColumn('api_key', Varien_Db_Ddl_Table::TYPE_TEXT, null, [
        'nullable' => false,
    ], 'API Key')
    ->addColumn('token', Varien_Db_Ddl_Table::TYPE_TEXT, null, [
        'nullable' => false,
    ], 'Token')
    ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'nullable' => false,
        'default' => '1',
    ], 'Is Active')
    ->setComment('Configuration Table');
$installer->getConnection()->createTable($table);

$installer->endSetup();
