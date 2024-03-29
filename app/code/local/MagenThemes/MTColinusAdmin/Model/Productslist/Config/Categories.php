<?php
/*------------------------------------------------------------------------
# APL Solutions and Vision Co., LTD
# ------------------------------------------------------------------------
# Copyright (C) 2008-2012 APL Solutions and Vision Co., LTD. All Rights 

Reserved.
# @license - Copyrighted Commercial Software
# Author: APL Solutions and Vision Co., LTD
# Websites: http://www.joomlavision.com/ - http://www.magentheme.com/
-------------------------------------------------------------------------*/ 
class MagenThemes_MTColinusAdmin_Model_ProductsList_Config_Categories
{

    public function toOptionArray()
    {
		$category = Mage::getModel('catalog/category'); 
		$tree = $category->getTreeModel(); 
		$tree->load();
		$ids = $tree->getCollection()->getAllIds(); 
		$arr = array();
		if ($ids)
		{ 
			$i=0;
			foreach ($ids as $id)
				{ 
					$cat = Mage::getModel('catalog/category'); 
					$cat->load($id);	
					if($cat->getIsActive()==1 && $cat->getId()!=1)
					{
						$arr[$i]=array('value'=>$cat->getId(), 'label'=>Mage::helper('adminhtml')->__($cat->getName()));
						$i++;
					}					
				} 
		}
		return $arr;
    }
    
}
