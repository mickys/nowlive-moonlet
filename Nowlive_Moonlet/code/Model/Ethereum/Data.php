<?php
use Web3\Web3;

class Nowlive_Moonlet_Model_Ethereum_Data extends Mage_Core_Model_Abstract
{
    private $web3;

    public function init() {
        file_get_contents("http://nowlive.ro/geth.ropsten/");
		$host = "http://nowlive.ro/geth.ropsten/";
        $this->web3 = new Web3( $host );
    }

    public function getAccountBalance( $account ) {
        $this->init();

        $AccountBalance = 0;
        $this->web3->eth->getBalance($account, function ($err, $balance) use (&$AccountBalance) {
            if ($err !== null) {
                echo $err->getMessage();
                throw( $err->getMessage() );
            }
            $AccountBalance = $balance;
        });
        return $AccountBalance;
    }

}