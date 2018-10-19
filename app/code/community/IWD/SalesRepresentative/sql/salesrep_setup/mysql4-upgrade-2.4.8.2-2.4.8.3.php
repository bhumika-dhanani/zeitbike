<?php
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;

$installer->startSetup();

$installer->run(" 
		ALTER TABLE {$this->getTable('iwd_sales_representative_users_link')}
		    CHANGE COLUMN `link_id`     `iwd_link_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            CHANGE COLUMN `user_id`	`iwd_user_id` int(10) unsigned NOT NULL DEFAULT '0',
            CHANGE COLUMN `linked_product_id`	`iwd_linked_product_id` int(10) unsigned NOT NULL DEFAULT '0',
            CHANGE COLUMN `product_rate_type`	`iwd_product_rate_type` int(1) NOT NULL,
            CHANGE COLUMN `product_percent_rate`	`iwd_product_percent_rate` float NOT NULL,
            CHANGE COLUMN `product_fixed_rate`	`iwd_product_fixed_rate` float NOT NULL,
            drop primary key, add primary key (`iwd_link_id`),
            ADD UNIQUE KEY `IWD_UNQ_CAT_PRD_LNK_LNK_TYPE_ID_PRD_ID_LNKED_PRD_ID` (`iwd_user_id`,`iwd_linked_product_id`),
            ADD	KEY `IWD_IDX_SELREP_PRODUCT_LINK_PRODUCT_ID` (`iwd_user_id`),
            ADD	KEY `IWD_IDX_SELREP_PRODUCT_LINK_LINKED_PRODUCT_ID` (`iwd_linked_product_id`),
            DROP FOREIGN KEY `FK_SER_PRD_LNK_LNKED_PRD_ID_CAT_PRD_ENTT_ENTT_ID`,
            ADD CONSTRAINT `IWD_FK_SER_PRD_LNK_LNKED_PRD_ID_CAT_PRD_ENTT_ENTT_ID`
            FOREIGN KEY (`iwd_linked_product_id`) REFERENCES  `catalog_product_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE;
");

$installer->run("
		ALTER TABLE {$this->getTable('iwd_sales_representative_users')}
            CHANGE COLUMN   `entity_id`                  `iwd_entity_id` int(11) NOT NULL AUTO_INCREMENT,
            CHANGE COLUMN   `user_id`                    `iwd_user_id` int(11) NOT NULL,
            CHANGE COLUMN   `name`                      `iwd_name` varchar(255) NOT NULL,
            CHANGE COLUMN   `color`                      `iwd_color` varchar(6) NOT NULL,
            CHANGE COLUMN   `global`                    `iwd_global` int(1) NOT NULL DEFAULT '0',
            CHANGE COLUMN   `rate_type`                `iwd_rate_type` int(2) NOT NULL,
            CHANGE COLUMN   `percent_rate`           `iwd_percent_rate` float NOT NULL,
            CHANGE COLUMN   `fixed_rate`               `iwd_fixed_rate` float NOT NULL,
            CHANGE COLUMN   `order_notification_report` `iwd_order_notification_report` INT(1) NULL DEFAULT 0 ,
            CHANGE COLUMN   `product_notification_report` `iwd_product_notification_report` INT(1) NULL DEFAULT 0,
            CHANGE COLUMN 	`limit_users`      `iwd_limit_users`  INT(1) ZEROFILL NOT NULL,
            CHANGE COLUMN 	`limit_items`      `iwd_limit_items`  VARCHAR(255) NULL,
            CHANGE COLUMN 	`notify`  `iwd_notify`  INT(1) ZEROFILL NOT NULL,
            CHANGE COLUMN `rate_type_order`          `iwd_rate_type_order` INT(2) NULL,
            CHANGE COLUMN `percent_rate_order`   `iwd_percent_rate_order` FLOAT NULL,
            CHANGE COLUMN `fixed_rate_order`    `iwd_fixed_rate_order` FLOAT NULL,
            CHANGE COLUMN `show_user_orders_only` `iwd_show_user_orders_only` INT(2) NULL,
            CHANGE COLUMN `status` `iwd_status` INT(1) NOT NULL DEFAULT 1,
            CHANGE COLUMN `autoasssign_user` `iwd_autoasssign_user` INT(1) NOT NULL DEFAULT 0,
            drop primary key, add primary key (`iwd_entity_id`),
            add   UNIQUE KEY `iwd_user_id` (`iwd_user_id`);
");


$installer->run("
		ALTER TABLE {$this->getTable('iwd_sales_representative')}
            CHANGE COLUMN     `entity_id`            `iwd_entity_id` int(11) NOT NULL AUTO_INCREMENT,
            CHANGE COLUMN      `order_id`           `iwd_order_id` int(11) NOT NULL,
            CHANGE COLUMN      `user_id`            `iwd_user_id` int(11) NOT NULL,
            drop primary key, add            PRIMARY KEY (`iwd_entity_id`);
        ");
$installer->run("
		ALTER TABLE {$this->getTable('iwd_sales_representative_users_customers')}
            CHANGE COLUMN `link_id`                      `iwd_link_id` int(11) NOT NULL AUTO_INCREMENT,
            CHANGE COLUMN `user_id`                     `iwd_user_id` int(10) unsigned DEFAULT NULL,
            CHANGE COLUMN `linked_customer_id`     `iwd_linked_customer_id` int(10) unsigned DEFAULT NULL,
            drop primary key, add   PRIMARY KEY (`iwd_link_id`);
        ");

$installer->endSetup();
