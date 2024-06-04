<?php

$installer = $this;
$installer->startSetup();


$installer->getConnection()
    ->addColumn($installer->getTable('ccc_outlook/email'), 'configuration_id', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable'  => false,
        'comment'   => 'Configuration ID'
    ));


$installer->getConnection()->addForeignKey(
    $installer->getFkName('ccc_outlook/email', 'configuration_id', 'ccc_outlook/configuration', 'configuration_id'),
    $installer->getTable('ccc_outlook/email'),
    'configuration_id',
    $installer->getTable('ccc_outlook/configuration'),
    'configuration_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_CASCADE
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('ccc_outlook/attachment', 'email_id', 'ccc_outlook/email', 'email_id'),
    $installer->getTable('ccc_outlook/attachment'),
    'email_id',
    $installer->getTable('ccc_outlook/email'),
    'email_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_CASCADE
);

$installer->endSetup();