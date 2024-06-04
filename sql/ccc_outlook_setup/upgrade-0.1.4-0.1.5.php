<?php

$installer = $this;
$installer->startSetup();

// Create vendor_inventory_configuration table
$tableName = $installer->getTable('ccc_outlook/dispatchevent');
$table = $installer->getConnection()
    ->newTable($tableName)
    ->addColumn('data_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 11, array(
        'unsigned' => true,
        'primary' => true,
        'nullable' => false,
        'auto_increment' => true,
    ), 'Data ID')
    ->addColumn('config_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'unsigned' => true,
        'nullable' => false,
    ), 'Configuration ID')
    ->addColumn('group_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'unsigned' => true,
        'nullable' => false,
    ), 'Group ID')
    ->addColumn('condition', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => false,
    ), 'Condition')
    ->addColumn('operator', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => false,
    ), 'Operator')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => false,
    ), 'Value')
    ->addColumn('event', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => false,
    ), 'Event')
    ->addIndex(
        $installer->getIdxName('ccc_outlook/dispatchevent', array('config_id')),
        array('config_id')
    )
    ->addForeignKey(
        $installer->getFkName(
            'ccc_outlook/dispatchevent',
            'config_id',
            'ccc_outlook/configuration',
            'configuration_id'
        ),
        'config_id',
        $installer->getTable('ccc_outlook/configuration'),
        'configuration_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setOption('charset', 'utf8')
    ->setOption('engine', 'InnoDB');
$installer->getConnection()->createTable($table);

$installer->endSetup();
