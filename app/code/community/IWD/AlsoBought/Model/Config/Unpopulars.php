<?php
class IWD_AlsoBought_Model_Config_Unpopulars
{
    const UNPOPULAR_NOTHING = 'nothing';
    const UNPOPULAR_CATEGORY = 'category';
    const UNPOPULAR_CROSSSELL = 'crosssell';
    const UNPOPULAR_UPSELL = 'upsell';
    const UNPOPULAR_RELATED = 'related';

    public function toOptionArray()
    {
        return array(
            array('value' => self::UNPOPULAR_NOTHING, 'label' => 'Not display'),
            array('value' => self::UNPOPULAR_CATEGORY, 'label' => 'Product by category'),
            array('value' => self::UNPOPULAR_CROSSSELL, 'label' => 'Cross-sells product'),
            array('value' => self::UNPOPULAR_UPSELL, 'label' => 'Up-sells product'),
            array('value' => self::UNPOPULAR_RELATED, 'label' => 'Related product'),
        );
    }
}