<?php

class Nowlive_Moonlet_Block_Payment_Form extends Mage_Payment_Block_Form
{

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('moonlet/payment/form/moonlet.phtml');
    }

}
