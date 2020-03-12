=>$sign);
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
    