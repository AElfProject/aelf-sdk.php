<?php
/**
 * 
 * @day2020022
 */
namespace control\aelf;

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
        
        $this->$url = $url;
        if($version != null){
            $this->$version = $version;
        }
 
        $this->getBlockChainSdkObj();
    }
    private function getBlockChainSdkObj(){
        
        if($this->$blcokChainSdk==NULL){
            
            $c = new Block();
            $c->index();
        }
    }
}
