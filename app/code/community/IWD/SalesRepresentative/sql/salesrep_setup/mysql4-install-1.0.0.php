<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("
				CREATE TABLE {$this->getTable('iwd_sales_representative')} (
				  `entity_id` int(11) NOT NULL AUTO_INCREMENT,
				  `order_id` int(11) NOT NULL,
				  `user_id` int(11) NOT NULL,
				  PRIMARY KEY (`entity_id`)
				) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

");
$installer->endSetup();