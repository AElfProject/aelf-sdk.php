>setParams($Hash->serializeToString());
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
     * @param