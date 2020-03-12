<?php


require_once "BlockChainSdk.php";
require_once "NetSdk.php";
/**
 *
 * @day2020022
 */
require_once "Bytes.php";
require_once "Sha.php";
require_once "pro/Aelf/Protobuf/Generated/Address.php";
require_once "pro/Aelf/Protobuf/Generated/Transaction.php";
require_once "pro/Aelf/Protobuf/Generated/TransferInput.php";
require_once "pro/Aelf/Protobuf/Generated/StringInput.php";
require_once "pro/Aelf/Protobuf/Generated/Hash.php";
require_once "pro/GPBMetadata/Types.php";
require_once "pro/GPBMetadata/Timestamp.php";
use Aelf\Protobuf\Generated\Address;
use Aelf\Protobuf\Generated\Transaction;
use Aelf\Protobuf\Generated\TransferInput;
use Aelf\Protobuf\Generated\StringInput;
use Aelf\Protobuf\Generated\Hash;
use GPBMetadata\Types;
use BitcoinPHP\BitcoinECDSA\BitcoinECDSA;
use StephenHill\Base58;
use Bytes;
use kornrunner\Secp256k1;
use kornrunner\Serializer\HexSignatureSerializer;
/**
 * AEL