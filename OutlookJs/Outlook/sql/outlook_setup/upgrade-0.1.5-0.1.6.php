<?php
$installer = $this;
$installer->startSetup();
$tableName = $installer->getTable('ccc_outlook/configuration');
$table = $installer->getConnection()
     ->addColumn($tableName, 'email_address', Varien_Db_Ddl_Table::TYPE_TEXT, null, [
        'nullable' => false,
    ], 'Email');
$installer->endSetup();

