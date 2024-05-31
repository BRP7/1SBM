<?php
$installer = $this;
$installer->startSetup();

$tableName = $installer->getTable('ccc_outlook/attachment');

if ($installer->getConnection()->isTableExists($tableName)) {
    Mage::log("Table $tableName already exists", null, 'outlook_attachment.log');
    return;
}

$table = $installer->getConnection()
    ->newTable($installer->getTable('ccc_outlook/attachment'))
    ->addColumn('attachment_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'nullable' => false,
        'primary' => true,
    ), 'Attachment Id')
    ->addColumn('email_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false,
    ), 'Email Id')
    ->addColumn('filename', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => false,
    ), 'Filename')
    // ->addColumn('file_content', Varien_Db_Ddl_Table::TYPE_BLOB, null, array(
    //     'nullable' => false,
    // ), 'File Content')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable' => false,
        'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT,
    ), 'Created At')
    ->addForeignKey(
        $installer->getFkName(
            'ccc_outlook/attachment',
            'email_id',
            'ccc_outlook/email',
            'email_id'
        ),
        'email_id',
        $installer->getTable('ccc_outlook/email'),
        'email_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('CCC Outlook Attachment Table');

$installer->getConnection()->createTable($table);
$installer->endSetup();
