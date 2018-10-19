<?php
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("
		drop table if exists {$this->getTable('iwd_stocknotification')};
		
	

		CREATE TABLE {$this->getTable('iwd_stocknotification')} (
		  `entity_id` int(11) NOT NULL AUTO_INCREMENT,
		  `product_id` int(11) DEFAULT NULL,
		  `parent_id` int(11) DEFAULT NULL,
		  `email` varchar(255) DEFAULT NULL,
		  `customer_id` int(11) DEFAULT NULL,
		  `created_at` datetime DEFAULT NULL,
		  `is_notified` int(1) DEFAULT NULL,
		  `store_id` int(1) DEFAULT NULL,
		  PRIMARY KEY (`entity_id`)
		) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
		



");

$installer->endSetup();