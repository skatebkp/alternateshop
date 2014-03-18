<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    My
 * @package     My_Ibanner
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Banner Helper
 *
 * @category   My
 * @package    My_Ibanner
 * @author     Theodore Doan <theodore.doan@gmail.com>
 */
class My_Ibanner_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_AUTO_RESIZE      = 'ibanner/general/auto_resize';
    const XML_PATH_CAPTION_POSITION = 'ibanner/general/caption_position';

    const XML_PATH_WIDTH    = 'ibanner/general/width';
    const XML_PATH_HEIGHT   = 'ibanner/general/height';

    const XML_PATH_AUTO_PLAY        = 'ibanner/general/auto_play';
    const XML_PATH_TRANSITION       = 'ibanner/general/transition';
    const XML_PATH_HOVER_PAUSE      = 'ibanner/general/hover_pause';
    const XML_PATH_STOP_ON_CLICK    = 'ibanner/general/stop_on_click';

    const XML_PATH_SPEED            = 'ibanner/general/speed';
    const XML_PATH_ANIMATION_SPEED  = 'ibanner/general/animation_speed';
    

    /*
     * Get image url of a banner
     */
    public function getImageUrl($url = null) {
        return Mage::getSingleton('ibanner/config')->getBaseMediaUrl() . $url;
    }

    /*
     * Is auto resize?
     */
    public function getAutoResize() {
        return (Mage::getStoreConfig(self::XML_PATH_AUTO_RESIZE) == "1") ? true : false;
    }

    /*
     * Get image width
     */
    public function getWidth() {
        return intval(Mage::getStoreConfig(self::XML_PATH_WIDTH));
    }

    /*
     * Get image height
     */
    public function getHeight() {
        return intval(Mage::getStoreConfig(self::XML_PATH_HEIGHT));
    }

    /*
     * Get caption position
     */
    public function getCaptionPosition() {
        return Mage::getStoreConfig(self::XML_PATH_CAPTION_POSITION);
    }

    /*
     * Is auto play?
     */
    public function getAutoPlay() {
        return (Mage::getStoreConfig(self::XML_PATH_AUTO_PLAY) == "1") ? true : false;
    }

    /*
     * Get transition
     */
    public function getTransition() {
        return intval(Mage::getStoreConfig(self::XML_PATH_TRANSITION));
    }

    /*
     * Pause on hovering
     */
    public function getHoverPause() {
        return (Mage::getStoreConfig(self::XML_PATH_HOVER_PAUSE) == "1") ? true : false;
    }

    /*
     * Stop on clicking
     */
    public function getStopOnCLick() {
        return (Mage::getStoreConfig(self::XML_PATH_STOP_ON_CLICK) == '1') ? 'true' : 'false';
    }

    /*
     * Speed
     */
    public function getSpeed() {
        return intval(Mage::getStoreConfig(self::XML_PATH_SPEED));
    }

    /*
     * Animation speed
     */
    public function getAnimationSpeed() {
        return intval(Mage::getStoreConfig(self::XML_PATH_ANIMATION_SPEED));
    }

    /**
     * Encode the mixed $valueToEncode into the JSON format
     *
     * @param mixed $valueToEncode
     * @param  boolean $cycleCheck Optional; whether or not to check for object recursion; off by default
     * @param  array $options Additional options used during encoding
     * @return string
     */
    public function jsonEncode($valueToEncode, $cycleCheck = false, $options = array())
    {
        $json = Zend_Json::encode($valueToEncode, $cycleCheck, $options);
        /* @var $inline Mage_Core_Model_Translate_Inline */
        $inline = Mage::getSingleton('core/translate_inline');
        if ($inline->isAllowed()) {
            $inline->setIsJson(true);
            $inline->processResponseBody($json);
            $inline->setIsJson(false);
        }

        return $json;
    }
}