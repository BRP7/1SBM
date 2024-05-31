<?php
// Define upgrade script
$installer = $this;
$installer->startSetup();

// Check if the table exists
$tableName = $installer->getTable('ccc_outlook/configuration');
if ($installer->getConnection()->isTableExists($tableName)) {
    // Add client_id column
    $installer->getConnection()
        ->addColumn($tableName, 'client_id', array(
            'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable'  => true,
            'default'   => '',
            'comment'   => 'Client Id'
        ));

    // Add client_secret column
    $installer->getConnection()
        ->addColumn($tableName, 'client_secret', array(
            'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable'  => true,
            'default'   => '',
            'comment'   => 'Client Secret'
        ));

    // Add tenant_id column
    $installer->getConnection()
        ->addColumn($tableName, 'tenant_id', array(
            'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable'  => true,
            'default'   => '',
            'comment'   => 'Tenant Id'
        ));
} else {
    Mage::log('Table ' . $tableName . ' does not exist', null, 'upgrade.log');
}

$installer->endSetup();
