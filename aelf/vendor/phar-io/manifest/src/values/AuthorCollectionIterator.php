tTransactionPoolStatus();
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
    public function sendT