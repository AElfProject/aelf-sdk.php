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
            var_dump($Success->getBody());
        }
    }

}