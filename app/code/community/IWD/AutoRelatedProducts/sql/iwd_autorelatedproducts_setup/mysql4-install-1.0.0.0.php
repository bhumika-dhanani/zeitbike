<?php
$installer = $this;
$installer->startSetup();

$installer->run("
        DROP TABLE IF EXISTS {$this->getTable('iwd_auto_related_products')};
        CREATE TABLE {$this->getTable('iwd_auto_related_products')}(
          `id` INT(11) NOT NULL AUTO_INCREMENT,

          `block_type` VARCHAR(50) NOT NULL COMMENT 'Block type',

          `status` INT(2) NOT NULL DEFAULT 0 COMMENT 'Enable / Disable block',

          `store_view` VARCHAR(500) NOT NULL COMMENT 'Show block on selected stores',
          `customer_group_ids` VARCHAR(500) NOT NULL COMMENT 'Customer groups',

          `title` VARCHAR(255) NULL COMMENT 'Block title',
          `description` TEXT NULL COMMENT 'Block description',

          `max_count_elements` INT(5) NOT NULL DEFAULT 50 COMMENT 'Max count elements in block',
          `sort_order` INT(2) NOT NULL DEFAULT 1 COMMENT 'Sort order products in block',
          `show_out_of_stock` BOOLEAN NOT NULL DEFAULT 1 COMMENT 'Show out of stock products in block',

          `current_conditions_serialized` TEXT NULL COMMENT 'Conditions for select current products',
          `current_products_id_serialized` TEXT NULL COMMENT 'Current products ids',
          `related_conditions_serialized` TEXT NULL COMMENT 'Conditions for select related products',
          `related_products_id_serialized` TEXT NULL COMMENT 'Related products ids',

          `shopping_cart_conditions_serialized` TEXT NULL COMMENT 'Shopping cart conditions serialized',

          `category_id_serialized` TEXT NULL COMMENT 'Category id serialized',

          `order_statuses_serialized` TEXT NULL COMMENT 'Order statuses by Who bought this also bought',

          `link_buttons` VARCHAR(250) NOT NULL COMMENT 'Buttons Add to...',

          `from_date` TIMESTAMP NULL DEFAULT NULL COMMENT 'Display block from date',
          `to_date` TIMESTAMP NULL DEFAULT NULL COMMENT 'Display block to date',
          `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Created At',
          `updated_at` timestamp NULL DEFAULT NULL COMMENT 'Updated At',
          PRIMARY KEY (`id`)
        ) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
	");

$installer->endSetup();
