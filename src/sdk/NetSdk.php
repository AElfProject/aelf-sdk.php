<?php
namespace AElf\Api;
/***
 * 
 */
use Hhxsv5\PhpMultiCurl\Curl as Curl;
use Hhxsv5\PhpMultiCurl\MultiCurl as MultiCurl;

Class NetSdk{

    private $aelfClientUrl;
    private $version;
    private static $WA_ADDPEER = "/api/net/peer";
    private static $WA_REMOVEPEER = "/api/net/peer";
    private static $WA_GETPEERS = "/api/net/peers";
    private static $WA_GETNETWORKINFO = "/api/net/networkInfo";
    private $curl;
    /**
     * Object construction through the url path.
     */
    public function __construct($url,$version='') {
      
        $this->aelfClientUrl = $url;
        $this->version = $version;
        $this->curl = new Curl();
        $this->postRequestHeader = ['Content-Type' => 'application/json;charset=UTF-8'.$version];
        $this->getRequestHeader = ['Accept'=>'application/json;charset=UTF-8'.$version];
    }


    /**
     * Attempts to add a node to the connected network nodes wa:/api/net/peer.
     */
    public function addPeer($input){
        $url = $this->aelfClientUrl.self::$WA_ADDPEER;
      
        $this->curl->makePost($url,json_encode(['address'=>$input]),array('Content-type: application/json;charset=UTF-8'));
        $response = $this->curl->exec();
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
        $url = $this->aelfClientUrl.self::$WA_REMOVEPEER;
        $response = send_request($url.'?address='.$address,'DELETE');
      
        if ($response['httpCode']==200) {
            //Fail
            return $response['data'];
        } else {
            //Success
          
            return $response['data'];
        }
       
       
    }

    /**
     * Gets information about the peer nodes of the current node.Optional whether to include metrics.
     * wa:/api/net/peers?withMetrics=false
     */
    public function getPeers($withMetrics){
     
        $this->curl->makeGet($this->aelfClientUrl.self::$WA_GETPEERS."?withMetrics=".($withMetrics?'true':'false'));
       
        $success = $this->curl->exec();
        if ($success->hasError()) {
            //Fail
            var_dump($success->getError());
        } else {
            //Success
            
            return json_decode($success->getBody(),JSON_UNESCAPED_UNICODE);
            
        }
       
    }

    /**
     * Get information about the node’s connection to the network. wa:/api/net/networkInfo
     */
    public function getNetworkInfo(){
        $this->curl->makeGet($this->aelfClientUrl.self::$WA_GETNETWORKINFO);
        $success = $this->curl->exec();
        if ($success->hasError()) {
            //Fail
            var_dump($success->getError());
        } else {
            //Success
            return json_decode($success->getBody(),JSON_UNESCAPED_UNICODE);
            
        }
       

    }
}