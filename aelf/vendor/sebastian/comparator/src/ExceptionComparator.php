<?php

declare(strict_types=1);

namespace BitWasp\Bitcoin\Serializer\Key\PrivateKey;

use BitWasp\Bitcoin\Base58;
use BitWasp\Bitcoin\Bitcoin;
use BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface;
use BitWasp\Bitcoin\Crypto\EcAdapter\Key\PrivateKeyInterface;
use BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Key\PrivateKeySerializerInterface;
use BitWasp\Bitcoin\Exceptions\Base58ChecksumFailure;
use BitWasp\Bitcoin\Exceptions\InvalidPrivateKey;
use BitWasp\Bitcoin\Network\NetworkInterface;
use BitWasp\Buffertools\Buffer;

class WifPrivateKeySerializer
{
    /**
     * @var PrivateKeySerializerInterface
     */
    private $keySerializer;

    /**
     * @param PrivateKeySerializerInterface $serializer
     */
    public function __construct(PrivateKeySerializerInterface $serializer)
    {
        $this->keySerializer = $serializer;
    }

    /**
     * @param NetworkInterface $network
     * @param PrivateKeyInterface $privateKey
     * @return string
     * @throws \Exception
     */
    public function serialize(NetworkInterface $network, PrivateKeyInterface $privateKey): string
    {
        $prefix = pack("H*", $network->getPrivByte());
        if ($privateKey->isCompressed()) {
            $ending = "\x01";
      