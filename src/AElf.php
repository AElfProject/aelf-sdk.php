<?php

namespace AElf;

use AElf\Api\NetSdk;
use AElf\Bytes\Bytes;
use GPBMetadata\Types;
use StephenHill\Base58;
use kornrunner\Secp256k1;
use AElf\Api\BlockChainSdk;
use AElf\Protobuf\Generated\Hash;
use AElf\Protobuf\Generated\Address;
use AElf\Protobuf\Generated\TokenInfo;
use AElf\Protobuf\Generated\StringInput;
use AElf\Protobuf\Generated\Transaction;
use AElf\Protobuf\Generated\GetTokenInfoInput;
use AElf\Protobuf\Generated\TransactionFeeCharged;
use AElf\Protobuf\Generated\ResourceTokenCharged;
use BitcoinPHP\BitcoinECDSA\BitcoinECDSA as AElfECDSA;
use kornrunner\Serializer\HexSignatureSerializer;

/**
 * AElf
 */
class AElf
{
    public $url;
    public $base58;
    public $version;
    public $userName;
    public $password;
    public $netSdk;
    public $blockChainSdk;
    public $getRequestHeader;
    public $postRequestHeader;
    public $privateKey;

    /**
     * Object construction through the url path.
     *
     * @param url Http Request Url exp:(http://xxxx)
     * @param version application/json;v={version}
     */
    public function __construct($url, $version = null, $userName = null, $password = null)
    {
        $this->url = $url;
        if ($version != null) {
            $this->$version = $version;
        }
        if ($userName != null) {
            $this->userName = $userName;
        }
        if ($password != null) {
            $this->password = $password;
        }
        $this->getBlockChainSdkObj();
        $this->getNetSdkObj();
        $this->base58 = new Base58();
    }

    public function getBlockChainSdkObj()
    {
        if ($this->blockChainSdk == NULL) {
            return $this->blockChainSdk = new BlockChainSdk($this->url, $this->version);
        } else {
            return $this->blockChainSdk;
        }
    }

    public function getNetSdkObj()
    {
        if ($this->netSdk == NULL) {
            $this->netSdk = new NetSdk($this->url, $this->version, $this->userName, $this->password);
        } else {
            return $this->netSdk;
        }
    }

    /**
     * Get the height of the current chain. wa:/api/blockChain/blockHeight
     */
    public function getBlockHeight()
    {
        return $this->getBlockChainSdkObj()->getBlockHeight();
    }


    /**
     * Get information about a given block by block hash. Otionally with the list of its transactions.
     * wa://api/blockChain/block?includeTransactions={includeTransactions}
     */
    public function getBlockByHash($blockHash, $includeTransactions = false)
    {
        return $this->getBlockChainSdkObj()->getBlockByHash($blockHash, $includeTransactions);
    }

    /**
     * Get information of a block by specified height. Optional whether to include transaction
     * information. wa://api/blockChain/blockByHeight?includeTransactions={includeTransactions}
     */
    public function getBlockByHeight($blockHeight, $includeTransactions = false)
    {
        //$includeTransactions The default is false
        return $this->getBlockChainSdkObj()->getBlockByHeight($blockHeight, $includeTransactions);
    }

    /**
     * Get the current status of the block chain. wa:/api/blockChain/chainStatus
     */
    public function getChainStatus()
    {
        return $this->getBlockChainSdkObj()->getChainStatus();
    }

    /**
     * Get the protobuf definitions related to a contract /api/blockChain/contractFileDescriptorSet.
     */
    public function getContractFileDescriptorSet($address)
    {
        return $this->getBlockChainSdkObj()->getContractFileDescriptorSet($address);
    }

    /**
     * Gets the status information of the task queue wa:/api/blockChain/taskQueueStatus.
     */
    public function getTaskQueueStatus()
    {
        return $this->getBlockChainSdkObj()->getTaskQueueStatus();
    }

    /**
     * Gets information about the current transaction pool.wa:/api/blockChain/transactionPoolStatus
     */
    public function getTransactionPoolStatus()
    {
        return $this->getBlockChainSdkObj()->getTransactionPoolStatus();
    }

    /**
     * Call a read-only method of a contract. wa:/api/blockChain/executeTransaction
     */
    public function executeTransaction($input)
    {
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
    public function executeRawTransaction($input)
    {
        return $this->getBlockChainSdkObj()->executeRawTransaction($input);
    }

    /**
     * Broadcast a serialized transaction. wa:/api/blockChain/sendRawTransaction
     */
    public function sendRawTransaction($input)
    {
        return $this->getBlockChainSdkObj()->sendRawTransaction($input);
    }

    /**
     * Broadcast a transaction wa:/api/blockChain/sendTransaction.
     */
    public function sendTransaction($input)
    {
        return $this->getBlockChainSdkObj()->sendTransaction($input);
    }

    /**
     * Broadcast volume transactions wa:/api/blockChain/sendTransactions.
     */
    public function sendTransactions($input)
    {
        return $this->getBlockChainSdkObj()->sendTransactions($input);
    }

    /**
     * Get the current status of a transaction wa:/api/blockChain/transactionResult.
     */
    public function getTransactionResult($transactionId)
    {
        return $this->getBlockChainSdkObj()->getTransactionResult($transactionId);
    }


    /**
     * Get multiple transaction results. wa:/api/blockChain/transactionResults
     */
    public function getTransactionResults($blockHash, $offset = 0, $limit = 10)
    {
        return $this->getBlockChainSdkObj()->getTransactionResults($blockHash, $offset, $limit);
    }

    /**
     * Get merkle path of a transaction. wa:/api/blockChain/merklePathByTransactionId
     */
    public function getMerklePathByTransactionId($transactionId)
    {
        return $this->getBlockChainSdkObj()->getMerklePathByTransactionId($transactionId);
    }

    /**
     * Get id of the chain.
     */
    public function getChainId()
    {
        return $this->getBlockChainSdkObj()->getChainId();
    }

    /**
     * Attempts to add a node to the connected network nodes wa:/api/net/peer.
     */
    public function addPeer($address)
    {
        return $this->getNetSdkObj()->addPeer($address);
    }

    /**
     * Attempts to remove a node from the connected network nodes wa:/api/net/peer.
     */
    public function removePeer($address)
    {
        return $this->getNetSdkObj()->removePeer($address);
    }

    /**
     * Gets information about the peer nodes of the current node.Optional whether to include metrics.
     * wa:/api/net/peers?withMetrics=false
     */
    public function getPeers($withMetrics)
    {
        return $this->getNetSdkObj()->getPeers($withMetrics);
    }

    /**
     * Get information about the node’s connection to the network. wa:/api/net/networkInfo
     */
    public function getNetworkInfo()
    {
        return $this->getNetSdkObj()->getNetworkInfo();
    }

    /**
     * Build a transaction from the input parameters.
     */
    public function generateTransaction($from, $to, $methodName, $params)
    {
        $chainStatus = $this->getBlockChainSdkObj()->getChainStatus();
        $from = decodeChecked($from);
        $to = decodeChecked($to);
        $transaction = new Transaction();
        $fromAddress = new Address();
        $toAddress = new Address();
        $fromAddress->setValue($from);
        $toAddress->setValue($to);
        $transaction->setFromAddress($fromAddress);
        $transaction->setToAddress($toAddress);
        $transaction->setMethodName($methodName);
        $transaction->setParams($params->serializeToString());
        $transaction->setRefBlockNumber($chainStatus['BestChainHeight']);
        $transaction->setRefBlockPrefix(substr(hex2bin($chainStatus['BestChainHash']), 0, 4));
        return $transaction;
    }

    /**
     * Sign a transaction using private key.
     */
    public function signTransaction($privateKeyHex, $transaction)
    {
        $transactionData = sha256($transaction->serializeToString());
        return $this->getSignatureWithPrivateKey($privateKeyHex, $transactionData);
    }

    /**
     * Get the address of genesis contract.
     *
     * @return address
     */
    public function getGenesisContractAddress()
    {
        $chainstatusDto = $this->getBlockChainSdkObj()->getChainStatus();
        return $chainstatusDto['GenesisContractAddress'];
    }

    /**
     * Get the account address through the public key.
     *
     * @param pubKey pubKey hex
     * @return Str
     */
    public function getAddressFromPubKey($pubKey = null)
    {
        $aelfKey = new AElfECDSA();
        $address = $aelfKey->hash256(hex2bin($pubKey));
        //checksum
        $address = $address . substr($aelfKey->hash256(hex2bin($address)), 0, 8);
        $address = $aelfKey->base58_encode($address);
        return $address;
    }

    /**
     * Convert the Address to the displayed string：symbol_base58-string_base58-string-chain-id.
     */
    public function getFormattedAddress($privateKey, $address)
    {
        $chainIdString = $this->getBlockChainSdkObj()->getChainStatus()['ChainId'];
        $fromAddress = $this->getAddressFromPrivateKey($privateKey);
        $contractNames = new Hash();
        $contractNames->setValue(hex2bin(sha256('AElf.ContractNames.Token')));
        $toAddress = $this->getContractAddressByName($privateKey, $contractNames);
        $methodName = "GetPrimaryTokenSymbol";
        $bytes = new Hash();
        $bytes->setValue('');
        $transaction = $this->generateTransaction($fromAddress, $toAddress, $methodName, $bytes);
        $signature = $this->signTransaction($privateKey, $transaction);
        $transaction->setSignature(hex2bin($signature));
        $executeTransactionDto = ['RawTransaction' => bin2hex($transaction->serializeToString())];
        $response = $this->getBlockChainSdkObj()->executeTransaction($executeTransactionDto);
        $symbol = new StringInput();
        $symbol->mergeFromString(hex2bin($response));
        $symbolStr = $symbol->getStringValue();
        return $symbolStr . "_" . $address . "_" . $chainIdString;
    }

    /**
     * new generateKeyPairInfo;
     */
    public function generateKeyPairInfo()
    {
        $keyPair = new AElfECDSA();
        $keyPair->generateRandomPrivateKey();
        $privateKey = $keyPair->getPrivateKey();
        $publicKey = $keyPair->getUncompressedPubKey();
        $address = $keyPair->hash256(hex2bin($publicKey));
        $address = $address . substr($keyPair->hash256(hex2bin($address)), 0, 8);
        $address = $keyPair->base58_encode($address);
        $keyPairInfo = array('privateKey' => $privateKey, 'publicKey' => $publicKey, 'address' => $address);
        return $keyPairInfo;
    }

    /**
     * Get address of a contract by given contractNameHash.
     */
    public function getContractAddressByName($privateKey, $contractNameHash)
    {
        $from = $this->getAddressFromPrivateKey($privateKey);
        $to = $this->getGenesisContractAddress();
        $methodName = 'GetContractAddressByName';
        $transaction = $this->generateTransaction($from, $to, $methodName, $contractNameHash);
        $signature = $this->signTransaction($privateKey, $transaction);
        $transaction->setSignature(hex2bin($signature));
        $executeTransactionDto = ['RawTransaction' => bin2hex($transaction->serializeToString())];
        $response = $this->getBlockChainSdkObj()->executeTransaction($executeTransactionDto);
        $address = new Address();
        $address->mergeFromString(hex2bin($response));
        $base58Str = encodeChecked($address->getValue());
        return $base58Str;
    }

    /**
     * Get address of a contract by given contractNameHash.
     */
    public function getAddressFromPrivateKey($privateKey)
    {
        $aelfKey = new AElfECDSA();
        $aelfKey->setPrivateKey($privateKey);
        $publicKey = $aelfKey->getUncompressedPubKey();
        $address = $aelfKey->hash256(hex2bin($publicKey));
        $address = $address . substr($aelfKey->hash256(hex2bin($address)), 0, 8);
        $address = $aelfKey->base58_encode($address);
        return $address;
    }

    /**
     * Get the private sha256 signature.
     */
    public function getSignatureWithPrivateKey($privateKey, $txData)
    {
        $secp256k1 = new Secp256k1();
        $signature = $secp256k1->sign($txData, $privateKey);
        // get r
        $r = $signature->getR();
        // get s
        $s = $signature->getS();
        // get recovery param
        $v = $signature->getRecoveryParam();
        // encode to hex
        $serializer = new HexSignatureSerializer();
        $signatureString = $serializer->serialize($signature);
        // or you can call toHex
        $signatureString = $signature->toHex();

        if (strlen((string)$v) == 1) {
            $v = "0" . $v;
        }
        return $signatureString . $v;
    }

    /**
     * Verify whether $this sdk successfully connects the chain.
     *
     * @return IsConnected or not
     */
    public function isConnected()
    {
        try {
            $this->getBlockChainSdkObj()->getChainStatus();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getTransactionFees($transactionResult)
    {
        /*
        Get transaction fees
        :param logs: logs from transaction results
        :return: transaction fees
        */
        $transactionFees = [];
        if (!empty($transactionResult['Logs'])) {
            foreach ($transactionResult['Logs'] as $log) {
                if ($log['Name'] == 'TransactionFeeCharged') {
                    $transactionFee = new TransactionFeeCharged();
                    $transactionFee->mergeFromString(base64_decode($log['NonIndexed']));
                    array_push($transactionFees, [
                        'name' => 'transaction_fee_charged',
                        'symbol' => $transactionFee->getSymbol(),
                        'amount' => $transactionFee->getAmount(),
                    ]);
                }
                if ($log['Name'] == 'ResourceTokenCharged') {
                    $resourceTokenFee = new ResourceTokenCharged();
                    $resourceTokenFee->mergeFromString(base64_decode($log['NonIndexed']));
                    array_push($transactionFees, [
                        'name' => 'resource_token_charged',
                        'symbol' => $resourceTokenFee->getSymbol(),
                        'amount' => $resourceTokenFee->getAmount(),
                    ]);
                }
            }
        }
        return $transactionFees;

    }

    /**
     * @param $input
     * @return TransactionFeeResultOutput
     */
    public function calculateTransactionFee($input)
    {
        return $this->getBlockChainSdkObj()->calculateTransactionFee($input);
    }
}
