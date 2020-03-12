<?php

declare(strict_types=1);

namespace BitWasp\Buffertools\Tests\Types;

use BitWasp\Buffertools\ByteOrder;
use BitWasp\Buffertools\Tests\BinaryTest;
use BitWasp\Buffertools\Types\UintInterface;
use BitWasp\Buffertools\Types\Uint8;
use BitWasp\Buffertools\Types\Uint16;
use BitWasp\Buffertools\Types\Uint32;
use BitWasp\Buffertools\Types\Uint64;
use BitWasp\Buffertools\Types\Uint128;
use BitWasp\Buffertools\Types\Uint256;
use BitWasp\Buffertools\Buffer;
use BitWasp\Buffertools\Buffertools;
use BitWasp\Buffertools\Parser;

class UintSetTest extends BinaryTest
{

    /**
     * @param int $bitSize
     * @param int $byteOrder
     * @return array
     */
    private function generateSizeBasedTests(int $bitSize, int $byteOrder)
    {
        $halfPos = gmp_strval(gmp_init(str_pad('7', $bitSize / 4, 'f', STR_PAD_RIGHT), 16), 10);
        $maxPos = gmp_strval(gmp_init(str_pad('', $bitSize / 4, 'f', STR_PAD_RIGHT), 16), 10);

        $test = function ($integer) use ($bitSize, $byteOrder) {
            $hex = str_pad(gmp_strval(gmp_init($integer, 10), 16), $bitSize / 4, '0', STR_PAD_LEFT);

            if ($byteOrder == ByteOrder::LE) {
                $hex = Buffertools::flipBytes(Buffer::hex($hex))->getHex();
            }
            return [
                $integer,
                $hex,
                null
            ];
        };

        return [
            $test(0),
            $test(1),
            $test($halfPos),
            $test($maxPos)
        ];
    }

    /**
     * @return UintInterface[]
     */
    public function getUintClasses(): array
    {
        return [
            new Uint8(),
            new Uint16(),
            new Uin