<?php

class IWD_Cache_Model_Cron
{
    public function refreshInvalideCache()
    {
        $invalid = Mage::app()->getCacheInstance()->getInvalidatedTypes();

        foreach($invalid as $i)
        {
            Mage::app()->getCacheInstance()->cleanType($i["id"]);
        }
    }
}