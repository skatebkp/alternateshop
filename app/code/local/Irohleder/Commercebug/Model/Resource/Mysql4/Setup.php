<?php
/**
* Copyright © Pulsestorm LLC: All rights reserved
*/

class Irohleder_Commercebug_Model_Resource_Mysql4_Setup extends Mage_Core_Model_Resource_Setup
{
    public function getShim()
    {
        $shim = Irohleder_Commercebug_Model_Shim::getInstance();
        return $shim;
    }
}