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
    public $private_key;
    public $public_key;
    public $address;
    public $OPREATIONADDRESS;

    public function setUp() {
        $url = 'http://127.0.0.1:8001';
       
        $this->AElf = new AElf($url);
        $this->OPREATIONADDRESS ='127.0.0.1:6800';

        $AElfECDSA = new AElfECDSA();
        $this->private_key = 'cd86ab6347d8e52bbbe8532141fc59ce596268143a308d1d40fedf385528b458';
        $AElfECDSA->setPrivateKey($this->private_key);
        $this->public_key = $AElfECDSA->getUncompressedPubKey();
        $this->address= $this->AElf->getAddressFromPrivateKey($this->private_key);
        $this->base58 = new Base58();
    }
    public function testGetChainStatus(){
        $ChainStatus =$this->AElf->getChainStatus();
        print_r($ChainStatus);
        $this->assertTrue($ChainStatus['BestChainHeight'] > 0);
        $ChainId = $this->AElf->getChainId();
        print_r($ChainId);
        $this->assertTrue($ChainId == 9992731);

    }

    public function testBlockapi(){
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


    public function testGetTransactionFee(){
        $TransactionResult = [
            [
                "Address"=>"25CecrU94dmMdbhC3LWMKxtoaL4Wv8PChGvVJM6PxkHAyvXEhB",
                "Name"=> "TransactionFeeCharged",
                "Indexed"=>null,
                "NonIndexed"=> "CgNFTEYQ8OGPHw=="
            ],
            [
                "Address"=> "25CecrU94dmMdbhC3LWMKxtoaL4Wv8PChGvVJM6PxkHAyvXEhB",
                "Name"=> "ResourceTokenCharged",
                "Indexed"=> null,
                "NonIndexed"=>  "CgNFTEYQ8OGPHw=="
            ],
        ];
        $TransactionFees = $this->AElf->getTransactionFees($TransactionResult);
        var_dump($TransactionFees);
        $this->assertEquals($TransactionFees[0]['symbol'],'ELF');
        $this->assertEquals($TransactionFees[0]['amount'],32635000);
    
    }

    public function testGetTransactionFees(){

        $toAccount = "2bWwpsN9WSc4iKJPHYL4EZX3nfxVY7XLadecnNMar1GdSb4hJz";
        $Hash = new Hash();
        $Hash->setValue(hex2bin(hash('sha256','AElf.ContractNames.Token')));
        $toAddress = $this->AElf->getContractAddressByName($this->private_key,$Hash);
        $methodName = "Transfer";
        $Bit = new AElfECDSA();
        $param =new TransferInput(['to'=>new Address(['value'=>$Bit->decodeChecked($toAccount)]),'symbol'=>'ELF','amount'=>1000]);
        $transaction = $this->AElf->generateTransaction($this->address,$toAddress,$methodName,$param);
        $signature = $this->AElf->signTransaction($this->private_key, $transaction);
        $transaction->setSignature(hex2bin($signature));
        $Transactioninput =['RawTransaction'=>bin2hex($transaction->serializeToString())];
        $result =  $this->AElf->sendTransaction($Transactioninput);
        print_r($result);
        $TransactionResult = $this->AElf->getTransactionResult($result['TransactionId']);
        print_r($TransactionResult);
        $TransactionFees = $this->AElf->getTransactionFees($TransactionResult['Logs']);
        $this->assertEquals($TransactionFees[0]['symbol'],'ELF');
        $this->assertEquals($TransactionFees[0]['amount'],32635000);
    }
    public function testGetTransactionResultApi(){
        $Block = $this->AElf->getBlockByHeight(1,true);
        $Transaction_result = $this->AElf->getTransactionResult($Block['Body']['Transactions'][0]);
        print_r('# get_transaction_result');
        print_r($Transaction_result);
        $this->assertTrue($Transaction_result['Status'] == 'MINED');
        $Transaction_results = $this->AElf->getTransactionResults($Block['BlockHash']);
        print_r('# get_transaction_results');
        print_r($Transaction_results);
        $merkle_path = $this->AElf->getMerklePathByTransactionId($Block['Body']['Transactions'][0]);
        $this->assertTrue(is_array($merkle_path['MerklePathNodes']));
    }
    public function testExecuteTransaction(){
        $toAddress = $this->AElf->getGenesisContractAddress();
        $methodName = "GetContractAddressByName";
        $Bytes = new Hash();
        $Bytes->setValue(hex2bin(hash('sha256','AElf.ContractNames.TokenConverter')));
        $Transaction = $this->AElf->generateTransaction($this->address, $toAddress, $methodName, $Bytes);
        $Signature = $this->AElf->signTransaction($this->private_key, $Transaction);
        $Transaction->setSignature(hex2bin($Signature));
        $ExecuteTransactionDtoObj =['RawTransaction'=>bin2hex($Transaction->serializeToString())];
        $Response =  $this->AElf->executeTransaction($ExecuteTransactionDtoObj);
        $Address = new Address();
        $Address->mergeFromString(hex2bin($Response));
        $base58Str = $this->base58->encodeChecked($Address->getValue());
        $Address  = $this->AElf->getContractAddressByName($this->private_key,$Bytes);
        $this->assertTrue($Address == $base58Str);
    
    }
    public function testRawTransactionApi(){
        $status = $this->AElf->getChainStatus();
        $Params = base64_encode(hex2bin(hash('sha256', 'AElf.ContractNames.Consensus')));
        $param = array('value'=>$Params);
        $transaction = [
            "from" =>$this->AElf->getAddressFromPrivateKey($this->private_key),
            "to"=>$this->AElf->getGenesisContractAddress(),
            "refBlockNumber"=>$status['BestChainHeight'],
            "refBlockHash"=> $status['BestChainHash'],
            "methodName"=> "GetContractAddressByName",
            "params"=> json_encode($param)
        ];
        $RawTransaction  = $this->AElf->createRawTransaction($transaction);
        print_r($RawTransaction);
        $transactionId =hash('sha256',hex2bin($RawTransaction['RawTransaction']));

        $sign =  $this->AElf->getSignatureWithPrivateKey($this->private_key,$transactionId);

        $transaction = array('RawTransaction'=>$RawTransaction['RawTransaction'],'signature'=>$sign);
        $Execute = $this->AElf->executeRawTransaction($transaction);
        print_r($Execute);
        $transaction2 = array('Transaction'=>$RawTransaction['RawTransaction'],'signature'=>$sign,'returnTransaction'=>true);
        $Execute1 = $this->AElf->sendRawTransaction($transaction2);
        print_r($Execute1);
        $this->assertTrue($Execute1 != '');
    }
    public function testgetAddressFromPubKey(){
        $pubKeyAddress = $this->AElf->getAddressFromPubKey('04166cf4be901dee1c21f3d97b9e4818f229bec72a5ecd56b5c4d6ce7abfc3c87e25c36fd279db721acf4258fb489b4a4406e6e6e467935d06990be9d134e5741c');
       
        print_r($pubKeyAddress);
        $this->assertTrue($pubKeyAddress == 'SD6BXDrKT2syNd1WehtPyRo3dPBiXqfGUj8UJym7YP9W9RynM');
    }

    public function testSendTransactionApi(){
        $Params = new Hash();
        $Params->setValue(hex2bin(hash('sha256','AElf.ContractNames.Vote')));
        $Transaction = $this->buildTransaction($this->AElf->getGenesisContractAddress(),'GetContractAddressByName',$Params);
   
        $ExecuteTransactionDtoObj =['RawTransaction'=>bin2hex($Transaction->serializeToString())];
        $result =  $this->AElf->sendTransaction($ExecuteTransactionDtoObj);
        print_r($result);
        $this->assertTrue($result['TransactionId'] != "");
    }
    public function testsendTransactions() {
        $toAddress = $this->AElf->getGenesisContractAddress();
        $Params1 = new Hash();
        $Params1->setValue(hex2bin(hash('sha256','AElf.ContractNames.Token')));
        $Params2 = new Hash();
        $Params2->setValue(hex2bin(hash('sha256','AElf.ContractNames.Vote')));
        $methodName = "GetContractAddressByName";
        $tmp  = [$Params1,$Params2];
        foreach($tmp as $k){
            $transactionObj = $this->buildTransaction($toAddress,$methodName,$k);
            $rawTransactions = bin2hex($transactionObj->serializeToString());
            $sendTransactionsInputs = ['RawTransactions'=>$rawTransactions];
            $listString = $this->AElf->sendTransactions($sendTransactionsInputs);
            print_r($listString);
           $this->assertTrue($listString != "");
        }

    }
    public function testTransactionPoolApi(){
        $TransactionPoolStatus = $this->AElf->getTransactionPoolStatus();
        print_r('# get_transaction_pool_status:');
        print_r($TransactionPoolStatus);
        $this->assertTrue($TransactionPoolStatus['Queued'] >= 0);
    }

    public function testTaskQueueApi(){
        $task_queue_status = $this->AElf->getTaskQueueStatus();
        print_r($task_queue_status);
        $this->assertTrue(count($task_queue_status) > 0);
    }

    public function testNetworkApi(){
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
        $this->assertTrue($this->AElf->addPeer($this->OPREATIONADDRESS));
    }

    public function testGetContractFilCeDescriptorSet(){
        $BlockHeight = $this->AElf->getBlockHeight();
        $this->assertTrue($BlockHeight  > 0);
        $blockDto = $this->AElf->getBlockByHeight($BlockHeight, false);
        $transactionResultDtoList = $this->AElf->getTransactionResults($blockDto['BlockHash'],0,10);

        foreach($transactionResultDtoList as $v){
            $this->AElf->getContractFilCeDescriptorSet($v['Transaction']['To']);
     
        }
       

    }


    public function testGenerateKeyPairInfo(){
        $PairInfo = $this->AElf->generateKeyPairInfo();

        $this->assertTrue($PairInfo!=null);
    }

    public function testHelpers(){
        $is_connected =$this->AElf->isConnected();
        $this->assertTrue($is_connected);
    }

    public function testGetFormattedAddress(){
        $addressVal = $this->AElf->getFormattedAddress($this->private_key, $this->address);
   
        $NowAddress = "ELF_".$this->address."_AELF";

        $this->assertEquals($NowAddress,$addressVal);
    }

    private function buildTransaction($toaddress,$methodName,$params){
      
        $transactionObj  = $this->AElf->generateTransaction($this->address,$toaddress,$methodName,$params);

        $signature = $this->AElf->signTransaction($this->private_key,$transactionObj);
        $transactionObj->setSignature(hex2bin($signature));

        return $transactionObj;
    }
}

?>

