<?php
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;

$installer->startSetup();

$installer->run("

		ALTER TABLE {$this->getTable('iwd_sales_representative_users')}
ADD COLUMN `rate_type_order` INT(2) NULL AFTER `autoasssign_user`,
ADD COLUMN `percent_rate_order` FLOAT NULL AFTER `rate_type_order`,
ADD COLUMN `fixed_rate_order` FLOAT NULL AFTER `percent_rate_order`;
");

$installer->endSetup ();