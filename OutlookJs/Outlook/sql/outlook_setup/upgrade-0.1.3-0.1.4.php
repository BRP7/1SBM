<?php
$installer = $this;
$installer->startSetup();
$tableName = $installer->getTable('ccc_outlook/configuration');
$table = $installer->getConnection()
    ->addColumn($tableName, 'last_readed_id', array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable' => true,
        'comment' => 'Last Read Id',
    ));
    $installer->getConnection()->addForeignKey(
        $installer->getFkName(
            'ccc_outlook/configuration',
            'last_readed_id',
            'ccc_outlook/email',
            'email_id'
        ),
        $tableName,
        'last_readed_id',
        $installer->getTable('ccc_outlook/email'),
        'email_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    );
   
    $tableName = $installer->getTable('ccc_outlook/configuration');
$table = $installer->getConnection()
    ->addColumn($tableName, 'client_id', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable' => true,
        'length' => null,
        'comment' => 'Client ID',
    ));
$table = $installer->getConnection()
    ->addColumn($tableName, 'client_secret', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable' => true,
        'length' => null,
        'comment' => 'Client Secret',
    ));
$table = $installer->getConnection()
    ->addColumn($tableName, 'redirect_url', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable' => true,
        'length' => null,
        'comment' => 'Redirect URL',
    ));

$table = $installer->getConnection()
    ->addColumn($tableName, 'scope', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable' => true,
        'length' => '64k',
        'comment' => 'Scope',
    ));
$installer->endSetup();
