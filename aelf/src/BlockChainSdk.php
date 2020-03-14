<?php

/***
 * 
 */

use Hhxsv5\PhpMultiCurl\Curl as Curl;
use Hhxsv5\PhpMultiCurl\MultiCurl as MultiCurl;
use StephenHill\Base58;
use Aelf\Bytes\Bytes;
Class BlockChainSdk{

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
    public $base58;
    /**
     * Object construction through the url path.
     */
    public function __construct($url,$version='') {
      
        $this->AElfClientUrl = $url;
        $this->version = $version;
        $options = [//The custom options of cURL
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_USERAGENT      => 'Multi-cURL client v1.5.0',
        ];
        $this->Curl = new Curl(null, $options);
        $this->base58 = new Base58();
    }


    /**
    * Get the height of the current chain. wa:/api/blockChain/blockHeight
    */
    public function getBlockHeight(){
        
        $this->Curl->makeGet($this->AElfClientUrl.self::$WA_BLOCKHEIGHT);
        $Success = $this->Curl->exec();
        if ($Success->hasError()) {
            //Fail
            var_dump($Success->getError());
        } else {
            //Success
            
            return $Success->getBody();
        }
    }

    /**
     * Get   information about a given block by block hash. Otionally with the list of its transactions.
     * wa://api/blockChain/block?includeTransactions={includeTransactions}
     */
    public function getBlockByHash($blockHash,$includeTransactions=false){
        $this->Curl->makeGet($this->AElfClientUrl.self::$WA_BLOCK.'?blockHash='.$blockHash.'&includeTransactions='.($includeTransactions?'true':'false'));
        $Success = $this->Curl->exec();
        if ($Success->hasError()) {
            //Fail
            var_dump($Success->getError());
        } else {
            //Success
          //  var_dump($Success->getBody());
            return json_decode($Success->getBody(),JSON_UNESCAPED_UNICODE);
          //  return $Success->getBody();
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
       
        $this->Curl->makeGet($this->AElfClientUrl.self::$WA_BLOCKBYHEIGHT.'?blockHeight='.$blockHeight.'&includeTransactions='.($includeTransactions?'true':'false'));
        $Success = $this->Curl->exec();
        if ($Success->hasError()) {
            //Fail
            var_dump($Success->getError());
        } else {
            //Success
            return json_decode($Success->getBody(),JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Get the current status of the block chain. wa:/api/blockChain/chainStatus
     */
    public function getChainStatus(){
        $this->Curl->makeGet($this->AElfClientUrl.self::$WA_GETCHAINSTATUS);
        $Success = $this->Curl->exec();
        if ($Success->hasError()) {
            //Fail
            var_dump($Success->getError());
        } else {
            //Success
            return json_decode($Success->getBody(),JSON_UNESCAPED_UNICODE);
            
        }
    }

    /**
     * Get the protobuf definitions related to a contract /api/blockChain/contractFileDescriptorSet.
     */
    public function getContractFilCeDescriptorSet($address){

        $this->Curl->makeGet($this->AElfClientUrl.self::$WA_GETCFCRIPTORSET."?address=".$address);
        $Success = $this->Curl->exec();
        if ($Success->hasError()) {
            //Fail
            var_dump($Success->getError());
        } else {
            //Success
       
            return $Success->getBody();
        }

    }

    /**
     * Gets the status information of the task queue wa:/api/blockChain/taskQueueStatus.
     */
    public function getTaskQueueStatus(){
        $this->Curl->makeGet($this->AElfClientUrl.self::$WA_GETTASKQUEUESTATUS);
        $Success = $this->Curl->exec();
        if ($Success->hasError()) {
            //Fail
            var_dump($Success->getError());
        } else {
            //Success
            return json_decode($Success->getBody(),JSON_UNESCAPED_UNICODE);
            
        }
       
    }

    /**
     * Gets information about the current transaction pool.wa:/api/blockChain/transactionPoolStatus
     */
    public function getTransactionPoolStatus(){
        $this->Curl->makeGet($this->AElfClientUrl.self::$WA_GETTRANSACTIONPOOLSTATUS);
        $Success = $this->Curl->exec();
        if ($Success->hasError()) {
            //Fail
            var_dump($Success->getError());
        } else {
            //Success
            return json_decode($Success->getBody(),JSON_UNESCAPED_UNICODE);
            
        }
       
    }

    /**
     * Call a read-only method of a contract. wa:/api/blockChain/executeTransaction
     */
    public function executeTransaction($input){
        $url = $this->AElfClientUrl.self::$WA_EXECUTETRANSACTION;
  
        $this->Curl->makePost($url,json_encode($input),array('Content-type: application/json;charset=UTF-8'));
       
        $response = $this->Curl->exec();
        if ($response->hasError()) {
            //Fail
            var_dump($response->getError());
        } else {
            //Success
          
            return $response->getBody();
        }
        
    }
    function http_post($sUrl, $aHeader, $aData){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $sUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($aData));
        $sResult = curl_exec($ch);
        if($sError=curl_error($ch)){
            die($sError);
        }
        curl_close($ch);
        return $sResult;
    }
    /**
     * Creates an unsigned serialized transaction wa:/api/blockChain/rawTransaction.
     */
    public function createRawTransaction($input) {
        $url = $this->AElfClientUrl.self::$WA_CREATERAWTRANSACTION;
        $this->Curl->makePost($url,json_encode($input),array('Content-type: application/json;charset=UTF-8'));
        $response = $this->Curl->exec();
        if ($response->hasError()) {
            //Fail
            var_dump($response->getError());
        } else {
            //Success
            return json_decode($response->getBody(),JSON_UNESCAPED_UNICODE);
        }

       
    }

    /**
     * Call a method of a contract by given serialized str wa:/api/blockChain/executeRawTransaction.
     */
    public function executeRawTransaction($input){
        $url = $this->AElfClientUrl.self::$WA_EXECUTERAWTRANSACTION;
        $this->Curl->makePost($url,json_encode($input),array('Content-type: application/json;charset=UTF-8'));
        $response = $this->Curl->exec();
        if ($response->hasError()) {
            //Fail
            var_dump($response->getError());
        } else {
            //Success
            return json_decode($response->getBody(),JSON_UNESCAPED_UNICODE);
        }
       
    }

    /**
     * Broadcast a serialized transaction. wa:/api/blockChain/sendRawTransaction
     */
    public function sendRawTransaction($input){
        $url = $this->AElfClientUrl.self::$WA_SENDRAWTRANSACTION;
       
        $this->Curl->makePost($url,json_encode($input),array('Content-type: application/json;charset=UTF-8'));
        $response = $this->Curl->exec();
        if ($response->hasError()) {
            //Fail
            var_dump($response->getError());
        } else {
            //Success
            return json_decode($response->getBody(),JSON_UNESCAPED_UNICODE);
        }
    }


    /**
     * Broadcast a transaction wa:/api/blockChain/sendTransaction.
     */
    public function sendTransaction($input){
        $url = $this->AElfClientUrl.self::$WA_SENDTRANSACTION;
       
        $this->Curl->makePost($url,json_encode(['RawTransaction'=>$input]),array('Content-type: application/json;charset=UTF-8'));
        $response = $this->Curl->exec();
        if ($response->hasError()) {
            //Fail
            var_dump($response->getError());
        } else {
            //Success
            return json_decode($response->getBody(),JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Broadcast volume transactions wa:/api/blockChain/sendTransactions.
     */
    public function sendTransactions($input){
        $url = $this->AElfClientUrl.self::$WA_SENDTRANSACTIONS;
       
        $this->Curl->makePost($url,json_encode($input),array('Content-type: application/json;charset=UTF-8'));
        $response = $this->Curl->exec();
        if ($response->hasError()) {
            //Fail
            var_dump($response->getError());
        } else {
            //Success
            return json_decode($response->getBody(),JSON_UNESCAPED_UNICODE);
        }
   
    
    }

    /**
     * Get the current status of a transaction wa:/api/blockChain/transactionResult.
     */
    public function getTransactionResult($transactionId){
        $options = [//The custom options of cURL
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_CONNECTTIMEOUT => 80000,
            CURLOPT_USERAGENT      => 'Multi-cURL client v1.5.0',
        ];
        $c = new Curl(null, $options);
        $c->makeGet($this->AElfClientUrl.self::$WA_GETTRANSACTIONRESULT."?transactionId=".$transactionId);
        $response = $c->exec();
        if ($response->hasError()) {
            //Fail
            var_dump($response->getError());
        } else {
            //Success
            return json_decode($response->getBody(),JSON_UNESCAPED_UNICODE);
            //var_dump($response->getBody());
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
        $this->Curl->makeGet($this->AElfClientUrl.self::$WA_GETTRANSACTIONRESULTS."?blockHash=".$blockHash.'&offset='.$offset."&limit=".$limit);
        $Success = $this->Curl->exec();
        if ($Success->hasError()) {
            //Fail
            var_dump($Success->getError());
        } else {
            //Success
            return json_decode($Success->getBody(),JSON_UNESCAPED_UNICODE);
         
        }
    
    }

    /**
     * Get merkle path of a transaction. wa:/api/blockChain/merklePathByTransactionId
     */
    public function  getMerklePathByTransactionId($transactionId) {
  
        $this->Curl->makeGet(($this->AElfClientUrl.self::$WA_GETMBYTRANSACTIONID."?transactionId=".$transactionId);
        $Success = $this->Curl->exec();
        if ($Success->hasError()) {
            //Fail
            var_dump($Success->getError());
        } else {
            //Success
           return json_decode($Success->getBody(),JSON_UNESCAPED_UNICODE);
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
        $chainStatusDto = $this->getChainStatus();
        $bytes = $this->base58->decode($chainStatusDto['ChainId']);
        $bytes = Bytes::getBytes($bytes);
        return Bytes::bytestointeger($bytes, 0);
    }
}