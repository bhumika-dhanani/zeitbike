<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Base
 */


class Amasty_Base_Model_Feed extends Mage_AdminNotification_Model_Feed
{
    const HOUR_MIN_SEC_VALUE = 86400;//60 * 60 * 24

    const REMOVE_EXPIRED_FREQUENCY = 21600;//4 times per day 60 * 60 * 6

    const XML_FREQUENCY_PATH = 'ambase/feed/frequency';

    const XML_LAST_REMOVMENT = 'ambase/system_value/remove_date';

    const XML_LAST_UPDATE_PATH = 'ambase/feed/last_update';

    const URL_NEWS = 'amasty.com/feed-news.xml';//don't pass http or https

    public function check()
    {
        $this->checkUpdate();
        $this->removeExpiredItems();
    }

    protected function _isPromoSubscribed()
    {
        return Mage::helper("ambase/promo")->isSubscribed();
    }

    public function checkUpdate()
    {
        if (($this->getFrequency() + $this->getLastUpdate()) > time()) {
            return $this;
        }

        if (!extension_loaded('curl') || !Mage::helper('ambase')->isModuleActive('Mage_AdminNotification')) {
            return $this;
        }

        $allowedNotifications = $this->getAllowedTypes();
        if (empty($allowedNotifications)
            || in_array(Amasty_Base_Model_Source_Type::UNSUBSCRIBE_ALL, $allowedNotifications)
        ) {
            return $this;
        }

        $feedData = array();
        $maxPriority = 0;
        $feedXml = $this->getFeedData();
        $wasInstalled = gmdate('Y-m-d H:i:s', Amasty_Base_Helper_Module::baseModuleInstalled());

        if ($feedXml && $feedXml->channel && $feedXml->channel->item) {
            foreach ($feedXml->channel->item as $item) {
                if (!in_array((string)$item->type, $allowedNotifications)
                    || (int)$item->version == 2 // for magento two
                    || ((string)$item->edition && (string)$item->edition != $this->getCurrentEdition())
                ) {
                    continue;
                }

                if (!$this->validateByExtension((string)$item->extension)) {
                    continue;
                }

                $date = $this->getDate((string)$item->pubDate);
                $expired =(string)$item->expirationDate ? strtotime((string)$item->expirationDate) : null;
                $priority =(int)$item->priority ?: 1;

                if ($wasInstalled <= $date
                    && (!$expired || $expired > gmdate('U'))
                    && ($priority > $maxPriority)
                    && !$this->isItemExists($item)
                ) {
                    //add only one with the highest priority
                    $maxPriority = $priority;
                    $expired = $expired ? date('Y-m-d H:i:s', $expired) : null;
                    $feedData = array(
                        'severity' => Mage_AdminNotification_Model_Inbox::SEVERITY_NOTICE,
                        'date_added' => $this->getDate($date),
                        'expiration_date' => $expired,
                        'title' => (string)$item->title,
                        'description' => (string)$item->description,
                        'url' => (string)$item->link,
                        'is_amasty' => 1
                    );
                }
            }

            if ($feedData) {
                $inbox = Mage::getModel('adminnotification/inbox');

                if ($inbox) {
                    $inbox->parse(array($feedData));
                }
            }
        }

        $this->setLastUpdate();

        //load all available extensions in the cache
        Amasty_Base_Helper_Module::reload();

        return $this;
    }

    /**
     * @return $this
     */
    public function removeExpiredItems()
    {
        if ($this->getLastRemovement() + self::REMOVE_EXPIRED_FREQUENCY > time()) {
            return $this;
        }

        $collection = Mage::getResourceModel('ambase/inbox_expired_collection');
        foreach ($collection as $model) {
            $model->setIsRemove(1)->save();
        }

        $this->setLastRemovement();

        return $this;
    }

    protected function isItemExists(\SimpleXMLElement $item)
    {
        return Mage::getResourceModel('ambase/inbox_collection')->execute($item);
    }

    /**
     * @return int
     */
    public function getFrequency()
    {
        return Mage::getStoreConfig(self::XML_FREQUENCY_PATH) * self::HOUR_MIN_SEC_VALUE;
    }

    public function getLastUpdate()
    {
        return Mage::getStoreConfig(self::XML_LAST_UPDATE_PATH);
    }

    public function setLastUpdate()
    {
        $config = Mage::getModel('core/config');
        /* @var $config Mage_Core_Model_Config */
        $config->saveConfig(self::XML_LAST_UPDATE_PATH, time());
        Mage::getConfig()->cleanCache();
        return $this;
    }

    /**
     * @return int
     */
    protected function getLastRemovement()
    {
        return Mage::getStoreConfig(self::XML_LAST_REMOVMENT);
    }

    /**
     * @return $this
     */
    protected function setLastRemovement()
    {
        $config = Mage::getModel('core/config');
        /* @var $config Mage_Core_Model_Config */
        $config->saveConfig(self::XML_LAST_REMOVMENT, time());
        Mage::getConfig()->cleanCache();
        return $this;
    }

    public function getFeedUrl()
    {
        if (is_null($this->_feedUrl)) {
            $this->_feedUrl = (Mage::getStoreConfigFlag(self::XML_USE_HTTPS_PATH) ? 'https://' : 'http://')
                . self::URL_NEWS;
        }
        return $this->_feedUrl;
    }

    protected function isExtensionInstalled($code)
    {
        $modules = array_keys((array)Mage::getConfig()->getNode('modules')->children());
        foreach ($modules as $moduleName) {
            if ($moduleName == $code) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array
     */
    protected function getAllowedTypes()
    {
        $allowedNotifications = Mage::getStoreConfig('ambase/feed/type');
        $allowedNotifications = explode(',', $allowedNotifications);

        return $allowedNotifications;
    }

    /**
     * @return string
     */
    protected function getCurrentEdition()
    {
        return Mage::getConfig()->getNode('modules/Enterprise_Enterprise') ? 'ee' : 'ce';
    }


    /**
     * @return array
     */
    protected function getInstalledAmastyExtensions()
    {
        $modules = Mage::getConfig()->getNode('modules')->children();
        $modules = (array)$modules;

        $modules = array_filter(
            $modules,
            function ($item) {
                return strpos($item, 'Amasty_') !== false;
            }
        );

        return $modules;
    }


    /**
     * @param string $extensions
     * @return bool
     */
    protected function validateByExtension($extensions)
    {
        if ($extensions) {
            $result = false;
            $extensions = explode(',', $extensions);
            $extensions = array_filter(
                $extensions,
                function ($item) {
                    return strpos($item, '_2') === false;
                }
            );

            $extensions = array_map(
                function ($item) {
                    return str_replace('_1', '', $item);
                },
                $extensions
            );

            if ($extensions) {
                $amastyModules = $this->getInstalledAmastyExtensions();
                $intersect = array_intersect($extensions, $amastyModules);
                if ($intersect) {
                    $result = true;
                }
            }
        } else {
            $result = true;
        }

        return $result;
    }
}
