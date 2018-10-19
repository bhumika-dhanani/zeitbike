<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Stockstatus
 */
$installer = $this;
$installer->startSetup();

$installer->run("
CREATE TABLE IF NOT EXISTS `{$this->getTable('amasty_stockstatus_icontype')}`  (
    `option_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    `type` VARCHAR(10) NOT NULL
) ENGINE = InnoDB ;
");
$installer->endSetup();