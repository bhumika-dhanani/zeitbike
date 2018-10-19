<?php 
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;

$installer->startSetup();

$installer->run("
		
		
		CREATE TABLE IF NOT EXISTS {$this->getTable('iwd_sales_representative_users_customers')} (
		  `link_id` int(11) NOT NULL AUTO_INCREMENT,
		  `user_id` int(10) unsigned DEFAULT NULL,
		  `linked_customer_id` int(10) unsigned DEFAULT NULL,
		  PRIMARY KEY (`link_id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;
		
		
		
		
		
		
		
		
		
		ALTER TABLE {$this->getTable('iwd_sales_representative_users')}
		ADD COLUMN `limit_users`  INT(1) ZEROFILL NOT NULL AFTER `product_notification_report`,
		ADD COLUMN `limit_items`  VARCHAR(255) NULL AFTER `limit_users`,
		ADD COLUMN `notify`  INT(1) ZEROFILL NOT NULL AFTER `limit_items`;
");

$installer->endSetup ();