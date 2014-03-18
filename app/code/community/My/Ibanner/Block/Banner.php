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
 * Base Banner Block
 *
 * @category   My
 * @package    My_Ibanner
 * @author     Theodore Doan <theodore.doan@gmail.com>
 */
class My_Ibanner_Block_Banner extends Mage_Core_Block_Template {
    protected $_position = 'CONTENT_TOP';
    protected $_isActive = 1;
    protected $_banner = array();

    public function getBanner($position = 'CONTENT_TOP') {
        if (isset($this->_banner[$position])) {
            return $this->_banner[$position];
        }

        $storeId = Mage::app()->getStore()->getId();
        $collection = Mage::getModel('ibanner/banner')->getCollection()
                ->addEnableFilter($this->_isActive);
        if (!Mage::app()->isSingleStoreMode()) {
            $collection->addStoreFilter($storeId);
        }

        if (Mage::registry('current_category')) {
            $_categoryId = Mage::registry('current_category')->getId();
            $collection->addCategoryFilter($_categoryId);
        } elseif (Mage::app()->getFrontController()->getRequest()->getRouteName() == 'cms') {
            $_pageId = Mage::getBlockSingleton('cms/page')->getPage()->getPageId();
            $collection->addPageFilter($_pageId);
        }

        if ($position) {
            $collection->addPositionFilter($position);
        } elseif ($this->_position) {
            $collection->addPositionFilter($this->_position);
        }
        $this->_banner[$position] = $collection->getFirstItem();

        return $this->_banner[$position];
    }
}