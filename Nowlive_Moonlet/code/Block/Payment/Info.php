<?php

class Nowlive_Moonlet_Block_Payment_Info extends Mage_Payment_Block_Info
{

    protected $_transaction_id;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('moonlet/payment/info/moonlet.phtml');
    }

    public function getTransactionId()
    {
        if (is_null($this->_transaction_id)) {
            $this->_convertAdditionalData();
        }
        return $this->_transaction_id;
    }

    protected function _convertAdditionalData()
    {
        $details = false;
        try {
            $details = Mage::helper('core/unserializeArray')
                ->unserialize($this->getInfo()->getAdditionalData());
        } catch (Exception $e) {
            Mage::logException($e);
        }
        if (is_array($details)) {
            $this->_transaction_id = isset($details['transaction_id']) ? (string) $details['transaction_id'] : '';
        } else {
            $this->_transaction_id = '';
        }
        return $this;
    }

    /*
    public function toPdf()
    {
        $this->setTemplate('payment/info/pdf/checkmo.phtml');
        return $this->toHtml();
    }
    */
}
