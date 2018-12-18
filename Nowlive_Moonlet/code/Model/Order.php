<?php

class Nowlive_Moonlet_Model_Order extends Mage_Core_Model_Abstract
{
    public $_productSku = "";
    private $_storeId = '1';
    private $_groupId = '1';
    private $_sendConfirmation = '0';

    private $orderData = array();
    private $_product;

    private $_sourceCustomer;

        /**
     * Retrieve order create model
     *
     * @return  Mage_Adminhtml_Model_Sales_Order_Create
     */
    protected function _getOrderCreateModel()
    {
        return Mage::getSingleton('adminhtml/sales_order_create');
    }

    /**
     * Retrieve session object
     *
     * @return Mage_Adminhtml_Model_Session_Quote
     */
    protected function _getSession()
    {
        return Mage::getSingleton('adminhtml/session_quote');
    }

    /**
     * Initialize order creation session data
     *
     * @param array $data
     * @return Mage_Adminhtml_Sales_Order_CreateController
     */
    protected function _initSession($data)
    {
        /* Get/identify customer */
        if (!empty($data['customer_id'])) {
            $this->_getSession()->setCustomerId((int) $data['customer_id']);
        }

        /* Get/identify store */
        if (!empty($data['store_id'])) {
            $this->_getSession()->setStoreId((int) $data['store_id']);
        }

        return $this;
    }

    public function setOrderInfo( Mage_Customer_Model_Customer $sourceCustomer, $productSku )
    {
        $this->_sourceCustomer = $sourceCustomer;

        // You can extract/refactor this if you have more than one product, etc.
        $this->_product = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToFilter('sku', $productSku)
            ->addAttributeToSelect('*')
            ->getFirstItem();

        // Load full product data to product object
        $this->_product->load($this->_product->getId());

        $billingAddress = $this->_sourceCustomer->getPrimaryBillingAddress();
        $shippingAddress = $this->_sourceCustomer->getPrimaryShippingAddress();

        $this->orderData = array(
            'session'       => array(
                'customer_id'   => $this->_sourceCustomer->getId(),
                'store_id'      => $this->_storeId,
            ),
            'payment'       => array(
                'method'    => 'moonlet',
            ),
            /*
            'shipping'      => array(
                'method'    => 'warehouse_warehouse',
            ),
            */
            'add_products'  => array(
                $this->_product->getId() => array('qty' => 1),
            ),
            'order' => array(
                'currency' => 'USD',
                'account' => array(
                    'group_id' => $this->_groupId,
                    'email' => $this->_sourceCustomer->getEmail()
                ),
                'billing_address' => array(
                    'customer_address_id' => $billingAddress->getId(),
                    'prefix' => '',
                    'firstname' => $this->_sourceCustomer->getFirstname(),
                    'middlename' => '',
                    'lastname' => $this->_sourceCustomer->getLastname(),
                    'suffix' => '',
                    'company' => '',
                    'street' => $billingAddress->getStreet(),
                    'city' => $billingAddress->getCity(),
                    'country_id' => $billingAddress->getCountryId(),
                    'region' => '',
                    'region_id' => $billingAddress->getRegionId(),
                    'postcode' => $billingAddress->getPostcode(),
                    'telephone' => 123123123,
                    'fax' => '',
                ),
                'shipping_address' => array(
                    'customer_address_id' => $shippingAddress->getId(),
                    'prefix' => '',
                    'firstname' => $this->_sourceCustomer->getFirstname(),
                    'middlename' => '',
                    'lastname' => $this->_sourceCustomer->getLastname(),
                    'suffix' => '',
                    'company' => '',
                    'street' => $shippingAddress->getStreet(),
                    'city' => $shippingAddress->getCity(),
                    'country_id' => $shippingAddress->getCountryId(),
                    'region' => '',
                    'region_id' => $shippingAddress->getRegionId(),
                    'postcode' => $shippingAddress->getPostcode(),
                    'telephone' => 123123123,
                    'fax' => '',
                ),
                // 'shipping_method' => 'warehouse_warehouse',
                'comment' => array(
                    'customer_note' => 'This order has been created by the Moonlet One Click Pay Module.',
                ),
                'send_confirmation' => $this->_sendConfirmation
            ),
        );
    }

    /**
     * Creates order
     */
    public function create()
    {
        $orderData = $this->orderData;

        if (!empty($orderData)) {

            $this->_initSession($orderData['session']);
            $this->_processQuote($orderData);

            if (!empty($orderData['payment'])) {
                    $this->_getOrderCreateModel()->setPaymentData($orderData['payment']);
                    $this->_getOrderCreateModel()->getQuote()->getPayment()->addData($orderData['payment']);
                }

                $_order = $this->_getOrderCreateModel()->importPostData($orderData['order']);
                if ($_order->getIsVirtual()) {
                    $_order->setAddress($order->getBillingAddress())->setSuppressShipping(true);
                }

                $_order->createOrder();

                $quote_id = $this->_getSession()->getQuoteId();
                $quote = Mage::getModel('sales/quote')->load($quote_id);
                $realOrder = Mage::getModel('sales/order')->loadByIncrementId(
                    $quote->getReservedOrderId()
                );

                $this->_getSession()->clear();
                Mage::unregister('rule_data');
                
                return $realOrder;

        } else {
            Mage::helper("moonlet")->log(__CLASS__."::".__METHOD__." no data ");
        }

        return null;
    }

    protected function _processQuote($data = array())
    {
        /* Saving order data */
        if (!empty($data['order'])) {
            $this->_getOrderCreateModel()->importPostData($data['order']);
        }

        $this->_getOrderCreateModel()->getBillingAddress();
        $this->_getOrderCreateModel()->setShippingAsBilling(true);

        /* Just like adding products from Magento admin grid */
        if (!empty($data['add_products'])) {
            $this->_getOrderCreateModel()->addProducts($data['add_products']);
        }

        /* Collect shipping rates */
        // $this->_getOrderCreateModel()->collectShippingRates();
        // Mage::log(__METHOD__." after collectShippingRates", null, "esb_tests_export_exception");

        /* Add payment data */
        if (!empty($data['payment'])) {
            $this->_getOrderCreateModel()->getQuote()->getPayment()->addData($data['payment']);
        }

        $this->_getOrderCreateModel()
            ->initRuleData()
            ->saveQuote();

        if (!empty($data['payment'])) {
            $this->_getOrderCreateModel()->getQuote()->getPayment()->addData($data['payment']);
        }

        return $this;
    }
}