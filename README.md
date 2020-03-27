# AElf-sdk.php
AElf php SDK

```lang=bash
$ composer require AElf/AElf-sdk
```

## Usage

## Basic usage

### Examples

You can also see full examples in `./example`;

1. Create a new instance of AElf, connect to an AElf chain node.
    ```php
  	<?php
    require_once 'vendor/autoload.php';
    use AElf\AElf;
    $url = '127.0.0.1:8000';
    $AElf = new AElf($url);
    ```
2. Create or load a wallet with `AElf.wallet`;

    ```php
    use use AElf\AElfECDSA\AElfECDSA;
    // create a new wallet
    $AElfECDSA = new AElfECDSA();
    // load a wallet by private key
    $privateKey = 'be3abe5c1439899ac2efd0001e15715fd989a3ae11f09e1cb95d320cd4993e2a';
    $AElfECDSA->setPrivateKey($privateKey);
    // To obtain the public key
    $publicKey = $AElfECDSA->getUncompressedPubKey();
    ```
3. Get a system contract address, take `AElf.ContractNames.Token` as an example
    ```php
    $tokenContractName = 'AElf.ContractNames.Token';
    $tokenContractAddress = $AElf->getContractAddressByName($privateKey,hex2bin(hash('sha256',$tokenContractName)));
    ```
4. Get a contract instance by contract address
    ```php
	  $tokenContractName = 'AElf.ContractNames.Token';
    $tokenContract = $AElf->getTransactionResults($tokenContractAddress);
    ```
5. How to use contract instance

    A contract instance consists of several contract methods and methods can be called in two ways: read-only and send transaction
    ```php
    use AElf\Protobuf\Generated\Hash;
    use GPBMetadata\Types;
    $params = new Hash();
    $params->setValue(hex2bin(hash('sha256','AElf.ContractNames.Vote')));
    $transactionObj  = $this->AElf->generateTransaction($this->address,$AElf->getGenesisContractAddress(),'GetContractAddressByName',$params);
    $signature = $AElf->signTransaction($privateKey,$transactionObj);
    $transactionObj->setSignature(hex2bin($signature));
    $executeTransactionDtoObj =['RawTransaction'=>bin2hex($transaction->serializeToString())];
    $result =  $AElf->sendTransaction($executeTransactionDtoObj);
    print_r($result);
    ```
### Test

This module contains tests for all services provided by AElf. You can see how to properly use services provided by AElf here.

You need to firstly set necessary parameters to make sure tests can run successfully.

1. Set baseUrl to your target url.

   
   ```php

   $url = "Http://127.0.0.1:8001";
   ```


2. Give a valid privateKey of a node.

   ```php
   $this->privateKey = 'be3abe5c1439899ac2efd0001e15715fd989a3ae11f09e1cb95d320cd4993e2a';
   ```

### Note

You need to run a local or remote AElf node to run the unit test successfully. If you're not familiar with how to run a node or multiple nodes, please see [Running a node](https://docs.AElf.io/v/dev/main/main/run-node) / [Running multiple nodes](https://docs.AElf.io/v/dev/main/main/multi-nodes) for more information.