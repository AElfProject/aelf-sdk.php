<?php

use PHPUnit\Framework\TestCase;
use BitcoinPHP\BitcoinECDSA\BitcoinECDSA;
use Aelf\Protobuf\Generated\Address;
use GPBMetadata\Types;
use StephenHill\Base58;
class AelfTest extends TestCase
{
    public $Aelf;
    public $private_key;
    public $public_key;
    public $address;
    public $OPREATIONADDRESS;

    public function setUp() {
        $url = 'http://18.223.158.83:8000';

        $this->Aelf = new Aelf($url);
        $this->OPREATIONADDRESS ='18.223.158.83:7003';

        $bitcoinECDSA = new BitcoinECDSA();
        $this->private_key = 'be3abe5c1439899ac2efd0001e15715fd989a3ae11f09e1cb95d320cd4993e2a';
        $bitcoinECDSA->setPrivateKey($this->private_key);
        $this->public_key = $bitcoinECDSA->getUncompressedPubKey();
        $this->address= $this->Aelf->getAddressFromPrivateKey($this->private_key);
         $this->base58 = new Base58();
    }


    public function testgetChainStatus(){
        $chain_status =$this->Aelf->getChainStatus();
        print_r($chain_status);
        $this->assertTrue($chain_status['BestChainHeight'] > 0);
        $chain_id = $this->Aelf->getChainId();
        print_r($chain_id);
        $this->assertTrue($chain_id == 9992731);

    }

    public function testBlockapi(){
        $blockHeight = $this->Aelf->getBlockHeight();
        print_r('# getBlockHeight');
        echo '<br>';
        print_r($blockHeight);
        $this->assertTrue($blockHeight > 0);
        $block = $this->Aelf->getBlockByHeight(1,true);

        $this->assertTrue($block['Header']['Height'] ==1 );

        $block2 = $this->Aelf->getBlockByHash($block['BlockHash'],false);


        $this->assertTrue($block2['Header']['Height'] == 1);
        return $block2['Header']['Height'];

    }

    public function testgetTransactionResultApi(){
        $block = $this->Aelf->getBlockByHeight(1,true);

        $transaction_result = $this->Aelf->getTransactionResult($block['Body']['Transactions'][0]);
        print_r('# get_transaction_result' );
        print_r($transaction_result);
        $this->assertTrue($transaction_result['Status'] == 'MINED');
        $transaction_results = $this->Aelf->getTransactionResults($block['BlockHash']);
        print_r('# get_transaction_results');
        print_r($transaction_results);
        $merkle_path = $this->Aelf->getMerklePathByTransactionId($block['Body']['Transactions'][0]);
        $this->assertTrue(is_array($merkle_path['MerklePathNodes']));
    }
    public function testexecuteTransaction(){
        $toAddress = $this->Aelf->getGenesisContractAddress();
        $methodName = "GetContractAddressByName";
        $bytes =hex2bin(hash('sha256','AElf.ContractNames.TokenConverter'));
        $transaction = $this->Aelf->generateTransaction($this->address, $toAddress, $methodName, $bytes);
    
        $signature = $this->Aelf->signTransaction($this->private_key, $transaction);

        $transaction->setSignature(hex2bin($signature));
       
        $executeTransactionDtoObj =['RawTransaction'=>bin2hex($transaction->serializeToString())];
        $response =  $this->Aelf->executeTransaction($executeTransactionDtoObj);
        $address = new Address();
        $address->mergeFromString(hex2bin($response));
        $base58Str = $this->base58->encodeChecked($address->getValue());
        $aa  = $this->Aelf->getContractAddressByName($this->private_key,$bytes);
        print_r($aa);

        print_r($base58Str);
    }
    public function testRawTransactionApi(){
        $status = $this->Aelf->getChainStatus();
        $Params = base64_encode(hex2bin(hash('sha256', 'AElf.ContractNames.Consensus')));
        $param = array('value'=>$Params);
        $transaction = [
            "from" =>$this->Aelf->getAddressFromPrivateKey($this->private_key),
            "to"=>$this->Aelf->getGenesisContractAddress(),
            "refBlockNumber"=>$status['BestChainHeight'],
            "refBlockHash"=> $status['BestChainHash'],
            "methodName"=> "GetContractAddressByName",
            "params"=> json_encode($param)
        ];
        $raw_transaction  = $this->Aelf->createRawTransaction($transaction);
        print_r($raw_transaction.'<br/>');
        $transactionId =hash('sha256',hex2bin($raw_transaction['RawTransaction']));

        $sign =  $this->Aelf->getSignatureWithPrivateKey($this->private_key,$transactionId);

        $transaction1 = array('RawTransaction'=>$raw_transaction['RawTransaction'],'signature'=>$sign);
        $execute = $this->Aelf->executeRawTransaction($transaction1);
        print_r($execute);
        $transaction2 = array('Transaction'=>$raw_transaction['RawTransaction'],'signature'=>$sign,'returnTransaction'=>true);
        $execute1 = $this->Aelf->sendRawTransaction($transaction2);
        print_r($execute1);
    }
    public function testgetAddressFromPubKeyTest(){
        //$pubKeyAddress = $this->Aelf->getAddressFromPubKey($this->public_key);
        $pubKeyAddress = $this->Aelf->getAddressFromPubKey('04166cf4be901dee1c21f3d97b9e4818f229bec72a5ecd56b5c4d6ce7abfc3c87e25c36fd279db721acf4258fb489b4a4406e6e6e467935d06990be9d134e5741c');
       
        print_r($pubKeyAddress);
        $this->assertTrue($pubKeyAddress == 'SD6BXDrKT2syNd1WehtPyRo3dPBiXqfGUj8UJym7YP9W9RynM');
    }

    public function testSendTransactionApi(){
        $currentHeight = $this->Aelf->getBlockHeight();
        $block = $this->Aelf->getBlockByHeight($currentHeight,False);
        $params = hex2bin(hash('sha256','AElf.ContractNames.Vote'));
        $transaction = $this->buildTransaction($this->Aelf->getGenesisContractAddress(),'GetContractAddressByName',$params);

        $result =  $this->Aelf->sendTransaction(bin2hex($transaction->serializeToString()));
        print_r($result);
        $this->assertTrue($result['TransactionId'] != "");
    }
    public function testsendTransactions() {
        $toAddress = $this->Aelf->getGenesisContractAddress();
        $param1 =hex2bin(hash('sha256','AElf.ContractNames.Token'));
        $param2 =hex2bin(hash('sha256','AElf.ContractNames.Vote'));
        $methodName = "GetContractAddressByName";
        $tmp  = [$param1,$param2];
        foreach($tmp as $k){
            $transactionObj = $this->buildTransaction($toAddress,$methodName,$k);
            $rawTransactions = bin2hex($transactionObj->serializeToString());
            $sendTransactionsInputs = ['RawTransactions'=>$rawTransactions];
            $listString = $this->Aelf->sendTransactions($sendTransactionsInputs);
            print_r($listString);
        }

    }
    public function testTxPoolApi(){
        $txPoolStatus = $this->Aelf->getTransactionPoolStatus();
        print_r('# get_transaction_pool_status:');
        print_r($txPoolStatus);
        $this->assertTrue($txPoolStatus['Queued'] >= 0);
    }

    public function testTaskQueueApi(){
        $task_queue_status = $this->Aelf->getTaskQueueStatus();
        print_r($task_queue_status);
        $this->assertTrue(count($task_queue_status) > 0);
    }

    public function testNetworkApi(){
        print('getNetworkInfo');
        print_r($this->Aelf->getNetworkInfo());
        print('remove_peer');
        print($this->Aelf->removePeer('18.223.158.83:7003'));
        print('add_peer');
        print_r($this->Aelf->addPeer($this->OPREATIONADDRESS));
        print_r($this->Aelf->getPeers(true));

    }

    public function testGetContractFilCeDescriptorSet(){
        $blockHeight = $this->Aelf->getBlockHeight();
        $this->assertTrue($blockHeight  > 0);
        $blockDto = $this->Aelf->getBlockByHeight($blockHeight, false);
        $transactionResultDtoList = $this->Aelf->getTransactionResults($blockDto['BlockHash'],0,10);

        foreach($transactionResultDtoList as $v){

            $this->Aelf->getContractFilCeDescriptorSet($v['Transaction']['To']);
        }

    }



    public function testHelpers(){
        $is_connected =$this->Aelf->isConnected();
        $this->assertTrue($is_connected);
    }

    public function testgetFormattedAddress(){
        $addressVal = $this->Aelf->getFormattedAddress($this->private_key, $this->address);
        $this->assertTrue(("ELF_".$this->address."_AELF")==$addressVal);
    }

    private function buildTransaction($toaddress,$methodName,$params){
        $transactionObj  = $this->Aelf->generateTransaction($this->address,$toaddress,$methodName,$params);

        $signature = $this->Aelf->signTransaction($this->private_key,$transactionObj);
        $transactionObj->setSignature(hex2bin($signature));

        return $transactionObj;
    }
}


?>