<?php

declare(strict_types=1);

namespace BitWasp\Bitcoin\Serializer\Key\HierarchicalKey;

use BitWasp\Buffertools\BufferInterface;

class RawKeyParams
{
    /**
     * @var string
     */
    private $prefix;

    /**
     * @var int
     */
    private $depth;

    /**
     * @var int
     */
    private $parentFpr;

    /**
     * @var int
     */
    private $sequence;

    /**
     * @var BufferInterface
     */
    private $chainCode;

    /**
     * @var BufferInterface
     */
    private $keyData;

    /**
     * RawKeyParams constructor.
     * @param string $prefix
     * @param int $depth
     * @param int $parentFingerprint
     * @param int $sequence
     * @param BufferInterface $chainCode
     * @param