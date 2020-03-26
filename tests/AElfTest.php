<?php

use AElf\AElf;
use PHPUnit\Framework\TestCase;
use AElf\AElfECDSA\AElfECDSA;
use AElf\Protobuf\Generated\Address;
use GPBMetadata\Types;
use AElf\Protobuf\Generated\TransferInput;
use AElf\Protobuf\Generated\TransferFromInput;
use StephenHill\Base58;
use AElf\Protobuf\Generated\Hash;
class AElfTest extends TestCase
{
    public $aelf;
    public $privateKey;
    public $public_key;
    public $address;
    public $opreationAddress;

    public function setUp() {
        $url = 'http://127.0.0.1:8001';
        $this->aelf = new AElf($url);
        $this->opreationAddress ='127.0.0.1:6800';
        $aelfEcdsa = new AElfECDSA();
        $this->privateKey = 'cd86ab6347d8e52bbbe8532141fc59ce596268143a308d1d40fedf385528b458';
        $aelfEcdsa->setPrivateKey($this->privateKey);
        $this->public_key = $aelfEcdsa->getUncompressedPubKey();
        $this->address= $this->aelf->getAddressFromPrivateKey($this->privateKey);
        $this->base58 = new Base58();
    }
    public function testGetChainStatusApi(){
        $chainStatus =$this->aelf->getChainStatus();
        print_r($chainStatus);
        $this->assertTrue($chainStatus['BestChainHeight'] > 0);
        $chainId = $this->aelf->getChainId();
        print_r($chainId);
        $this->assertTrue($chainId == 9992731);

    }

    public function testBlockApi(){
        $blockHeight = $this->aelf->getBlockHeight();
        print_r('# getBlockHeight');
        echo '<br>';
        print_r($blockHeight);
        $this->assertTrue($blockHeight > 0);
        $block = $this->aelf->getBlockByHeight(1,true);
        $this->assertTrue($block['Header']['Height'] ==1 );
        $block2 = $this->aelf->getBlockByHash($block['BlockHash'],false);
        $this->assertTrue($block2['Header']['Height'] == 1);
        return $block2['Header']['Height'];

    }

    public function testGetTransactionFeesApi(){

        $toAccount = "2bWwpsN9WSc4iKJPHYL4EZX3nfxVY7XLadecnNMar1GdSb4hJz";
        $hash = new Hash();
        $hash->setValue(hex2bin(hash('sha256','AElf.ContractNames.Token')));
        $toAddress = $this->aelf->getContractAddressByName($this->privateKey,$hash);
        $methodName = "Transfer";
        $bit = new AElfECDSA();
        $param =new TransferInput(['to'=>new Address(['value'=>$bit->decodeChecked($toAccount)]),'symbol'=>'ELF','amount'=>1]);
        $transaction = $this->aelf->generateTransaction($this->address,$toAddress,$methodName,$param);
        $signature = $this->aelf->signTransaction($this->privateKey, $transaction);
        $transaction->setSignature(hex2bin($signature));
        $transactionInput =['RawTransaction'=>bin2hex($transaction->serializeToString())];
        $result =  $this->aelf->sendTransaction($transactionInput);
        print_r($result);
        sleep(4);
        $transactionResult = $this->aelf->getTransactionResult($result['TransactionId']);

        $transactionFees = $this->aelf->getTransactionFees($transactionResult);
     
        $this->assertEquals($transactionFees[0]['symbol'],'ELF');
        $this->assertEquals($transactionFees[0]['amount'],1);
    }
    public function testGetTransactionResultApi(){
        $block = $this->aelf->getBlockByHeight(1,true);
        $transactionResult = $this->aelf->getTransactionResult($block['Body']['Transactions'][0]);
        print_r('# get_transaction_result');
        print_r($transactionResult);
        $this->assertTrue($transactionResult['Status'] == 'MINED');
        $transactionResults = $this->aelf->getTransactionResults($block['BlockHash']);
        print_r('# get_transaction_results');
        print_r($transactionResults);

        $merklePath = $this->aelf->getMerklePathByTransactionId($block['Body']['Transactions'][0]);

        $this->assertTrue(is_array($merklePath['MerklePathNodes']));
    }
    public function testExecuteTransactionApi(){
        $toAddress = $this->aelf->getGenesisContractAddress();
        $methodName = "GetContractAddressByName";
        $bytes = new Hash();
        $bytes->setValue(hex2bin(hash('sha256','AElf.ContractNames.TokenConverter')));
        $transaction = $this->aelf->generateTransaction($this->address, $toAddress, $methodName, $bytes);
        $signature = $this->aelf->signTransaction($this->privateKey, $transaction);
        $transaction->setSignature(hex2bin($signature));
        $executeTransactionDtoObj =['RawTransaction'=>bin2hex($transaction->serializeToString())];
        $response =  $this->aelf->executeTransaction($executeTransactionDtoObj);
        $address = new Address();
        $address->mergeFromString(hex2bin($response));
        $base58Str = $this->base58->encodeChecked($address->getValue());
        $address  = $this->aelf->getContractAddressByName($this->privateKey,$bytes);
        $this->assertTrue($address == $base58Str);
    
    }
    public function testRawTransactionApi(){
        $status = $this->aelf->getChainStatus();
        $params = base64_encode(hex2bin(hash('sha256', 'AElf.ContractNames.Consensus')));
        $param = array('value'=>$params);
        $transaction = [
            "from" =>$this->aelf->getAddressFromPrivateKey($this->privateKey),
            "to"=>$this->aelf->getGenesisContractAddress(),
            "refBlockNumber"=>$status['BestChainHeight'],
            "refBlockHash"=> $status['BestChainHash'],
            "methodName"=> "GetContractAddressByName",
            "params"=> json_encode($param)
        ];
        $rawTransaction  = $this->aelf->createRawTransaction($transaction);
        print_r($rawTransaction);
        $transactionId =hash('sha256',hex2bin($rawTransaction['RawTransaction']));

        $sign =  $this->aelf->getSignatureWithPrivateKey($this->privateKey,$transactionId);

        $transaction = array('RawTransaction'=>$rawTransaction['RawTransaction'],'signature'=>$sign);
        $execute = $this->aelf->executeRawTransaction($transaction);
        print_r($execute);
        $transaction2 = array('Transaction'=>$rawTransaction['RawTransaction'],'signature'=>$sign,'returnTransaction'=>true);
        $execute1 = $this->aelf->sendRawTransaction($transaction2);
        print_r($execute1);
        $this->assertTrue($execute1 != '');
    }
    public function testGetAddressFromPubKey(){
        $pubKeyAddress = $this->aelf->getAddressFromPubKey('04166cf4be901dee1c21f3d97b9e4818f229bec72a5ecd56b5c4d6ce7abfc3c87e25c36fd279db721acf4258fb489b4a4406e6e6e467935d06990be9d134e5741c');
       
        print_r($pubKeyAddress);
        $this->assertTrue($pubKeyAddress == 'SD6BXDrKT2syNd1WehtPyRo3dPBiXqfGUj8UJym7YP9W9RynM');
    }

    public function testSendTransactionApi(){
        $params = new Hash();
        $params->setValue(hex2bin(hash('sha256','AElf.ContractNames.Vote')));
        $transaction = $this->buildTransaction($this->aelf->getGenesisContractAddress(),'GetContractAddressByName',$params);
   
        $executeTransactionDtoObj =['RawTransaction'=>bin2hex($transaction->serializeToString())];
        $result =  $this->aelf->sendTransaction($executeTransactionDtoObj);
        print_r($result);
        $this->assertTrue($result['TransactionId'] != "");
    }
    public function testSendTransactionsApi() {
        $toAddress = $this->aelf->getGenesisContractAddress();
        $params1 = new Hash();
        $params1->setValue(hex2bin(hash('sha256','AElf.ContractNames.Token')));
        $params2 = new Hash();
        $params2->setValue(hex2bin(hash('sha256','AElf.ContractNames.Vote')));
        $methodName = "GetContractAddressByName";
        $paramsList  = [$params1,$params2];
        $rawTransactionsList = [];
        foreach($paramsList as $k){
            $transactionObj = $this->buildTransaction($toAddress,$methodName,$k);
            $rawTransactions = bin2hex($transactionObj->serializeToString());
            array_push($rawTransactionsList,$rawTransactions);
        }
        $sendTransactionsInputs = ['RawTransactions'=>implode(',',$rawTransactionsList)];
        $listString = $this->aelf->sendTransactions($sendTransactionsInputs);
        print_r($listString);
        $this->assertTrue($listString != "");

    }
    public function testTransactionPoolApi(){
        $transactionPoolStatus = $this->aelf->getTransactionPoolStatus();
        print_r('# get_transaction_pool_status:');
        print_r($transactionPoolStatus);
        $this->assertTrue($transactionPoolStatus['Queued'] >= 0);
    }

    public function testTaskQueueApi(){
        $taskQueueStatus = $this->aelf->getTaskQueueStatus();
        print_r($taskQueueStatus);
        $this->assertTrue(count($taskQueueStatus) > 0);
    }

    public function testNetworkApi(){
       print('getNetworkInfo');
        echo "<br>";
        print_r($this->aelf->getNetworkInfo());
        echo "<br>";
        print('remove_peer');
        echo "<br>";
        var_dump($this->aelf->removePeer($this->opreationAddress));
        echo "<br>";
        print('add_peer');
        print_r($this->aelf->addPeer($this->opreationAddress));
        print_r($this->aelf->getPeers(true));
 
    //    $this->markTestSkipped();
        $this->assertTrue(!$this->aelf->addPeer($this->opreationAddress));
    }

    public function testGetContractFileDescriptorSetApi(){
        $blockHeight = $this->aelf->getBlockHeight();
        $this->assertTrue($blockHeight  > 0);
        $blockDto = $this->aelf->getBlockByHeight($blockHeight, false);
        $transactionResultDtoList = $this->aelf->getTransactionResults($blockDto['BlockHash'],0,10);

        foreach($transactionResultDtoList as $v){
            $request = $this->aelf->getContractFileDescriptorSet($v['Transaction']['To']);
            print_r($request);
        }
       

    }


    public function testGenerateKeyPairInfo(){
        $pairInfo = $this->aelf->generateKeyPairInfo();

        $this->assertTrue($pairInfo!=null);
    }

    public function testHelpers(){
        $isConnected =$this->aelf->isConnected();
        $this->assertTrue($isConnected);
    }

    public function testGetFormattedAddress(){
        $addressVal = $this->aelf->getFormattedAddress($this->privateKey, $this->address);
   
        $nowAddress = "ELF_".$this->address."_AELF";

        $this->assertEquals($nowAddress,$addressVal);
    }

    private function buildTransaction($toaddress,$methodName,$params){
      
        $transactionObj  = $this->aelf->generateTransaction($this->address,$toaddress,$methodName,$params);

        $signature = $this->aelf->signTransaction($this->privateKey,$transactionObj);
        $transactionObj->setSignature(hex2bin($signature));

        return $transactionObj;
    }
}

?>