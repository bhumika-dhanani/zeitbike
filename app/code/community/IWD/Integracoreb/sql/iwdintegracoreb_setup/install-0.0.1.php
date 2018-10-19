<?php

$installer = $this;
$tableIntegracoreb = $installer->getTable('iwdintegracoreb/table_integracoreb');
//Mage::log("Your Log Message", null, "your_log_file.log");
//die('log save');
$installer->startSetup();

$installer->getConnection()->dropTable($tableIntegracoreb);
$table = $installer->getConnection()
    ->newTable($tableIntegracoreb)
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ))
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ))
    ->addColumn('accepted', Varien_Db_Ddl_Table::TYPE_BOOLEAN, false, array(
        'nullable'  => false,
    ))
    ->addColumn('is_shipped', Varien_Db_Ddl_Table::TYPE_BOOLEAN, false, array(
        'nullable'  => false,
    ))
    ->addColumn('created', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'nullable'  => false,
    ))
    ->addColumn('update', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'nullable'  => false,
    ))
    ->addColumn('order_number', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable'  => false,
    ));
$installer->getConnection()->createTable($table);

$tableIntegracoreb = $installer->getTable('iwdintegracoreb/table_integracoreb_log');

$installer->getConnection()->dropTable($tableIntegracoreb);
$table = $installer->getConnection()
    ->newTable($tableIntegracoreb)
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ))
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ))
    ->addColumn('created', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'nullable'  => false,
    ))
    ->addColumn('error', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable'  => false,
    ));
$installer->getConnection()->createTable($table);

$installer->endSetup();