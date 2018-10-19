<?php
class Ktpl_Catalogprint_Model_System_Config_Source_Uploaded_Fonts
{
    public function toOptionArray()
    {
        $scanPath = Mage::getBaseDir().DS.'lib'.DS.'md-catalog-print'.DS;
        $scanDir = scandir($scanPath);
        $options = array();
        $i = 0;
        foreach($scanDir as $file){
            $fullPath = $scanPath.''.$file;
            if(is_file($fullPath)){
                $options[] = array("label"=>$file, "value"=>str_replace(Mage::getBaseDir(),'',$fullPath));
                
            }
        }
        return $options;
    }
}

