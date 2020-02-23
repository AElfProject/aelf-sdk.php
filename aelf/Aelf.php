<?php
namespace control\aelf;
require_once "BlockSdk.php";
/**
 * 
 * @day2020022
 */

/**
 * AELF
 */
class Aelf{

    public $url; //
    public $version;
    public $get_request_header;
    public $post_request_header; //
    public $blcokChainSdk;
    public $private_key;  //

    public function __construct($url,$version = null){
  

        $this->url= $url;

        if($version != null){
            $this-$version = $version;
        }
        $this->getBlockChainSdkObj();
    }
    public function getBlockChainSdkObj(){
    
        if($this->$blcokChainSdk==NULL){
           
            $c = new Block($this->url);
            $c->getBlockHeight();
        }
    }
}
$k = new Aelf('http://52.90.147.175:8000');
