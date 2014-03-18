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
 * @category    Mage
 * @package     Mage_Tax
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Calculate items and address amounts including/excluding tax
 */
class Ophirah_Qquoteadv_Model_Tax_Sales_Total_Quote_Subtotal extends Mage_Tax_Model_Sales_Total_Quote_Subtotal
{




    /**
     * Recalculate row information for item based on children calculation
     *
     * @param   Mage_Sales_Model_Quote_Item_Abstract $item
     * @return  Mage_Tax_Model_Sales_Total_Quote_Subtotal
     */
    protected function _recalculateParent(Mage_Sales_Model_Quote_Item_Abstract $item)
    {
        $price       = 0;
        $basePrice   = 0;
        $rowTotal    = 0;
        $baseRowTotal= 0;
        $priceInclTax       = 0;
        $basePriceInclTax   = 0;
        $rowTotalInclTax    = 0;
        $baseRowTotalInclTax= 0;
        foreach ($item->getChildren() as $child) {
            $price              += $child->getPrice() * $child->getQty();
            $basePrice          += $child->getBasePrice() * $child->getQty();
            $rowTotal           += $child->getRowTotal();
            $baseRowTotal       += $child->getBaseRowTotal();
            $priceInclTax       += $child->getPriceInclTax() * $child->getQty();
            $basePriceInclTax   += $child->getBasePriceInclTax() * $child->getQty();
            $rowTotalInclTax    += $child->getRowTotalInclTax();
            $baseRowTotalInclTax+= $child->getBaseRowTotalInclTax();
        }
        
        
        /**
        *
        * Customisation To make the custom price work with configurable items
        *
        **/
        
        if($item->getCustomPrice()){ 
					$customPrice = $item->getCustomPrice(); 
					$price = $customPrice; 
					$basePrice = $customPrice; 
					$rowTotal = $customPrice * $item->getQty(); 
					$baseRowTotal = $customPrice * $item->getQty(); 
					$priceInclTax = $customPrice; 
					$basePriceInclTax = $customPrice; 
					$rowTotalInclTax = $customPrice * $item->getQty(); 
					$baseRowTotalInclTax= $customPrice * $item->getQty(); 
				}
        
        $item->setConvertedPrice($price);
        $item->setPrice($basePrice);
        $item->setRowTotal($rowTotal);
        $item->setBaseRowTotal($baseRowTotal);
        $item->setPriceInclTax($priceInclTax);
        $item->setBasePriceInclTax($basePriceInclTax);
        $item->setRowTotalInclTax($rowTotalInclTax);
        $item->setBaseRowTotalInclTax($baseRowTotalInclTax);
        return $this;
    }
}