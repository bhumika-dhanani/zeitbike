<?php
$installer = $this;
$installer->startSetup();

$installer->run("
        DROP TABLE IF EXISTS {$this->getTable('iwd_customer_coupon')};
        CREATE TABLE {$this->getTable('iwd_customer_coupon')}(
          `id` INT(11) NOT NULL AUTO_INCREMENT,
          `cause` VARCHAR(50) NOT NULL,
          `customer_email` VARCHAR(255) NOT NULL,
          `customer_firstname` VARCHAR(255) NOT NULL,
          `customer_lastname` VARCHAR(255) NOT NULL,
          `customer_group_id` INT(11),
          `card_id` INT(11),
          `coupon_code` VARCHAR(255),
          `customer_dob` DATETIME,
          `customer_id` INT(11),
          `date_receive` TIMESTAMP NOT NULL,
          `date_expire` DATETIME,
          `date_used` DATETIME,
          PRIMARY KEY (`id`)
        ) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
	");

$installer->endSetup();
