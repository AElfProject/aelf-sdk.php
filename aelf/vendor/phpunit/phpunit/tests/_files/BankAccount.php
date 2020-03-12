<?php

declare(strict_types=1);

namespace BitWasp\Buffertools\Types;

use BitWasp\Buffertools\ByteOrder;
use BitWasp\Buffertools\Parser;

class VarInt extends AbstractType
{
    private $formatUint8 = "C";
    private $formatUint16LE = "v";
    private $formatUint32LE = "V";
    private $formatUint64LE = "P";

    /**
     * @param \GMP $integer
     * @return array
     * @deprecated
     */
    public function solveWriteSize(\GMP $integer)
    {
        if (gmp_cmp($integer, gmp_pow(gmp_init(2), 16)) < 0) {
            return [new Uint16(ByteOrder::LE), 0xfd];
        } else if (gmp_cmp($integer, gmp_pow(gmp_init(2), 32)) < 0) {
            return [new Uint32(ByteOrder::LE), 0xfe];
        } else if (gmp_cmp($integer, gmp_pow(gmp_init(2), 64)) < 0) {
            return [new Uint64(ByteOrder::LE), 0xff];
        } else {
            throw new \InvalidArgumentException('Integer too large, exceeds 64 bit');
        }
    }

    /**
     * @param \GMP $givenPrefix
     * @return UintInterface[]
     * @throws \InvalidArgumentException
     * @deprecated
     */
    public function solveReadSize(\GMP $givenPrefix)
    {
        if (gmp_cmp($givenPrefix, 0xfd) === 0) {
            return [new Uint16(ByteOrder::LE)];
        } else if (gmp_cmp($givenPrefix, 0xfe) === 0) {
            return [new Uint32(ByteOrder::LE)];
        } else if (gmp_cmp($givenPrefix, 0xff) === 0) {
            return [new Uint64(ByteOrder::LE)];
        }

        throw new \InvalidArgumentException('Unknown varint prefix');
    }

    /**
     * 