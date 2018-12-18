<?php

use Web3\Utils;

class Nowlive_Moonlet_Model_Etherscan_Data extends Mage_Core_Model_Abstract
{
    const XML_PATH_API_KEY = 'moonlet/general/etherscan_api_key';
    const XML_PATH_API_URL = 'moonlet/general/etherscan_api_url';

    public function getApiKey()
    {
        return Mage::getStoreConfig(self::XML_PATH_API_KEY);
    }

    public function getApiUrl()
    {
        return Mage::getStoreConfig(self::XML_PATH_API_URL);
    }

    public function getRequestUrl($params) {

        $defaults = array(
            "startblock" => "0",
            "endblock" => "99999999",
            "apikey" => $this->getApiKey(),
        );

        $newParams = array_merge( $defaults, $params );

        $chunks = array();
        foreach($newParams as $key => $val) {
            $chunks[] = $key."=".$val;
        }

        return $this->getApiUrl()."?".implode("&", $chunks);
    }

    public function getAccountTransactions( $address, $sort = "asc" ) {

        $url = $this->getRequestUrl(
            array( 
                "module" => "account",
                "action" => "txlist",
                "address" => $address,
                "sort" => $sort
            )
        );

        return $this->getApiData($url);
    }

    public function getApiData($url) {
        $ch = curl_init();

        $headers = array(
            'Accept: application/json',
            'Content-Type: application/json',
        );

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
        // Timeout in seconds
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
   
        return json_decode( curl_exec($ch), true);
    }

    public function getTransactionDetails( $txn, $account ) {
        $txns = $this->getAccountTransactions( $account );

        foreach($txns["result"] as $tx) {
            if($tx["hash"] == $txn) {
                $tx["value"] = Utils::toBn( $tx["value"] );
                return $tx;
            }
        }
        return false;
    }

}