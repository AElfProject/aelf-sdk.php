<?php

declare(strict_types=1);

namespace BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Signature;

use BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Adapter\EcAdapter;
use BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Serializer\Signature\CompactSignatureSerializer;
use BitWasp\Buffertools\BufferInterface;

class CompactSignature extends Signature implements CompactSignatureInterface
{
    /**
     * @var EcAdapter
     */
    private $ecAdapter;

    /**
     * @var int|string
     */
    private $recid;

    /**
     * @var bool
     */
    private $compressed;

    /**
     * @param EcAdapter $adapter
     * @param \GMP $r
     * @param \GMP $s
     * @param int $recid
     * @param bool $compressed
     */
    public function __construct(EcAdapter $adapter, \GMP $r, \GMP $s, int $recid, bool $compressed)
    {