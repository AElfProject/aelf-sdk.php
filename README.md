# aelf-sdk.php
AElf php SDK

```lang=bash
$ composer require aelf/aelf-sdk
```

## Usage

## Basic usage

### Examples

You can also see full examples in `./example`;

1. Create a new instance of AElf, connect to an AELF chain node.
    ```php
	<?php

	require_once 'vendor/autoload.php';
	use Aelf\Aelf;
	$url = '127.0.0.1:8000';
	$Aelf = new Aelf($url);
    ```
2. Create or load a wallet with `AElf.wallet`;

    ```php
    use use Aelf\AelfECDSA\AelfECDSA;
    // create a new wallet
    $AelfECDSA = new AelfECDSA();
    // load a wallet by private key
    $private_key = 'be3abe5c1439899ac2efd0001e15715fd989a3ae11f09e1cb95d320cd4993e2a';
    $AelfECDSA->setPrivateKey($private_key);
    // To obtain the public key
    $public_key = $AelfECDSA->getUncompressedPubKey();
    ```
3. Get a system contract address, take `AElf.ContractNames.Token` as an example
    ```php
	$tokenContractName = 'AElf.ContractNames.Token';
    
    $tokenContractAddress = $Aelf->getContractAddressByName($private_key,hex2bin(hash('sha256',$tokenContractName)));
    ```
4. Get a contract instance by contract address
    ```php
	$tokenContractName = 'AElf.ContractNames.Token';
    
    $tokenContract = $Aelf->getTransactionResults($tokenContractAddress);
    ```
5. How to use contract instance

    A contract instance consists of several contract methods and methods can be called in two ways: read-only and send transaction
    ```php

  	$params = hex2bin(hash('sha256','AElf.ContractNames.Vote'));
    $transactionObj  = $this->Aelf->generateTransaction($this->address,$Aelf->getGenesisContractAddress(),'GetContractAddressByName',$params);
	$signature = $Aelf->signTransaction($private_key,$transactionObj);
	$transactionObj->setSignature(hex2bin($signature));
    $executeTransactionDtoObj =['RawTransaction'=>bin2hex($transaction->serializeToString())];
    $result =  $Aelf->sendTransaction($executeTransactionDtoObj);
    print_r($result);
    ```

### Test

This module contains tests for all services provided by AElf. You can see how to properly use services provided by AElf here.

You need to firstly set necessary parameters to make sure tests can run successfully.

1. Set baseUrl to your target url.

   
   ```php

   $url = "Http://127.0.0.1:8001";
   ```
   ?>

2. Give a valid privateKey of a node.

   ```php
   $this->private_key = 'be3abe5c1439899ac2efd0001e15715fd989a3ae11f09e1cb95d320cd4993e2a';
   ```

### Note

You need to run a local or remote AElf node to run the unit test successfully. If you're not familiar with how to run a node or multiple nodes, please see [Running a node](https://docs.aelf.io/v/dev/main/main/run-node) / [Running multiple nodes](https://docs.aelf.io/v/dev/main/main/multi-nodes) for more information.