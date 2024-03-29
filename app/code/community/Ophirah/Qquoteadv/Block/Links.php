<?php
class Ophirah_Qquoteadv_Block_Links extends Mage_Core_Block_Template
{
    /**
     * Add Quote link to parent block
     *
     * @return Ophirah_Qquoteadv_Block_Links
     */
    public function addQuoteLink()
    {
        $parentBlock = $this->getParentBlock();
        if ($parentBlock && Mage::helper('core')->isModuleOutputEnabled('Ophirah_Qquoteadv')) {
            $text = Mage::helper('qquoteadv')->totalItemsText();
            $parentBlock->addLink($text, 'qquoteadv/index', $text, true, array(), 50, null, 'class="top-link-qquoteadv"');
        }
        return $this;
    }
    
    
}
