<?php

declare(strict_types=1);

namespace BitWasp\Buffertools\Types;

use BitWasp\Buffertools\Buffer;
use BitWasp\Buffertools\ByteOrder;
use BitWasp\Buffertools\Parser;

abstract class AbstractSignedInt extends AbstractType implements SignedIntInterface
{
    /**
     * @param int $byteOrder
     */
    public function __construct(int $byteOrder = ByteOrder::BE)
    {
        parent::__construct($byteOrder);
    }

    /**
     * @param int|string $integer
     * @return string
     */
    public function writeBits($integer): string
    {
        return str_pad(
            gmp_strval(gmp_init($integer, 10), 2),
            $this->getBitSize(),
            '0',
            STR_PAD_LEFT
        );
    }

    /**
     * @param Parser $parser
     * @return int|string
     * @throws \BitWasp\Buffertools\Exceptions\ParserOutOfRange
     * @throws \Exception
     */
    public function readBits(Parser $parser)
    {
        $bitSize = $this->getBitSize();
        $byteSize = $bitSize / 8;

        $bytes = $parser->readBytes($byteSize);
        $bytes = $this->isBigEndian() ? $bytes : $bytes->flip();
        $chars = $bytes->getBinary();

        $offsetIndex = 0;
        $isNegative = (ord($chars[$offsetIndex]) & 0x80) != 0x00;
        $number = gmp_init(ord($chars[$offsetIndex++])