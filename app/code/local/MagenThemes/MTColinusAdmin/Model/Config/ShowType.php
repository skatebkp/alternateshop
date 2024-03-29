<?php
/*------------------------------------------------------------------------
# APL Solutions and Vision Co., LTD
# ------------------------------------------------------------------------
# Copyright (C) 2008-2010 APL Solutions and Vision Co., LTD. All Rights Reserved.
# @license - Copyrighted Commercial Software
# Author: APL Solutions and Vision Co., LTD
# Websites: http://www.joomlavision.com/ - http://www.magentheme.com/
-------------------------------------------------------------------------*/ 
class MagenThemes_MTColinusAdmin_Model_Config_ShowType
{ 
    public function toOptionArray()
    {        
        return array(
            array('value' => '', 'label'=>Mage::helper('adminhtml')->__('-- None --')),
            array('value' => 'category', 'label'=>Mage::helper('adminhtml')->__('Description Category')),
            array('value' => 'static', 'label'=>Mage::helper('adminhtml')->__('Static Blocks')) 
        );
    }
}
