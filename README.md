# AElf-sdk.php

#Introduction

This is a PHP client library, used to communicate with the [AElf](https://github.com/AElfProject/AElf)  API.

## Installation
AElf php SDK

In order to install this library via composer run the following command in the console:

```lang=bash
$ composer require aelf/aelf-sdk
```
composer require curl/curl

If you directly clone the sdk You must install composer and execute it in the root directory 

```lang=bash
"aelf/aelf-sdk": "dev-dev"
```

## Environment configuration And extensions that need to be turned on

```lang=bash
1. PHP => 7.0
2. gmp
3. curl
```

## Basic usage
    
```php
require_once 'vendor/autoload.php';
use AElf\AElf;
$url = '127.0.0.1:8000';
$aelfClient = new AElf($url);
$height = $aelfClient->GetBlockHeight();
```
### Examples

You can also see full examples in `./example`;

### Interface

Interface methods can be easily available by the instance "aelfClient" shown in basic usage. The following is a list of input parameters and output for each method. Check out the [Web api reference](https://docs.aelf.io/v/dev/reference) for detailed Interface description.

#### IBlockAppService

```php
public function getBlockHeight();

public function getBlockByHash($blockHash,$includeTransactions = false);

public function getBlockByHeight($blockHeight,$includeTransactions = false);

```

#### IChainAppService

```php
public function getChainStatus();

public function getContractFileDescriptorSet($address);

public function getCurrentRoundInformationAsync();

public function getTaskQueueStatusAsync();

public function getChainIdAsync();
```
#### INetAppService

```php
 public function addPeer($address);

 public function removePeer($address);

 public function getPeers($withMetrics);

 public function getNetworkInfo();
```
#### ITransactionAppService

```php
public function getTransactionPoolStatus();

public function executeTransaction($input);

public function executeRawTransaction($input);

public function createRawTransaction($input);

public function sendRawTransaction($input);

public function sendTransaction($input);

public function sendTransactions($input);

public function getTransactionResult($transactionId);

public function getTransactionResults($blockHash, $offset = 0, $limit = 10);

public function getMerklePathByTransactionId($transactionId);
```

#### IClientService

```php
public function isConnected();

public function getFormattedAddress($privateKey, $address);

public function getAddressFromPubKey($pubKey) ;

public function getGenesisContractAddress();

public function getContractAddressByName($privateKey, $contractNameHash);
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