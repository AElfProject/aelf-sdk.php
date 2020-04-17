<?php
require __DIR__ . '/vendor/autoload.php';
use AElf\AElf;
use AElf\Protobuf\Generated\Hash;
use GPBMetadata\Types;

$url = 'http://127.0.0.1:8000';
$aelfClient = new AElf($url);
use BitcoinPHP\BitcoinECDSA\BitcoinECDSA;;

// create a new wallet
$AElfECDSA = new BitcoinECDSA();
// load a wallet by private key
$privateKey = 'be3abe5c1439899ac2efd0001e15715fd989a3ae11f09e1cb95d320cd4993e2a';
$AElfECDSA->setPrivateKey($privateKey);
// To obtain the public key
$publicKey = $AElfECDSA->getUncompressedPubKey();
$tokenContractName = new Hash();
$tokenContractName->setValue(hex2bin(hash('sha256', 'AElf.ContractNames.Token')));
$tokenContractAddress = $aelfClient->getContractAddressByName($privateKey, $tokenContractName);
var_dump($tokenContractAddress);
?>
