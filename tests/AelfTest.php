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
    public $AElf;
    public $privateKey;
    public $publicKey;
    public $address;
    public $OPREATIONADDRESS;

    public function setUp() 
    {
        $url = 'http://18.223.158.83:8000';
       
        $this->AElf = new AElf($url);
        $this->OPREATIONADDRESS ='127.0.0.1:6800';

        $AElfECDSA = new AElfECDSA();
        $this->privateKey = 'cd86ab6347d8e52bbbe8532141fc59ce596268143a308d1d40fedf385528b458';
        $AElfECDSA->setPrivateKey($this->privateKey);
        $this->publicKey = $AElfECDSA->getUncompressedPubKey();
        $this->address= $this->AElf->getAddressFromPrivateKey($this->publicKey);
        $this->base58 = new Base58();
    }
    public function testGetChainStatusApi()
    {
        $chainStatus =$this->AElf->getChainStatus();
        print_r($chainStatus);
        $this->assertTrue($chainStatus['BestChainHeight'] > 0);
        $chainId = $this->AElf->getChainId();
        print_r($chainId);
        $this->assertTrue($chainId == 9992731);

    }

    public function testBlockApi()
    {
        $blockHeight = $this->AElf->getBlockHeight();
        print_r('# getBlockHeight');
        echo '<br>';
        print_r($blockHeight);
        $this->assertTrue($blockHeight > 0);
        $block = $this->AElf->getBlockByHeight(1,true);
        $this->assertTrue($block['Header']['Height'] ==1 );
        $block2 = $this->AElf->getBlockByHash($block['BlockHash'],false);
        $this->assertTrue($block2['Header']['Height'] == 1);
        return $block2['Header']['Height'];

    }

    public function testGetTransactionFeesApi()
    {

        $toAccount = "2bWwpsN9WSc4iKJPHYL4EZX3nfxVY7XLadecnNMar1GdSb4hJz";
        $hash = new Hash();
        $hash->setValue(hex2bin(hash('sha256','AElf.ContractNames.Token')));
        $toAddress = $this->AElf->getContractAddressByName($this->privateKey,$hash);
        $methodName = "Transfer";
        $bit = new AElfECDSA();
        $param =new TransferInput(['to'=>new Address(['value'=>$bit->decodeChecked($toAccount)]),'symbol'=>'ELF','amount'=>1]);
        $transaction = $this->AElf->generateTransaction($this->address,$toAddress,$methodName,$param);
        $signature = $this->AElf->signTransaction($this->privateKey, $transaction);
        $transaction->setSignature(hex2bin($signature));
        $transactionInput =['RawTransaction'=>bin2hex($transaction->serializeToString())];
        $result =  $this->AElf->sendTransaction($transactionInput);
        print_r($result);
        sleep(4);
        $transactionResult = $this->AElf->getTransactionResult($result['TransactionId']);
        var_dump($transactionResult);
        $transactionFees = $this->AElf->getTransactionFees($transactionResult);
        echo "<br>";

        $this->assertEquals($transactionFees[0]['symbol'],'ELF');
        $this->assertEquals($transactionFees[0]['amount'],0);
    }
    public function testGetTransactionResultApi()
    {
        $block = $this->AElf->getBlockByHeight(1,true);
        $transactionResult = $this->AElf->getTransactionResult($block['Body']['Transactions'][0]);
        print_r('# getRransactionResult');
        print_r($transactionResult);
        $this->assertTrue($transactionResult['Status'] == 'MINED');
        $transactionResult = $this->AElf->getTransactionResults($block['BlockHash']);
        print_r('# getTransactionResults');
        print_r($transactionResult);

        $merklePath = $this->AElf->getMerklePathByTransactionId($block['Body']['Transactions'][0]);

        $this->assertTrue(is_array($merklePath['MerklePathNodes']));
    }
    public function testExecuteTransactionApi()
    {
        $toAddress = $this->AElf->getGenesisContractAddress();
        $methodName = "GetContractAddressByName";
        $bytes = new Hash();
        $bytes->setValue(hex2bin(hash('sha256','AElf.ContractNames.TokenConverter')));
        $transaction = $this->AElf->generateTransaction($this->address, $toAddress, $methodName, $bytes);
        $signature = $this->AElf->signTransaction($this->privateKey, $transaction);
        $transaction->setSignature(hex2bin($signature));
        $executeTransactionDtoObj =['RawTransaction'=>bin2hex($transaction->serializeToString())];
        $response =  $this->AElf->executeTransaction($executeTransactionDtoObj);
        $address = new Address();
        $address->mergeFromString(hex2bin($response));
        $base58Str = $this->base58->encodeChecked($address->getValue());
        $address  = $this->AElf->getContractAddressByName($this->privateKey,$bytes);
        $this->assertTrue($address == $base58Str);
    
    }
    public function testRawTransactionApi()
    {
        $status = $this->AElf->getChainStatus();
        $params = base64_encode(hex2bin(hash('sha256', 'AElf.ContractNames.Consensus')));
        $param = array('value'=>$params);
        $transaction = [
            "from" =>$this->AElf->getAddressFromPrivateKey($this->privateKey),
            "to"=>$this->AElf->getGenesisContractAddress(),
            "refBlockNumber"=>$status['BestChainHeight'],
            "refBlockHash"=> $status['BestChainHash'],
            "methodName"=> "GetContractAddressByName",
            "params"=> json_encode($param)
        ];
        $rawTransaction  = $this->AElf->createRawTransaction($transaction);
        print_r($rawTransaction);
        $transactionId =hash('sha256',hex2bin($rawTransaction['RawTransaction']));

        $signature =  $this->AElf->getSignatureWithPrivateKey($this->privateKey,$transactionId);

        $transaction = array('RawTransaction'=>$rawTransaction['RawTransaction'],'signature'=>$signature);
        $execute = $this->AElf->executeRawTransaction($transaction);
        print_r($execute);
        $transaction2 = array('Transaction'=>$rawTransaction['RawTransaction'],'signature'=>$sign,'returnTransaction'=>true);
        $execute1 = $this->AElf->sendRawTransaction($transaction2);
        print_r($execute1);
        $this->assertTrue($execute1 != '');
    }
    public function testGetAddressFromPubKey()
    {
        $pubKeyAddress = $this->AElf->getAddressFromPubKey('04166cf4be901dee1c21f3d97b9e4818f229bec72a5ecd56b5c4d6ce7abfc3c87e25c36fd279db721acf4258fb489b4a4406e6e6e467935d06990be9d134e5741c');
       
        print_r($pubKeyAddress);
        $this->assertTrue($pubKeyAddress == 'SD6BXDrKT2syNd1WehtPyRo3dPBiXqfGUj8UJym7YP9W9RynM');
    }

    public function testSendTransactionApi()
    {
        $params = new Hash();
        $params->setValue(hex2bin(hash('sha256','AElf.ContractNames.Vote')));
        $transaction = $this->buildTransaction($this->AElf->getGenesisContractAddress(),'GetContractAddressByName',$params);
   
        $executeTransactionDtoObj =['RawTransaction'=>bin2hex($transaction->serializeToString())];
        $result =  $this->AElf->sendTransaction($executeTransactionDtoObj);
        print_r($result);
        $this->assertTrue($result['TransactionId'] != "");
    }
    public function testsendTransactionsApi() 
    {
        $toAddress = $this->AElf->getGenesisContractAddress();
        $params1 = new Hash();
        $params1->setValue(hex2bin(hash('sha256','AElf.ContractNames.Token')));
        $params2 = new Hash();
        $params2->setValue(hex2bin(hash('sha256','AElf.ContractNames.Vote')));
        $methodName = "GetContractAddressByName";
        $listParams  = [$params1,$params2];
        $listRawTransactions = [];
        foreach($listParams as $k){
            $transactionObj = $this->buildTransaction($toAddress,$methodName,$k);
            $rawTransactions = bin2hex($transactionObj->serializeToString());
            array_push($listRawTransactions,$rawTransactions);
        }
        $sendTransactionsInputs = ['RawTransactions'=>implode(',',$listRawTransactions)];
        $listString = $this->AElf->sendTransactions($sendTransactionsInputs);
        print_r($listString);
        $this->assertTrue($listString != "");

    }
    public function testTransactionPoolApi()
    {
        $transactionPoolStatus = $this->AElf->getTransactionPoolStatus();
        print_r('# getTransactionPoolStatus:');
        print_r($transactionPoolStatus);
        $this->assertTrue($transactionPoolStatus['Queued'] >= 0);
    }

    public function testTaskQueueApi()
    {
        $taskQueueStatus = $this->AElf->getTaskQueueStatus();
        print_r($taskQueueStatus);
        $this->assertTrue(count($taskQueueStatus) > 0);
    }

    public function testNetworkApi()
    {
        print('getNetworkInfo');
        echo "<br>";
        print_r($this->AElf->getNetworkInfo());
        echo "<br>";
        print('remove_peer');
        echo "<br>";
        var_dump($this->AElf->removePeer($this->OPREATIONADDRESS));
        echo "<br>";
        print('add_peer');
        print_r($this->AElf->addPeer($this->OPREATIONADDRESS));
        print_r($this->AElf->getPeers(true));
 
    //    $this->markTestSkipped();
        $this->assertTrue(!$this->AElf->addPeer($this->OPREATIONADDRESS));
    }

    public function testGetContractFileDescriptorSetApi()
    {
        $blockHeight = $this->AElf->getBlockHeight();
        $this->assertTrue($blockHeight  > 0);
        $blockDto = $this->AElf->getBlockByHeight($blockHeight, false);
        $transactionResultDtoList = $this->AElf->getTransactionResults($blockDto['BlockHash'],0,10);

        foreach($transactionResultDtoList as $v){
            $request = $this->AElf->getContractFileDescriptorSet($v['Transaction']['To']);
            print_r($request);
        }
       

    }


    public function testGenerateKeyPairInfo()
    {
        $pairInfo = $this->AElf->generateKeyPairInfo();

        $this->assertTrue($pairInfo!=null);
    }

    public function testHelpers()
    {
        $isConnected =$this->AElf->isConnected();
        $this->assertTrue($isConnected);
    }

    public function testGetFormattedAddress()
    {
        $addressVal = $this->AElf->getFormattedAddress($this->privateKey, $this->address);
   
        $nowAddress = "ELF_".$this->address."_AELF";

        $this->assertEquals($nowAddress,$addressVal);
    }

    private function buildTransaction($toaddress,$methodName,$params)
    {
      
        $transactionObj  = $this->AElf->generateTransaction($this->address,$toaddress,$methodName,$params);

        $signature = $this->AElf->signTransaction($this->privateKey,$transactionObj);
        $transactionObj->setSignature(hex2bin($signature));

        return $transactionObj;
    }
}


?>

