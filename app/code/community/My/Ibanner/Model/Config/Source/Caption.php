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
class My_Ibanner_Model_Config_Source_Caption
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'no', 'label'=>Mage::helper('adminhtml')->__('No')),
            array('value' => 'left', 'label'=>Mage::helper('adminhtml')->__('Left')),
            array('value' => 'right', 'label'=>Mage::helper('adminhtml')->__('Right'))
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
            'no' => Mage::helper('adminhtml')->__('No'),
            'left' => Mage::helper('adminhtml')->__('Left'),
            'right' => Mage::helper('adminhtml')->__('Right')
        );
    }
}
