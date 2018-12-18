<?php
class Nowlive_Moonlet_Model_System_Config_Source_Crypto
{

    public function toOptionArray()
    {
        return array(
            array('value'=> 1, 'label' => Mage::helper('moonlet')->__('Ethereum - Native')),
            array('value'=> 2, 'label' => Mage::helper('moonlet')->__('Ethereum - ERC20 DAI')),
        );
    }
}