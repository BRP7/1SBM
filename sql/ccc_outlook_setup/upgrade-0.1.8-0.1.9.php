<?php
$installer = $this;
$installer->startSetup();


$tableName = $installer->getTable('ccc_outlook/email');


$installer->getConnection()->addColumn($tableName, 'has_attachment', array(
    'type'     => Varien_Db_Ddl_Table::TYPE_TEXT,
    'nullable' => false,
    'length'   => 255,
    'after'    => 'to_email', // Position of the new column
    'comment'  => 'Indicates whether the email has attachments'
));

$installer->getConnection()->addColumn($tableName, 'message_id', array(
    'type'     => Varien_Db_Ddl_Table::TYPE_VARCHAR,
    'nullable' => false,
    'length'   => 255,
    'comment'  => 'The message ID of the email'
));

$installer->endSetup();
