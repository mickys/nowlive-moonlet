<?php

class Nowlive_Moonlet_Model_Crypto extends Mage_Payment_Model_Method_Abstract
{
    protected $_code = 'moonlet';
	protected $_formBlockType = 'moonlet/payment_form';
    protected $_infoBlockType = 'moonlet/payment_info';
    
    protected $_isGateway               = true;
	protected $_canAuthorize            = true;
	protected $_canCapture              = false;
	protected $_canCapturePartial       = false;
	protected $_canRefund               = false;
	protected $_canSaveCc 				= false; 	//if made true, the actual credit card number and cvv code are stored in database.

	//protected $_canRefundInvoicePartial = true;
	//protected $_canVoid                 = true;
	//protected $_canUseInternal          = true;
	//protected $_canUseCheckout          = true;
	//protected $_canUseForMultishipping  = true;
	//protected $_canFetchTransactionInfo = true;
	//protected $_canReviewPayment        = true;

	/**
     * Assign data to info model instance
     *
     * @param   mixed $data
     * @return  Mage_Payment_Model_Method_Checkmo
     */
    public function assignData($data)
    {
        $details = array();
        if ($this->getTransactionId()) {
            $details['transaction_id'] = $this->getTransactionId();
		}
		
        if (!empty($details)) {
            $this->getInfoInstance()->setAdditionalData(serialize($details));
        }
        return $this;
	}
	
}