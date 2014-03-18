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
 * Position config model
 *
 * @category   My
 * @package    My_Ibanner
 * @author     Theodore Doan <theodore.doan@gmail.com>
 */
class My_Ibanner_Model_Config_Source_Transition
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => '-1', 'label'=>Mage::helper('adminhtml')->__('Random')),
            array('value' => '0', 'label'=>Mage::helper('adminhtml')->__('Fade out then fade in')),
            array('value' => '1', 'label'=>Mage::helper('adminhtml')->__('Full frame cross fade')),
            array('value' => '2', 'label'=>Mage::helper('adminhtml')->__('Paneled fold out')),
            array('value' => '3', 'label'=>Mage::helper('adminhtml')->__('Horizontal blinds')),
            array('value' => '4', 'label'=>Mage::helper('adminhtml')->__('Vertical blinds')),
            array('value' => '5', 'label'=>Mage::helper('adminhtml')->__('Small box random fades (personal favorite)')),
            array('value' => '6', 'label'=>Mage::helper('adminhtml')->__('full image blind slide'))
        );
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toGridOptionArray()
    {
        return array(
            '-1'=>Mage::helper('adminhtml')->__('Random'),
            '0'=>Mage::helper('adminhtml')->__('Fade out then fade in'),
            '1'=>Mage::helper('adminhtml')->__('Full frame cross fade'),
            '2'=>Mage::helper('adminhtml')->__('Paneled fold out'),
            '3'=>Mage::helper('adminhtml')->__('Horizontal blinds'),
            '4'=>Mage::helper('adminhtml')->__('Vertical blinds'),
            '5'=>Mage::helper('adminhtml')->__('Small box random fades (personal favorite)'),
            '6'=>Mage::helper('adminhtml')->__('full image blind slide')
        );
    }
}
