<?php
class Rishabhsoft_AdvanceImport_Model_Product extends Mage_Catalog_Model_Product
{
 
    protected function _beforeSave()
    {
		parent::_beforeSave();
		$this->setHasOptions(true);
    }
}
