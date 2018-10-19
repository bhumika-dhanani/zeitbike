<?php
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;

$installer->startSetup();

$installer->run("
		ALTER TABLE {$this->getTable('iwd_sales_representative_users')}
ADD COLUMN `show_user_orders_only` INT(2) NULL AFTER `autoasssign_user`;
");

$installer->endSetup();