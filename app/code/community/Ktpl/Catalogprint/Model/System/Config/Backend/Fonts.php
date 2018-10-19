<?php

class Ktpl_Catalogprint_Model_System_Config_Backend_Fonts extends Mage_Adminhtml_Model_System_Config_Backend_Image
{
    /**
     * Getter for allowed extensions of uploaded files
     *
     * @return array
     */
    protected function _getAllowedExtensions()
    {
        return array('ttf');
    }
    
    /**
     * Return the root part of directory path for uploading
     *
     * @var string
     * @return string
     */
    protected function _getUploadRoot($token)
    {
        return Mage::getBaseDir('lib');
    }
    
    protected function _beforeSave() {
        parent::_beforeSave();
        $this->setValue('');
        return $this;
    }
    

}

