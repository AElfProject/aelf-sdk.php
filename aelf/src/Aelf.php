<?php

require_once "../vendor/autoload.php";
require_once "BlockChainSdk.php";
require_once "NetSdk.php";
/**
 *
 * @day2020022
 */
require_once "Bytes.php";
require_once "Sha.php";
require_once "pro/Aelf/Protobuf/Generated/Address.php";
require_once "pro/Aelf/Protobuf/Generated/Transaction.php";
require_once "pro/Aelf/Protobuf/Generated/TransferInput.php";
require_once "pro/Aelf/Protobuf/Generated/StringInput.php";
require_once "pro/Aelf/Protobuf/Generated/Hash.php";
require_once "pro/GPBMetadata/Types.php";
require_once "pro/GPBMetadata/Timestamp.php";
use Aelf\Protobuf\Generated\Address;
use Aelf\Protobuf\Generated\Transaction;
use Aelf\Protobuf\Generated\TransferInput;
use Aelf\Protobuf\Generated\StringInput;
use Aelf\Protobuf\Generated\Hash;
use GPBMetadata\Types;
use BitcoinPHP\BitcoinECDSA\BitcoinECDSA;
use StephenHill\Base58;
use Aelf\Bytes\Bytes;
use kornrunner\Secp256k1;
use kornrunner\Serializer\HexSignatureSerializer;
/**
 * AELF
 */
class Aelf{

    public $url; //
    public $version;
    public $get_request_header;
    public $post_request_header; //
    public $blockChainSdk;
    public $netSdk;
    public $private_key;  //
    public $base58;
     /**
     * Object construction through the url path.
     *
     * @param url Http Request Url exp:(http://xxxx)
     * @param version application/json;v={version}
     */
    public function __construct($url,$version = null){
  

        $this->url= $url;

        if($version != null){
            $this-$version = $version;
        }
        $this->getBlockChainSdkObj();
        $this->getNetSdkObj();
        $this->base58 = new Base58();
    }
    
    public function getBlockChainSdkObj(){

        if($this->blockChainSdk==NULL){
          return $this->blockChainSdk = new BlockChainSdk($this->url,$this->version);
        }else{
            return $this->blockChainSdk;
        }
    }

    public function getNetSdkObj(){
        if($this->netSdk == NULL ){
            $this->netSdk = new NetSdk($this->url,$this->version);
        }else {
            return $this->netSdk;
        }
    }

    /**
     * Get the height of the current chain. wa:/api/blockChain/blockHeight
     */
    public function getBlockHeight(){

        return $this->getBlockChainSdkObj()->getBlockHeight();
    }


    /**
     * Get information about a given block by block hash. Otionally with the list of its transactions.
     * wa://api/blockChain/block?includeTransactions={includeTransactions}
     */
    public function getBlockByHash($blockHash,$includeTransactions = false){
        return $this->getBlockChainSdkObj()->getBlockByHash($blockHash, $includeTransactions);
    }

    /**
     * Get information of a block by specified height. Optional whether to include transaction
     * information. wa://api/blockChain/blockByHeight?includeTransactions={includeTransactions}
     */
    public function getBlockByHeight($blockHeight,$includeTransactions = false){
        //$includeTransactions The default is false
        return $this->getBlockChainSdkObj()->getBlockByHeight($blockHeight, $includeTransactions);
    }

    /**
     * Get the current status of the block chain. wa:/api/blockChain/chainStatus
     */
    public function getChainStatus(){
        return $this->getBlockChainSdkObj()->getChainStatus();
    }

    /**
     * Get the protobuf definitions related to a contract /api/blockChain/contractFileDescriptorSet.
     */
    public function getContractFilCeDescriptorSet($address){
        return $this->getBlockChainSdkObj()->getContractFilCeDescriptorSet($address);
    }

    /**
     * Gets the status information of the task queue wa:/api/blockChain/taskQueueStatus.
     */
    public function getTaskQueueStatus(){
        return $this->getBlockChainSdkObj()->getTaskQueueStatus();
    }

    /**
     * Gets information about the current transaction pool.wa:/api/blockChain/transactionPoolStatus
     */
    public function getTransactionPoolStatus(){
        return $this->getBlockChainSdkObj()->getTransactionPoolStatus();
    }

    /**
     * Call a read-only method of a contract. wa:/api/blockChain/executeTransaction
     */
    public function executeTransaction($input){
        return $this->getBlockChainSdkObj()->executeTransaction($input);
    }

    /**
     * Creates an unsigned serialized transaction wa:/api/blockChain/rawTransaction.
     */
    public function createRawTransaction($input)
       {
        return $this->getBlockChainSdkObj()->createRawTransaction($input);
    }

    /**
     * Call a method of a contract by given serialized str wa:/api/blockChain/executeRawTransaction.
     */
    public function executeRawTransaction($input){
        return $this->getBlockChainSdkObj()->executeRawTransaction($input);
    }

    /**
     * Broadcast a serialized transaction. wa:/api/blockChain/sendRawTransaction
     */
    public function sendRawTransaction($input){
        return $this->getBlockChainSdkObj()->sendRawTransaction($input);
    }

    /**
     * Broadcast a transaction wa:/api/blockChain/sendTransaction.
     */
    public function sendTransaction($input){
        return $this->getBlockChainSdkObj()->sendTransaction($input);
    }

    /**
     * Broadcast volume transactions wa:/api/blockChain/sendTransactions.
     */
    public function sendTransactions($input){
        return $this->getBlockChainSdkObj()->sendTransactions($input);
    }

    /**
     * Get the current status of a transaction wa:/api/blockChain/transactionResult.
     */
    public function getTransactionResult($transactionId){
        return $this->getBlockChainSdkObj()->getTransactionResult($transactionId);
    }


    /**
     * Get multiple transaction results. wa:/api/blockChain/transactionResults
     */
    public function getTransactionResults($blockHash,$offset = 0,$limit = 1)
    {
        return $this->getBlockChainSdkObj()->getTransactionResults($blockHash,$offset,$limit);
    }

    /**
     * Get merkle path of a transaction. wa:/api/blockChain/merklePathByTransactionId
     */
    public function getMerklePathByTransactionId($transactionId) {
        return $this->getBlockChainSdkObj()->getMerklePathByTransactionId($transactionId);
    }

    /**
     * Get id of the chain.
     */
    public function getChainId(){
        return $this->getBlockChainSdkObj()->getChainId();
    }

    /**
     * Attempts to add a node to the connected network nodes wa:/api/net/peer.
     */
    public function addPeer($input){
        return $this->getNetSdkObj()->addPeer($input);
    }

    /**
     * Attempts to remove a node from the connected network nodes wa:/api/net/peer.
     */
    public function removePeer($address){
        return $this->getNetSdkObj()->removePeer($address);
    }

    /**
     * Gets information about the peer nodes of the current node.Optional whether to include metrics.
     * wa:/api/net/peers?withMetrics=false
     */     
    public function getPeers($withMetrics){
        return $this->getNetSdkObj()->getPeers($withMetrics);
    }

    /**
     * Get information about the node’s connection to the network. wa:/api/net/networkInfo
     */
    public function getNetworkInfo(){
        return $this->getNetSdkObj()->getNetworkInfo();
    }

    /**
     * Build a transaction from the input parameters.
     */
    public function generateTransaction($from,$to,$methodName,$params){
        $chainStatus = $this->getBlockChainSdkObj()->getChainStatus();
        $Bit = new BitcoinECDSA();
        $from = $Bit->decodeChecked($from);
        $to = $Bit->decodeChecked($to);
        $transaction = new Transaction();
        $Faddress = new Address();
        $Taddress = new Address();
        $Faddress->setValue($from);
        $Taddress->setValue($to);
        $transaction->setFromAddress($Faddress);
        $transaction->setToAddress($Taddress);
        $transaction->setMethodName($methodName);
        $Hash = new Hash();
        $Hash->setValue($params);
        $transaction->setParams($Hash->serializeToString());
        $transaction->setRefBlockNumber($chainStatus['BestChainHeight']);
        $transaction->setRefBlockPrefix(substr(hex2bin($chainStatus['BestChainHash']),0,4));
        return $transaction;
        
    }

    /**
     * Sign a transaction using private key.
     */
    public function signTransaction($privateKeyHex,$transaction)
    {
        $transactionData = sha256($transaction->serializeToString());
        return $this->getSignatureWithPrivateKey($privateKeyHex,$transactionData);
    }

    /**
     * Get the address of genesis contract.
     *
     * @return address
     */
    public function getGenesisContractAddress(){
        $chainstatusDto = $this->getBlockChainSdkObj()->getChainStatus();
        return $chainstatusDto['GenesisContractAddress'];
    }

    /**
     * Get the account address through the public key.
     *
     * @param pubKey pubKey hex
     * @return Str
     */
    public function getAddressFromPubKey($pubKey = null) {
        $aelfkey = new BitcoinECDSA();
        $address = $aelfkey->hash256(hex2bin($pubKey));
        //checksum
        $address = $address.substr($aelfkey->hash256(hex2bin($address)), 0, 8);
        $address = $aelfkey->base58_encode($address);
        return $address;
    }
    /**
     * Convert the Address to the displayed string：symbol_base58-string_base58-string-chain-id.
     */
    public function getFormattedAddress($privateKey,$address){
        $chainIdString = $this->getBlockChainSdkObj()->getChainStatus()['ChainId'];
        $fromAddress = $this->getAddressFromPrivateKey($privateKey);
        
        $toAddress = $this->getContractAddressByName($privateKey,hex2bin(sha256('AElf.ContractNames.Token')));
     
        $methodName = "GetPrimaryTokenSymbol";
        $bytes = '';
        $transaction = $this->generateTransaction($fromAddress,$toAddress,$methodName,$bytes);
        $signature = $this->signTransaction($privateKey,$transaction);
        $transaction->setSignature(hex2bin($signature));
        $executeTransactionDto = ['RawTransaction'=>bin2hex($transaction->serializeToString())];
        $response = $this->getBlockChainSdkObj()->executeTransaction($executeTransactionDto);
        $symbol = new StringInput();
        $symbol->mergeFromString(hex2bin($response));
        $symbolstr = $symbol->getStringValue();
        return $symbolstr."_". $address. "_" .$chainIdString;
    }

    /**
     * new generateKeyPairInfo;
     */
    public function generateKeyPairInfo()
    {
        $keyPair = new BitcoinECDSA();
        $keyPair->generateRandomPrivateKey();
        $privateKey = $keyPair->getPrivateKey();
        $publicKey = $keyPair->getUncompressedPubKey();
        $address = $keyPair->getUncompressedAddress();
        $keyPairInfo=array('privateKey'=>$privateKey,'publicKey'=>$publicKey,'address'=>$address);
        return $keyPairInfo;
    }

    /**
     * Get address of a contract by given contractNameHash.
     */
    public function getContractAddressByName($privateKey,$contractNameHash)
    {
           $from = $this->getAddressFromPrivateKey($privateKey);
           $to = $this->getGenesisContractAddress();
           $methodName = 'GetContractAddressByName';

           $transaction = $this->generateTransaction($from,$to,$methodName,$contractNameHash);
            
           $signature = $this->signTransaction($privateKey, $transaction);
    
           $transaction->setSignature(hex2bin($signature));
      
           $executeTransactionDto = ['RawTransaction'=>bin2hex($transaction->serializeToString())];

           $response = $this->getBlockChainSdkObj()->executeTransaction($executeTransactionDto);
           $address = new Address();
           $address->mergeFromString(hex2bin($response));
        
           $base58Str = $this->base58->encodeChecked($address->getValue());
          
          return $base58Str;
    }

    /**
     * Get address of a contract by given contractNameHash.
     */
    public function getAddressFromPrivateKey($privateKey) {
        $aelfkey = new BitcoinECDSA();
        $aelfkey->setPrivateKey($privateKey);
        $address = $aelfkey->getUncompressedAddress();
        return $address;
    }

    /**
     * Get the private sha256 signature.
     */
    public function getSignatureWithPrivateKey($privateKey,$txData){
        $secp256k1 = new Secp256k1();
     
        $signature = $secp256k1->sign($txData, $privateKey);
        // get r
        $r = $signature->getR();
        // get s
        $s = $signature->getS();
        // get recovery param
        $v = $signature->getRecoveryParam();
        // encode to hex
        $serializer = new HexSignatureSerializer();
        $signatureString = $serializer->serialize($signature);
        
        // or you can call toHex
        $signatureString = $signature->toHex();
       
        if(strlen((string)$v)==1){
            $v = "0".$v;
        }
        return $signatureString.$v;
    }

    /**
     * Verify whether $this sdk successfully connects the chain.
     *
     * @return IsConnected or not
     */
    public function isConnected() {
      
        $this->getBlockChainSdkObj()->getChainStatus();
        try {
            return true;
    
         } catch (Exception $e){
             return false;

         }
    }


}
