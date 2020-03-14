<?php

/***
 * 
 */
use Hhxsv5\PhpMultiCurl\Curl as Curl;
use Hhxsv5\PhpMultiCurl\MultiCurl as MultiCurl;

Class NetSdk{

    private $AElfClientUrl;
    private $version;
    private static $WA_ADDPEER = "/api/net/peer";
    private static $WA_REMOVEPEER = "/api/net/peer";
    private static $WA_GETPEERS = "/api/net/peers";
    private static $WA_GETNETWORKINFO = "/api/net/networkInfo";
    public $Curl;
    /**
     * Object construction through the url path.
     */
    public function __construct($url,$version='') {
      
        $this->AElfClientUrl = $url;
        $this->version = $version;
        $this->Curl = new Curl();
        $this->post_request_header = ['Content-Type' => 'application/json;charset=UTF-8'.$version];
        $this->get_request_header = ['Accept'=>'application/json;charset=UTF-8'.$version];
    }


    /**
     * Attempts to add a node to the connected network nodes wa:/api/net/peer.
     */
    public function addPeer($input){
        $url = $this->AElfClientUrl.self::$WA_ADDPEER;
      
        $this->Curl->makePost($url,json_encode(['address'=>$input]),array('Content-type: application/json;charset=UTF-8'));
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
     * Attempts to remove a node from the connected network nodes wa:/api/net/peer.
     */
    public function removePeer($address){
         $url = $this->AElfClientUrl.self::$WA_REMOVEPEER;
  
        $this->Curl->makeDelete($url.'?address='.$address);
        
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
     * Gets information about the peer nodes of the current node.Optional whether to include metrics.
     * wa:/api/net/peers?withMetrics=false
     */
    public function getPeers($withMetrics){
     
        $this->Curl->makeGet($this->AElfClientUrl.self::$WA_GETPEERS."?withMetrics=".($withMetrics?'true':'false'));
       
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
     * Get information about the node’s connection to the network. wa:/api/net/networkInfo
     */
    public function getNetworkInfo(){
        $this->Curl->makeGet($this->AElfClientUrl.self::$WA_GETNETWORKINFO);
        $Success = $this->Curl->exec();
        if ($Success->hasError()) {
            //Fail
            var_dump($Success->getError());
        } else {
            //Success
            return json_decode($Success->getBody(),JSON_UNESCAPED_UNICODE);
            
        }
       

    }
}