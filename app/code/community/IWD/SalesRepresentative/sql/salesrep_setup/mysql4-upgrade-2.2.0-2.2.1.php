<?php
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('iwd_sales_representative_users')}
ADD COLUMN `order_notification_report` INT(1) NULL DEFAULT 0 AFTER `fixed_rate`,
ADD COLUMN `product_notification_report` INT(1) NULL DEFAULT 0 AFTER `order_notification_report`;
");

$installer->endSetup ();