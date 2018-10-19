<?php
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;

$installer->startSetup();

$installer->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('iwd_sales_representative_users')} (
		`entity_id` int(11) NOT NULL AUTO_INCREMENT,
		`user_id` int(11) NOT NULL,
		`name` varchar(255) NOT NULL,
		`color` varchar(6) NOT NULL,
		`global` int(1) NOT NULL DEFAULT '0',
		`rate_type` int(2) NOT NULL,
		`percent_rate` float NOT NULL,
		`fixed_rate` float NOT NULL,
		PRIMARY KEY (`entity_id`),
		UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS {$this->getTable('iwd_sales_representative_users_link')} (
		`link_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		`user_id` int(10) unsigned NOT NULL DEFAULT '0',
		`linked_product_id` int(10) unsigned NOT NULL DEFAULT '0',
		`product_rate_type` int(1) NOT NULL,
		`product_percent_rate` float NOT NULL,
		`product_fixed_rate` float NOT NULL,
		PRIMARY KEY (`link_id`),
		UNIQUE KEY `UNQ_CAT_PRD_LNK_LNK_TYPE_ID_PRD_ID_LNKED_PRD_ID` (`user_id`,`linked_product_id`),
		KEY `IDX_SELREP_PRODUCT_LINK_PRODUCT_ID` (`user_id`),
		KEY `IDX_SELREP_PRODUCT_LINK_LINKED_PRODUCT_ID` (`linked_product_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8  AUTO_INCREMENT=1 ;
");

$installer->run("
ALTER TABLE {$this->getTable('iwd_sales_representative_users_link')}
ADD CONSTRAINT `FK_SER_PRD_LNK_LNKED_PRD_ID_CAT_PRD_ENTT_ENTT_ID` FOREIGN KEY (`linked_product_id`) REFERENCES {$this->getTable('catalog_product_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE;
");

$installer->endSetup();