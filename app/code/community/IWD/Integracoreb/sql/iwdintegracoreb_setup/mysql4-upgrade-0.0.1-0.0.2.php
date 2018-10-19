<?php

$installer = $this;
$installer->startSetup();
$tableIntegracoreb = $installer->getTable('iwdintegracoreb/table_integracoreb_api_log');

$installer->getConnection()->dropTable($tableIntegracoreb);
$table = $installer->getConnection()
	->newTable($tableIntegracoreb)
	->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'identity'  => true,
		'nullable'  => false,
		'primary'   => true,
	))
	->addColumn('text', Varien_Db_Ddl_Table::TYPE_TEXT, false, array(
		'nullable'  => false,
	))
	->addColumn('type', Varien_Db_Ddl_Table::TYPE_TEXT, false, array(
		'nullable'  => false,
	))
	->addColumn('created', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
		'nullable'  => false,
	));
$installer->getConnection()->createTable($table);

$installer->endSetup();