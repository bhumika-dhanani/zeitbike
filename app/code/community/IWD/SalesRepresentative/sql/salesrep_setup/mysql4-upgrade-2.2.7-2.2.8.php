<?php 
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;

$installer->startSetup();

$installer->run("
		
		ALTER TABLE {$this->getTable('iwd_sales_representative_users')}
		ADD COLUMN `status` INT(1) NOT NULL DEFAULT 1 AFTER `notify`;
");

$installer->run("

		ALTER TABLE {$this->getTable('iwd_sales_representative_users')}
		ADD COLUMN `autoasssign_user` INT(1) NOT NULL DEFAULT 0 AFTER `status`;
");

$installer->endSetup ();