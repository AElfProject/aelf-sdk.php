<?php

declare(strict_types=1);

namespace BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Serializer\Signature;

use BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface;
use BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Adapter\EcAdapter;
use BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Signature\Signature;
use BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Signature\DerSignatureSerializerInterface;
use BitWasp\Bitcoin\Crypto\EcAdapter\Signature\SignatureInterface;
use BitWasp\Bitcoin\Serializer\Types;
use BitWasp\Buffertools\Buffer;
use BitWasp\Buffertools\BufferInterface;
use BitWasp\Buffertools\Buffertools;
use BitWasp\Buffertools\Exceptions\ParserOutOfRange;
use BitWasp\Buffertools\Parser;

class DerSignatureSerializer implements DerSignatureSerializerInterface
{
    /**
     * @var EcAdapter
     */
    private $ecAdapter;

    /**
     * @var \BitWasp\Buffertools\Types\VarString
     */
    private $varstring;

    /**
     * @param EcAdapter $adapter
     */
    public function __construct(EcAdapter $adapter)
    {
        $this->ecAdapter = $adapter;
        $this->varstring = Types::varstring();
    }

    /**
     * @return EcAdapterInterface
     */
    public function getEcAdapter(): EcAdapterInterface
    {
        return $this->ecAdapter;
    }

    /**
     * @param SignatureInterface $signature
     * @return BufferInterface
     * @throws \Exception
     */
    public function serialize(SignatureInterface $signature): BufferInterface
    {
        // Ensure that the R and S hex's are of even length
        $rBin = gmp_export($signature->getR(), 1, GMP_MSW_FIRST | GMP_BIG_ENDIAN);
        $sBin = gmp_export($signature->getS(), 1, GMP_MSW_FIRST | GMP_BIG_ENDIAN);

        // Pad R and S if their highest bit is flipped, ie,
        // they are negative.
        if ((ord($rBin[0]) & 0x80) === 0x80) {
            $rBin = "\x00$rBin";
       