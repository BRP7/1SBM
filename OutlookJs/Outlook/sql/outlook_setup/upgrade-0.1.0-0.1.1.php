<?php
$installer = $this;
$installer->startSetup();

$tableName = $installer->getTable('ccc_outlook/email');

if ($installer->getConnection()->isTableExists($tableName)) {
    Mage::log("Table $tableName already exists", null, 'outlook_email.log');
    return;
}
$table = $installer->getConnection()
    ->newTable($installer->getTable('ccc_outlook/email'))
    ->addColumn('email_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'nullable' => false,
        'primary' => true,
    ), 'Email Id')
    ->addColumn('subject', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => false,
    ), 'Subject')
    ->addColumn('from', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => false,
    ), 'From email')
    
    ->addColumn('body', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => false,
    ), 'Body')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable' => false,
        'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT,
    ), 'Created At')
    ->setComment('CCC Outlook Email Table');

$installer->getConnection()->createTable($table);
$installer->endSetup();
