<?php
$installer = $this;
$installer->startSetup();
$eav_installer = new Mage_Eav_Model_Entity_Setup('core_setup');
$eav_installer->addAttribute( Mage_Catalog_Model_Category::ENTITY, 'display_print', array(
            'group' => 'General Information',
            'input' => 'select',
            'type' => 'int',
            'label' => 'Display Pdf Print Link',
            'source' => 'eav/entity_attribute_source_boolean',
            'visible' => 1,
            'default' => 1,
            'required' => 0,
            'user_defined' => 0,
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        ) );
$installer->endSetup();
