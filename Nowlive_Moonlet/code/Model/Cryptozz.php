<?php

class Nowlive_Moonlet_Model_Crypto extends Mage_Core_Model_Abstract //Mage_Payment_Model_Method_Abstract
{
	protected $_code = 'moonlet';

	/*
	protected $_formBlockType = 'moonlet/form_crypto';
	protected $_infoBlockType = 'moonlet/info_crypto';

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
	
    public function isAvailable($quote = null)
    {
        Mage::helper("moonlet")->log(__CLASS__."::".__METHOD__);
		
		$helper = Mage::helper('moonlet/crypto'); 
		if($helper->isMethodAvailable() === true)
		{
			Mage::dispatchEvent('payment_method_is_active', array(
				'result'          => true,
				'method_instance' => $this,
				'quote'           => $quote,
			));
	
			return true;
		}
		else
		{
			return false;
		}
	} 
	
	*/
	
	/*
	public function process($data){

		if($data['cancel'] == 1){
		 $order->getPayment()
		 ->setTransactionId(null)
		 ->setParentTransactionId(time())
		 ->void();
		 $message = 'Unable to process Payment';
		 $order->registerCancellation($message)->save();
		}
	}

	public function capture(Varien_Object $payment, $amount)
	{
		$order = $payment->getOrder();
		$result = $this->callApi($payment,$amount,'authorize');
		if($result === false) {
			$errorCode = 'Invalid Data';
			$errorMsg = $this->_getHelper()->__('Error Processing the request');
		} else {
			Mage::log($result, null, $this->getCode().'.log');
			//process result here to check status etc as per payment gateway.
			// if invalid status throw exception

			if($result['status'] == 1){
				$payment->setTransactionId($result['transaction_id']);
				$payment->setIsTransactionClosed(1);
				$payment->setTransactionAdditionalInfo(Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS,array('key1'=>'value1','key2'=>'value2'));
			}else{
				Mage::throwException($errorMsg);
			}

			// Add the comment and save the order
		}
		if($errorMsg){
			Mage::throwException($errorMsg);
		}

		return $this;
	}


	public function authorize(Varien_Object $payment, $amount)
	{
		$order = $payment->getOrder();
		$result = $this->callApi($payment,$amount,'authorize');
		if($result === false) {
			$errorCode = 'Invalid Data';
			$errorMsg = $this->_getHelper()->__('Error Processing the request');
		} else {
			Mage::log($result, null, $this->getCode().'.log');
			//process result here to check status etc as per payment gateway.
			// if invalid status throw exception

			if($result['status'] == 1){
				$payment->setTransactionId($result['transaction_id']);

				// This marks transactions as closed or open
				$payment->setIsTransactionClosed(1);
				
				// This basically makes order status to be payment review and no invoice is created.
				// and adds a default comment like
				// Authorizing amount of $17.00 is pending approval on gateway. Transaction ID: "1335419269".
				//$payment->setIsTransactionPending(true);

				// This basically makes order status to be processing and no invoice is created.
				// add a default comment to order like
				// Authorized amount of $17.00. Transaction ID: "1335419459".
				//$payment->setIsTransactionApproved(true);

				// This method is used to display extra informatoin on transaction page
				$payment->setTransactionAdditionalInfo(Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS,array('key1'=>'value1','key2'=>'value2'));

				$order->addStatusToHistory($order->getStatus(), 'Payment Sucessfully Placed with Transaction ID'.$result['transaction_id'], false);
				$order->save();
			} else {
				Mage::throwException($errorMsg);
			}

			// Add the comment and save the order
		}
		if($errorMsg){
			Mage::throwException($errorMsg);
		}

		return $this;
	}

	public function processBeforeRefund($invoice, $payment){
		return parent::processBeforeRefund($invoice, $payment);
	}
	public function refund(Varien_Object $payment, $amount){
		$order = $payment->getOrder();
		$result = $this->callApi($payment,$amount,'refund');
		if($result === false) {
			$errorCode = 'Invalid Data';
			$errorMsg = $this->_getHelper()->__('Error Processing the request');
			Mage::throwException($errorMsg);
		}
		return $this;

	}
	public function processCreditmemo($creditmemo, $payment){
		return parent::processCreditmemo($creditmemo, $payment);
	}

	private function callApi(Varien_Object $payment, $amount,$type){

		//call your authorize api here, incase of error throw exception.
		//only example code written below to show flow of code

		return array(
			'status' =>1,
			'transaction_id' => time(),
			'fraud' => 0
		);
	}
	*/
}