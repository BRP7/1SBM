<?php
$installer = $this;
$installer->startSetup();
$tableName = $installer->getTable('ccc_outlook/configuration');
$table = $installer->getConnection()
    ->dropColumn($tableName, 'email', $schemaName = null);
$table = $installer->getConnection()
    ->dropColumn($tableName, 'password', $schemaName = null);
$table = $installer->getConnection()
    ->dropColumn($tableName, 'api_key', $schemaName = null);
$table = $installer->getConnection()
    ->dropColumn($tableName, 'token', $schemaName = null);
$installer->endSetup();

