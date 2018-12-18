<?php

use Web3\Utils;
use phpseclib\Math\BigInteger as BigNumber;

class Nowlive_Moonlet_IndexController extends Mage_Core_Controller_Front_Action
{

	protected $_errors = array();

    /**
     * @return Mage_Checkout_OnepageController
     */
    public function preDispatch()
    {
		parent::preDispatch();
		
		/** @var $helper Nowlive_Moonlet_Helper_Data */
        $helper = Mage::helper('moonlet');
        if (!$helper->isEnabled()) {
            return $this->_ajaxEnd(array(
                'error' => $helper->__('Please, use standard checkout.')
            ));
        }

	}
	
	/**
     * Get one page checkout model
     *
     * @return Mage_Checkout_Model_Type_Onepage
     */
    public function getOnepage()
    {
        return Mage::getSingleton('checkout/type_onepage');
	}
	
	protected function _ajaxEnd($data)
    {
		$this->getResponse()->clearHeaders()->setHeader('Content-type','application/json', true);
        return $this->getResponse()->setBody(
            Mage::helper('core')->jsonEncode($data)
        );
	}
	
	public function initNewOrderAction() {

		$product = Mage::getModel('catalog/product')->load(
			$this->getRequest()->getParam('product_id')
		);

		try {
			$order = $this->_saveOrder( $product->getSKU() );

			return $this->_ajaxEnd(array(
				'success' => true,
				'message' => "Order Created",
				'order_id' => $order->getIncrementId(),
				'price' => $product->getPrice(),
				'wallet_address' => Mage::helper("moonlet")->getMerchantAddress()
			));

		} catch (Exception $e) {
		
			Mage::log( __CLASS__."::".__METHOD__." exception" , null, 'moonlet.log');
			Mage::log( $e , null, 'moonlet.log');

			return $this->_ajaxEnd(array(
				'error' => json_encode($e),
			));
		}


	}

	public function savePaymentAction() {

		$order = Mage::getModel('sales/order')->loadByIncrementId(
			$this->getRequest()->getParam('id')
		);

		$payment = $order->getPayment();
		$payment->setTransactionId( 
			$this->getRequest()->getParam('txn')
		);

		$transaction = $payment->addTransaction(
			Mage_Sales_Model_Order_Payment_Transaction::TYPE_PAYMENT, null, false, "TXN: "
		);
		$transaction->setIsClosed(1);
		$transaction->save();

		$paymentInstance = $payment->getMethodInstance();
		$paymentInstance->setTransactionId( 
			$this->getRequest()->getParam('txn') 
		);
		$paymentInstance->assignData( array() );
		$order->save();



	}

	public function checkPaymentAction() {

		$order = Mage::getModel('sales/order')->loadByIncrementId(
			$this->getRequest()->getParam('id')
		);

		$txn = $this->getRequest()->getParam('txn');
		$account = Mage::helper("moonlet")->getMerchantAddress();
		$etherscan = Mage::getModel("moonlet/etherscan_data");
		$txnDetails = $etherscan->getTransactionDetails( $txn, $account );

		$totals = Mage::helper("moonlet")->getPriceToWei( 
			$order->getGrandTotal()
		);

		if(
			$txnDetails["to"] == strtolower($account) &&
			$txnDetails["value"]->equals($totals) == true &&
			$txnDetails["isError"] == 0 &&
			$txnDetails["txreceipt_status"] == 1
		) {

			$order->setData('state', Mage_Sales_Model_Order::STATE_COMPLETE);
			$order->setStatus(Mage_Sales_Model_Order::STATE_COMPLETE);
			$history = $order->addStatusHistoryComment('Order was set to Complete after Payment validation.', false);
			$order->save();

			// get downloadable url
			
			$url = Mage::getUrl(
				'downloadable/download/link/', 
				array('id' => $this->_getDownloadableLink($order),
				'_secure' => true)
			);

			return $this->_ajaxEnd(array(
				'success' => true,
				'url' => $url
			));
		}
		else {
			return $this->_ajaxEnd(array(
				'pending' => true
			));
		}
	}

	public function _getDownloadableLink($order) {

		$session = Mage::getSingleton('customer/session');
        $purchased = Mage::getResourceModel('downloadable/link_purchased_collection')
			->addFieldToFilter('order_id', $order->getId())
            ->addOrder('created_at', 'desc');
		
        $purchasedIds = array();
        foreach ($purchased as $_item) {
            $purchasedIds[] = $_item->getId();
        }
        if (empty($purchasedIds)) {
            $purchasedIds = array(null);
        }
        $purchasedItems = Mage::getResourceModel('downloadable/link_purchased_item_collection')
			->addFieldToFilter('purchased_id', array('in' => $purchasedIds))
			->addFieldToFilter('status',
				array(
					'nin' => array(
						Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_PENDING_PAYMENT,
						Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_PAYMENT_REVIEW
					)
				)
			)
			->setOrder('item_id', 'desc');
		
		return $purchasedItems->getFirstItem()->getLinkHash();
	}

	/*
	public function testAction()
    {

		$helper = Mage::helper("moonlet");
		$account = $helper->getMerchantAddress();

		$model = Mage::getModel("moonlet/ethereum_data");
		$etherscan = Mage::getModel("moonlet/etherscan_data");

		$balance = $model->getAccountBalance( $account );

		echo "Account: ".$account."<br />";
		echo "Balance: ".$balance."<br />";

		$txns = $etherscan->getAccountTransactions( $account );

		// print_r($txns);
	}

	public function saveAction() {

		$product_id = 1;
		$product = Mage::getModel('catalog/product')->load(
			$product_id
		);

		$order = $this->_saveOrder( $product->getSKU() );

		echo "Created order id:". $order->getIncrementId();


		$txn = '0x629b8124b8dac747bbccd2ec048f15b5ff0718db554a4a20171d4346f23d261c';

		$arrInformation = array("key" => "val");

		$payment = $order->getPayment();
		$payment->setTransactionId( $txn );

		$transaction = $payment->addTransaction(
			Mage_Sales_Model_Order_Payment_Transaction::TYPE_PAYMENT, null, false, "TXN: "
		);
		$transaction->setIsClosed(1);
		$transaction->save();

		$paymentInstance = $payment->getMethodInstance();
		$paymentInstance->setTransactionId( $txn );
		$paymentInstance->assignData( array() );


		$order->setData('state', Mage_Sales_Model_Order::STATE_COMPLETE);
		$order->setStatus(Mage_Sales_Model_Order::STATE_COMPLETE);
		$history = $order->addStatusHistoryComment('Order was set to Complete after Payment validation.', false);
		$order->save();


	}
	*/

	public function _saveOrder( $productSku ) {

		$order = Mage::getModel("moonlet/order");
		$order->setOrderInfo( $this->_getCustomer(), $productSku );
		return $order->create();
	}

	/**
     * @return Mage_Customer_Model_Customer
     */
    protected function _getCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    }
}