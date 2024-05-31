<?php

$installer = $this;

$installer->startSetup();

$tableName = $installer->getTable('ccc_outlook/configuration');

if ($installer->getConnection()->isTableExists($tableName)) {
    Mage::log("Table $tableName already exists", null, 'outlook_configuration.log');
    return;
}
$table = $installer->getConnection()
    ->newTable($installer->getTable('ccc_outlook/configuration'))
    ->addColumn('configuration_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'nullable' => false,
        'primary' => true,
    ), 'Configuration Id')
    ->addColumn('username', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => false,
    ), 'User Name')
    ->addColumn('password', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => false,
    ), 'Password')
    ->addColumn('api_key', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => false,
    ), 'API Key')
    ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => false,
    ), 'Is Active')
    ->addColumn('token', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => false,
    ), 'Token')
    ->setComment('CCC Outlook Table');
$installer->getConnection()->createTable($table);
$installer->endSetup();
