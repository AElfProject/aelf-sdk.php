<?php
	require_once '../vendor/autoload.php';
	use AElf\AElf;
	use AElf\Protobuf\Generated\Hash;
	use GPBMetadata\Types;
	$url = '127.0.0.1:8000';
	$AElf = new AElf($url);
	use use AElf\AElfECDSA\AElfECDSA;
    // create a new wallet
    $AElfECDSA = new AElfECDSA();
    // load a wallet by private key
    $private_key = 'be3abe5c1439899ac2efd0001e15715fd989a3ae11f09e1cb95d320cd4993e2a';
    $AElfECDSA->setPrivateKey($private_key);
    // To obtain the public key
    $public_key = $AElfECDSA->getUncompressedPubKey();
	$tokenContractName = new Hash();
    $tokenContractName->setValue(hex2bin(hash('sha256','AElf.ContractNames.Token')));
    $tokenContractAddress = $this->AElf->getContractAddressByName($private_key,$tokenContractName);
    var_dump($tokenContractAddress);
?>
