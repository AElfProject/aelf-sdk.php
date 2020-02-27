<?php namespace control\aelf;

/***
 * 
 */
require "http/Curl.php";
require "http/MultiCurl.php";
require "http/Response.php";
use Hhxsv5\PhpMultiCurl\Curl as Curl;
use Hhxsv5\PhpMultiCurl\MultiCurl as MultiCurl;

Class Block{

    private $AElfClientUrl;
    private $version;
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
    public $Curl;
    /**
     * Object construction through the url path.
     */
    public function __construct($url,$version='') {
      
        $this->AElfClientUrl = $url;
        $this->version = $version;
        $this->Curl = new Curl();
    }


    /**
    * Get the height of the current chain. wa:/api/blockChain/blockHeight
    */
    public function getBlockHeight(){
        
        $Success = $this->Curl->makeGet($this->AElfClientUrl.self::$WA_BLOCKHEIGHT)->exec();
        if ($Success->hasError()) {
            //Fail
            var_dump($Success->getError());
        } else {
            //Success
            return $Success->getBody();
        }
    }

    /**
     * Get  information about a given block by block hash. Otionally with the list of its transactions.
     * wa://api/blockChain/block?includeTransactions={includeTransactions}
     */
    public function getBlockByHash($blockHash,$includeTransactions=false){
        $Success = $this->Curl->makeGet($this->AElfClientUrl.self::$WA_BLOCK.'?blockHash='.$blockHash.'*includeTransactions='.$includeTransactions)->exec();
        if ($Success->hasError()) {
            //Fail
            var_dump($Success->getError());
        } else {
            //Success
            var_dump($Success->getBody());
        }
    }

    /**
     * Get information of a block by specified height. Optional whether to include transaction
     * information. wa://api/blockChain/blockByHeight?includeTransactions={includeTransactions}
    */
    public function getBlockByHeight($blockHeight,$includeTransactions = false){
        if ($blockHeight == 0) {
            throw new Exception("[20001]Not found");
        }
        $Success = $this->Curl->makeGet($this->AElfClientUrl.self::$WA_BLOCKBYHEIGHT.'?blockHeight='.$blockHeight.'*includeTransactions='.$includeTransactions)->exec();
        if ($Success->hasError()) {
            //Fail
            var_dump($Success->getError());
        } else {
            //Success
            var_dump($Success->getBody());
        }
    }

    /**
     * Get the current status of the block chain. wa:/api/blockChain/chainStatus
     */
    public function getChainStatus(){
        $Success = $this->Curl->makeGet($this->AElfClientUrl.self::$WA_GETCHAINSTATUS)->exec();
        if ($Success->hasError()) {
            //Fail
            var_dump($Success->getError());
        } else {
            //Success
            var_dump($Success->getBody());
        }
    }

    /**
     * Get the protobuf definitions related to a contract /api/blockChain/contractFileDescriptorSet.
     */
    public function getContractFilCeDescriptorSet($address){
        $Success = $this->Curl->makeGet($this->AElfClientUrl.self::$WA_GETCFCRIPTORSET."?address=".$address)->exec();
        if ($Success->hasError()) {
            //Fail
            var_dump($Success->getError());
        } else {
            //Success
            var_dump($Success->getBody());
        }

    }

    /**
     * Gets the status information of the task queue wa:/api/blockChain/taskQueueStatus.
     */
    public function getTaskQueueStatus(){
        $Success = $this->Curl->makeGet($this->AElfClientUrl.self::$WA_GETTASKQUEUESTATUS)->exec();
        if ($Success->hasError()) {
            //Fail
            var_dump($Success->getError());
        } else {
            //Success
            var_dump($Success->getBody());
        }
    }

    /**
     * Gets information about the current transaction pool.wa:/api/blockChain/transactionPoolStatus
     */
    public function getTransactionPoolStatus(){
        $Success = $this->Curl->makeGet($this->AElfClientUrl.self::$WA_GETTRANSACTIONPOOLSTATUS)->exec();
        if ($Success->hasError()) {
            //Fail
            var_dump($Success->getError());
        } else {
            //Success
            var_dump($Success->getBody());
        }
    }

    /**
     * Call a read-only method of a contract. wa:/api/blockChain/executeTransaction
     */
    public function executeTransaction($input){
        $url = $this->AElfClientUrl.self::$WA_EXECUTETRANSACTION;
        $response = $this->Curl->makePost($url,$input)->exec();
        if ($response->hasError()) {
            //Fail
            var_dump($response->getError());
        } else {
            //Success
            var_dump($response->getBody());
        }
    }

    /**
     * Creates an unsigned serialized transaction wa:/api/blockChain/rawTransaction.
     */
    public function createRawTransaction($input) {
        $url = $this->AElfClientUrl.self::$WA_CREATERAWTRANSACTION;
        $response = $this->Curl->makePost($url,$input)->exec();
        if ($response->hasError()) {
            //Fail
            var_dump($response->getError());
        } else {
            //Success
            var_dump($response->getBody());
        }
    }

    /**
     * Call a method of a contract by given serialized str wa:/api/blockChain/executeRawTransaction.
     */
    public function executeRawTransaction($input){
        $url = $this->AElfClientUrl.self::$WA_EXECUTERAWTRANSACTION;
        $response = $this->Curl->makePost($url,$input)->exec();
        if ($response->hasError()) {
            //Fail
            var_dump($response->getError());
        } else {
            //Success
            var_dump($response->getBody());
        }
    }

    /**
     * Broadcast a serialized transaction. wa:/api/blockChain/sendRawTransaction
     */
    public function sendRawTransaction($input){
        $url = $this->AElfClientUrl.self::$WA_SENDRAWTRANSACTION;
        $response = $this->Curl->makePost($url,$input)->exec();
        if ($response->hasError()) {
            //Fail
            var_dump($response->getError());
        } else {
            //Success
            var_dump($response->getBody());
        }
    }


    /**
     * Broadcast a transaction wa:/api/blockChain/sendTransaction.
     */
    public function sendTransaction($input){
        $url = $this->AElfClientUrl.self::$WA_SENDTRANSACTION;
        $response = $this->Curl->makePost($url,$input)->exec();
        if ($response->hasError()) {
            //Fail
            var_dump($response->getError());
        } else {
            //Success
            var_dump($response->getBody());
        }
    }

    /**
     * Broadcast volume transactions wa:/api/blockChain/sendTransactions.
     */
    public function sendTransactions($input){
        $url = $this->AElfClientUrl.self::$WA_SENDTRANSACTIONS;
        $response = $this->Curl->makePost($url,$input)->exec();
        if ($response->hasError()) {
            //Fail
            var_dump($response->getError());
        } else {
            //Success
            var_dump($response->getBody());
        }
    
    }

    /**
     * Get the current status of a transaction wa:/api/blockChain/transactionResult.
     */
    public function getTransactionResult($transactionId){
        $Success = $this->Curl->makeGet($this->AElfClientUrl.self::$WA_GETTRANSACTIONRESULT."?transactionId=".$transactionId)->exec();
        if ($Success->hasError()) {
            //Fail
            var_dump($Success->getError());
        } else {
            //Success
            var_dump($Success->getBody());
        }
    }

    /**
     * Get multiple transaction results. wa:/api/blockChain/transactionResults
     */
    public function getTransactionResults($blockHash,$offset = 0,$limit = 10) {
        if ($offset < 0) {
            echo "Error.InvalidOffset";
            exit();
        }
        if ($limit <= 0 || $limit > 100) {
            echo "Error.InvalidLimit";
            exit();
        }
        $Success = $this->Curl->makeGet($this->AElfClientUrl.self::$WA_GETTRANSACTIONRESULTS."?blockHash=".$blockHash.'&offset='.$offset."&limit=".$limit)->exec();
        if ($Success->hasError()) {
            //Fail
            var_dump($Success->getError());
        } else {
            //Success
            var_dump($Success->getBody());
        }
    
    }

    /**
     * Get merkle path of a transaction. wa:/api/blockChain/merklePathByTransactionId
     */
    public function  getMerklePathByTransactionId($transactionId) {
        $Success = $this->Curl->makeGet($this->AElfClientUrl.self::$WA_GETMBYTRANSACTIONID."?transactionId=".$transactionId)->exec();
        if ($Success->hasError()) {
            //Fail
            var_dump($Success->getError());
        } else {
            //Success
            var_dump($Success->getBody());
        }
    }


    private function createBlockDto($block,$includeTransactions){
        if ($block == null) {
            echo  "not found";
            exit();
        throw new RuntimeException("not found");
        }
        return $block;
    }

    /**
     * Get id of the chain.
     */
    public function getChainId(){
     /*   $chainStatusDto = this.getChainStatus();
        String base58ChainId = chainStatusDto.getChainId();
        byte[] bytes = Base58.decode(base58ChainId);
        if (bytes.length < 4) {
        byte[] bs = new byte[4];
        for (int i = 0; i < 4; i++) {
            bs[i] = 0;
            if (bytes.length > (i)) {
            bs[i] = bytes[i];
            }
        }
        bytes = bs;
        }
        return BitConverter.toInt(bytes, 0);*/
    }
}