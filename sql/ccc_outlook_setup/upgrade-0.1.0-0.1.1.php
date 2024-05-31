

<?php
// Define upgrade script
$installer = $this;
$installer->startSetup();

// Add status column to the table
$installer->getConnection()
    ->addColumn($installer->getTable('ccc_outlook/configuration'), 'read_count', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable'  => true,
        'default'   => 0,
        'comment'   => 'Last Readed Mail'
    ));

$installer->endSetup();
