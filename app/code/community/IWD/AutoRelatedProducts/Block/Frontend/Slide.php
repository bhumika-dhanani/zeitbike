<?php
class IWD_AutoRelatedProducts_Block_Frontend_Slide extends Mage_Core_Block_Template
{
    const XML_PATH_SLIDER_MIN_SLIDES = "iwd_autorelatedproducts/slider/min_slides";
    const XML_PATH_SLIDER_MAX_SLIDES = "iwd_autorelatedproducts/slider/max_slides";
    const XML_PATH_SLIDER_SLIDE_WIDTH = "iwd_autorelatedproducts/slider/slide_width";
    const XML_PATH_SLIDER_SLIDE_MARGIN = "iwd_autorelatedproducts/slider/slide_margin";
    const XML_PATH_SLIDER_SLIDE_MODE = "iwd_autorelatedproducts/slider/slide_mode";
    const XML_PATH_SLIDER_INFINITY_LOOP = "iwd_autorelatedproducts/slider/infinite_loop";

    public function getMinSlides()
    {
        return Mage::getStoreConfig(self::XML_PATH_SLIDER_MIN_SLIDES, Mage::app()->getStore());
    }

    public function getMaxSlides()
    {
        return Mage::getStoreConfig(self::XML_PATH_SLIDER_MAX_SLIDES, Mage::app()->getStore());
    }

    public function getSlideWidth()
    {
        return Mage::getStoreConfig(self::XML_PATH_SLIDER_SLIDE_WIDTH, Mage::app()->getStore());
    }

    public function getSlideMargin()
    {
        return Mage::getStoreConfig(self::XML_PATH_SLIDER_SLIDE_MARGIN, Mage::app()->getStore());
    }

    public function getSlideMode()
    {
        return Mage::getStoreConfig(self::XML_PATH_SLIDER_SLIDE_MODE, Mage::app()->getStore());
    }

    public function getInfiniteLoop()
    {
        return Mage::getStoreConfig(self::XML_PATH_SLIDER_INFINITY_LOOP, Mage::app()->getStore());
    }
    public function customAddToCartButton()
    {
        return Mage::getStoreConfig(self::XML_PATH_SLIDER_INFINITY_LOOP, Mage::app()->getStore());
    }
}