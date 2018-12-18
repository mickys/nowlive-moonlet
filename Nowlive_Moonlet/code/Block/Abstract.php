<?php

class Nowlive_Moonlet_Block_Abstract extends Mage_Core_Block_Template
{

    /**
     * @return Nowlive_Moonlet_Helper_Data
     */
    public function getModuleHelper()
    {
        return Mage::helper('moonlet');
    }

    protected function _toHtml()
    {
        if (!$this->getModuleHelper()->isEnabled()) {
            return '';
        }
        return parent::_toHtml();
    }

    public function isGuest()
    {
        return !(bool)Mage::getSingleton('customer/session')->getCustomerId();
    }

}