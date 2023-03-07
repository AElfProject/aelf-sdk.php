<?php

namespace AElf\Api;

use AElf\Bytes\Bytes;
use StephenHill\Base58;
use Hhxsv5\PhpMultiCurl\Curl as Curl;

Class BlockChainSdk
{
    private $curl;
    private $base58;
    private $version;
    private $aelfClientUrl;
    private static $WA_BLOCKHEIGHT = "/api/blockChain/blockHeight";
    private static $WA_BLOCK = "/api/blockChain/block";
    private static $WA_BLOCKBYHEIGHT = "/api/blockChain/blockByHeight";
    private static $WA_GETTRANSACTIONPOOLSTATUS = "/api/blockChain/transactionPoolStatus";
    private static $WA_GETCHAINSTATUS = "/api/blockChain/chainStatus";
    private static $WA_GETCFCRIPTORSET = "/api/blockChain/contractFileDescriptorSet";
    private static $WA_GETTASKQUEUESTATUS = "/api/blockChain/taskQueueStatus";
    private static $WA_EXECUTETRANSACTION = "/api/blockChain/executeTransaction";
    private static $WA_EXECUTERAWTRANSACTION = "/api/blockChain/executeRawTransaction";
    private static $WA_CREATERAWTRANSACTION = "/api/blockChain/rawTransaction";
    private static $WA_SENDRAWTRANSACTION = "/api/blockChain/sendRawTransaction";
    private static $WA_SENDTRANSACTION = "/api/blockChain/sendTransaction";
    private static $WA_GETTRANSACTIONRESULT = "/api/blockChain/transactionResult";
    private static $WA_GETTRANSACTIONRESULTS = "/api/blockChain/transactionResults";
    private static $WA_SENDTRANSACTIONS = "/api/blockChain/sendTransactions";
    private static $WA_GETMBYTRANSACTIONID = "/api/blockChain/merklePathByTransactionId";
    private static $WA_CALCULATETRANSACTIONFEERESULT = "/api/blockChain/calculateTransactionFee";
    private static $WA_GET;

    /**
     * Object construction through the url path.
     */
    public function __construct($url, $version = '')
    {
        $this->aelfClientUrl = $url;
        $this->version = $version;
        $options = [//The custom options of cURL
            CURLOPT_TIMEOUT => 0,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_USERAGENT => 'Multi-cURL client v1.5.0',
        ];
        $this->curl = new Curl(null, $options);
        $this->base58 = new Base58();
    }


    /**
     * Get the height of the current chain. wa:/api/blockChain/blockHeight
     */
    public function getBlockHeight()
    {
        $this->curl->makeGet($this->aelfClientUrl . self::$WA_BLOCKHEIGHT);
        $success = $this->curl->exec();
        if ($success->hasError()) {
            //Fail
            var_dump($success->getError());
        } else {
            //Success
            return $success->getBody();
        }
    }

    /**
     * Get   information about a given block by block hash. Otionally with the list of its transactions.
     * wa://api/blockChain/block?includeTransactions={includeTransactions}
     */
    public function getBlockByHash($blockHash, $includeTransactions = false)
    {
        $this->curl->makeGet($this->aelfClientUrl . self::$WA_BLOCK . '?blockHash=' . $blockHash . '&includeTransactions=' . ($includeTransactions ? 'true' : 'false'));
        $success = $this->curl->exec();
        if ($success->hasError()) {
            //Fail
            var_dump($success->getError());
        } else {
            //Success
            return json_decode($success->getBody(), JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Get information of a block by specified height. Optional whether to include transaction
     * information. wa://api/blockChain/blockByHeight?includeTransactions={includeTransactions}
     */
    public function getBlockByHeight($blockHeight, $includeTransactions = false)
    {
        if ($blockHeight == 0) {
            throw new Exception("[20001]Not found");
        }
        $this->curl->makeGet($this->aelfClientUrl . self::$WA_BLOCKBYHEIGHT . '?blockHeight=' . $blockHeight . '&includeTransactions=' . ($includeTransactions ? 'true' : 'false'));
        $success = $this->curl->exec();
        if ($success->hasError()) {
            //Fail
            var_dump($success->getError());
        } else {
            //Success
            return json_decode($success->getBody(), JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Get the current status of the block chain. wa:/api/blockChain/chainStatus
     */
    public function getChainStatus()
    {
        $this->curl->makeGet($this->aelfClientUrl . self::$WA_GETCHAINSTATUS);
        $success = $this->curl->exec();
        if ($success->hasError()) {
            //Fail
            var_dump($success->getError());
        } else {
            //Success
            return json_decode($success->getBody(), JSON_UNESCAPED_UNICODE);

        }
    }

    /**
     * Get the protobuf definitions related to a contract /api/blockChain/contractFileDescriptorSet.
     */
    public function getContractFileDescriptorSet($address)
    {
        $this->curl->makeGet($this->aelfClientUrl . self::$WA_GETCFCRIPTORSET . "?address=" . $address);
        $success = $this->curl->exec();
        if ($success->hasError()) {
            //Fail
            var_dump($success->getError());
        } else {
            //Success
            return $success->getBody();
        }

    }

    /**
     * Gets the status information of the task queue wa:/api/blockChain/taskQueueStatus.
     */
    public function getTaskQueueStatus()
    {
        $this->curl->makeGet($this->aelfClientUrl . self::$WA_GETTASKQUEUESTATUS);
        $success = $this->curl->exec();
        if ($success->hasError()) {
            //Fail
            var_dump($success->getError());
        } else {
            //Success
            return json_decode($success->getBody(), JSON_UNESCAPED_UNICODE);

        }

    }

    /**
     * Gets information about the current transaction pool.wa:/api/blockChain/transactionPoolStatus
     */
    public function getTransactionPoolStatus()
    {
        $this->curl->makeGet($this->aelfClientUrl . self::$WA_GETTRANSACTIONPOOLSTATUS);
        $success = $this->curl->exec();
        if ($success->hasError()) {
            //Fail
            var_dump($success->getError());
        } else {
            //Success
            return json_decode($success->getBody(), JSON_UNESCAPED_UNICODE);
        }

    }

    /**
     * Call a read-only method of a contract. wa:/api/blockChain/executeTransaction
     */
    public function executeTransaction($input)
    {
        $url = $this->aelfClientUrl . self::$WA_EXECUTETRANSACTION;
        $this->curl->makePost($url, json_encode($input), array('Content-type: application/json;charset=UTF-8'));
        $response = $this->curl->exec();
        if ($response->hasError()) {
            //Fail
            var_dump($response->getError());
        } else {
            //Success
            return $response->getBody();
        }

    }

    /**
     * Creates an unsigned serialized transaction wa:/api/blockChain/rawTransaction.
     */
    public function createRawTransaction($input)
    {
        $url = $this->aelfClientUrl . self::$WA_CREATERAWTRANSACTION;
        $this->curl->makePost($url, json_encode($input), array('Content-type: application/json;charset=UTF-8'));
        $response = $this->curl->exec();
        if ($response->hasError()) {
            //Fail
            var_dump($response->getError());
        } else {
            //Success
            return json_decode($response->getBody(), JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Call a method of a contract by given serialized str wa:/api/blockChain/executeRawTransaction.
     */
    public function executeRawTransaction($input)
    {
        $url = $this->aelfClientUrl . self::$WA_EXECUTERAWTRANSACTION;
        $this->curl->makePost($url, json_encode($input), array('Content-type: application/json;charset=UTF-8'));
        $response = $this->curl->exec();
        if ($response->hasError()) {
            //Fail
            var_dump($response->getError());
        } else {
            //Success
            return json_decode($response->getBody(), JSON_UNESCAPED_UNICODE);
        }

    }

    /**
     * Broadcast a serialized transaction. wa:/api/blockChain/sendRawTransaction
     */
    public function sendRawTransaction($input)
    {
        $url = $this->aelfClientUrl . self::$WA_SENDRAWTRANSACTION;
        $this->curl->makePost($url, json_encode($input), array('Content-type: application/json;charset=UTF-8'));
        $response = $this->curl->exec();
        if ($response->hasError()) {
            //Fail
            var_dump($response->getError());
        } else {
            //Success
            return json_decode($response->getBody(), JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Broadcast a transaction wa:/api/blockChain/sendTransaction.
     */
    public function sendTransaction($input)
    {
        $url = $this->aelfClientUrl . self::$WA_SENDTRANSACTION;
        $this->curl->makePost($url, json_encode($input), array('Content-type: application/json;charset=UTF-8'));
        $response = $this->curl->exec();
        if ($response->hasError()) {
            //Fail
            var_dump($response->getError());
        } else {
            //Success
            return json_decode($response->getBody(), JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Broadcast volume transactions wa:/api/blockChain/sendTransactions.
     */
    public function sendTransactions($input)
    {
        $url = $this->aelfClientUrl . self::$WA_SENDTRANSACTIONS;

        $this->curl->makePost($url, json_encode($input), array('Content-type: application/json;charset=UTF-8'));
        $response = $this->curl->exec();
        if ($response->hasError()) {
            //Fail
            var_dump($response->getError());
        } else {
            //Success
            return json_decode($response->getBody(), JSON_UNESCAPED_UNICODE);
        }


    }

    /**
     * Get the current status of a transaction wa:/api/blockChain/transactionResult.
     */
    public function getTransactionResult($transactionId)
    {
        $options = [//The custom options of cURL
            CURLOPT_TIMEOUT => 0,
            CURLOPT_CONNECTTIMEOUT => 80000,
            CURLOPT_USERAGENT => 'Multi-cURL client v1.5.0',
        ];
        $c = new Curl(null, $options);
        $c->makeGet($this->aelfClientUrl . self::$WA_GETTRANSACTIONRESULT . "?transactionId=" . $transactionId);
        $response = $c->exec();
        if ($response->hasError()) {
            //Fail
            var_dump($response->getError());
        } else {
            //Success
            //

            return json_decode($response->getBody(), JSON_UNESCAPED_UNICODE);
            //var_dump($response->getBody());
        }
    }

    /**
     * Get multiple transaction results. wa:/api/blockChain/transactionResults
     */
    public function getTransactionResults($blockHash, $offset = 0, $limit = 10)
    {
        if ($offset < 0) {
            echo "Error.InvalidOffset";
            exit();
        }
        if ($limit <= 0 || $limit > 100) {
            echo "Error.InvalidLimit";
            exit();
        }
        $this->curl->makeGet($this->aelfClientUrl . self::$WA_GETTRANSACTIONRESULTS . "?blockHash=" . $blockHash . '&offset=' . $offset . "&limit=" . $limit);
        $success = $this->curl->exec();
        if ($success->hasError()) {
            //Fail
            var_dump($success->getError());
        } else {
            //Success
            return json_decode($success->getBody(), JSON_UNESCAPED_UNICODE);

        }

    }

    /**
     * Get merkle path of a transaction. wa:/api/blockChain/merklePathByTransactionId
     */
    public function getMerklePathByTransactionId($transactionId)
    {

        $this->curl->makeGet($this->aelfClientUrl . self::$WA_GETMBYTRANSACTIONID . "?transactionId=" . $transactionId);
        $success = $this->curl->exec();
        if ($success->hasError()) {
            //Fail
            var_dump($success->getError());
        } else {
            //Success
            return json_decode($success->getBody(), JSON_UNESCAPED_UNICODE);
        }
    }


    /**
     * CalculateTransactionFee  wa:/api/blockChain/calculateTransactionFeeResult.
     */
    public function calculateTransactionFee($input)
    {
        $url = $this->aelfClientUrl . self::$WA_CALCULATETRANSACTIONFEERESULT;
        $this->curl->makePost($url, json_encode($input), array('Content-type: application/json;charset=UTF-8'));
        $response = $this->curl->exec();
        if ($response->hasError()) {
            //Fail
            var_dump($response->getError());
        } else {
            //Success
            return json_decode($response->getBody(), JSON_UNESCAPED_UNICODE);
        }
    }


    private function createBlockDto($block, $includeTransactions)
    {
        if ($block == null) {
            echo "not found";
            exit();
            throw new RuntimeException("not found");
        }
        return $block;
    }

    /**
     * Get id of the chain.
     */
    public function getChainId()
    {
        $chainStatusDto = $this->getChainStatus();
        $bytes = $this->base58->decode($chainStatusDto['ChainId']);
        for ($i = 0; $i <= 4; $i++) {
            if (strlen($bytes) <= $i) {
                $arr[] = 0;
            } else {
                $arr[] = ord($bytes[$i]);
            }

        }
        return Bytes::bytestointeger($arr, 0);
    }
}