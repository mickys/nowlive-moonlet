<?php

use Web3\Utils;
use phpseclib\Math\BigInteger as BigNumber;

class Nowlive_Moonlet_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_ENABLED = 'moonlet/oneclickpay/enabled';
    const XML_PATH_MERCHANT_ADDRESS = 'moonlet/merchant/address';

    /**
     * Is OneClick Order functionality enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ENABLED);
    }

    public function getMerchantAddress()
    {
        return Mage::getStoreConfig(self::XML_PATH_MERCHANT_ADDRESS);
    }


    public function getTrackerAddress( $erc20 = "dai" ) {
        // return 
    }

    public function getPriceToWei( $price ) {
        $OneEth = Utils::toBn( "10000000000000000"); 
		return $OneEth->multiply( new BigNumber( $price * 100) );
    }

    public function log($what) {
        Mage::log( $what , null, 'moonlet.log');
    }
}
