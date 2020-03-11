<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/3/11 0011
 * Time: 21:47
 */

use PHPUnit\Framework\TestCase;
use BitcoinPHP\BitcoinECDSA\BitcoinECDSA;
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
        print_r('# get_transaction_result', $transaction_result);
        $this->assertTrue($transaction_result['Status'] == 'MINED');
        $transaction_results = $this->Aelf->getTransactionResults($block['BlockHash']);
        print_r('# get_transaction_results', $transaction_results);
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
        $this->Aelf->executeTransaction($executeTransactionDtoObj);

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
        print_r($execute.'<br/>');
        $transaction2 = array('Transaction'=>$raw_transaction['RawTransaction'],'signature'=>$sign,'returnTransaction'=>true);
        $execute1 = $this->Aelf->sendRawTransaction($transaction2);
        print_r($execute1.'<br/>');
    }
    public function testgetAddressFromPubKeyTest(){
        $pubKeyAddress = $this->Aelf->getAddressFromPubKey($this->public_key);
        $this->assertTrue($pubKeyAddress == $this->address);
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
