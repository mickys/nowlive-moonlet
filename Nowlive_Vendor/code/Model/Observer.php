<?php
 
class Nowlive_Vendor_Model_Observer
{
    public function controllerFrontInitBefore(Varien_Event_Observer $event)
    {
        self::init();
    }

    static function init()
    {
        // Add our vendor folder to our include path
        set_include_path(get_include_path() . PATH_SEPARATOR . Mage::getBaseDir('lib') . DS . 'Nowlive' . DS . 'web3.php' . DS . 'vendor');
 
        // Include the autoloader for composer
        require_once(Mage::getBaseDir('lib') . DS . 'Nowlive' . DS . 'web3.php' . DS . 'vendor' . DS . 'autoload.php');
    }
 
}