<?php

declare(strict_types=1);

namespace BitWasp\Bitcoin\Math;

use Mdanter\Ecc\Math\GmpMath;
use Mdanter\Ecc\Util\NumberSize;

class Math extends GmpMath
{

    /**
     * @param \GMP $integer
     * @return bool
     */
    public function isEven(\GMP $integer): bool
    {
        return $this->cmp($this->mod($integer, gmp_init(2)), gmp_init(0)) === 0;
    }

    /**
     * @param \GMP $int
     * @param \GMP $otherInt
     * @return \GMP
     */
    public function bitwiseOr(\GMP $int, \GMP $otherInt): \GMP
    {
        return gmp_or($int, $otherInt);
    }

    /**
     * Similar to gmp_div_qr, return a tuple containing the
     * result and the remainder
     *
     * @param \GMP $dividend
     * @param \GMP $divisor
     * @return array
     */
    public function divQr(\GMP $dividend, \GMP $divisor): array
    {
        // $div = n / q
        $div = $this->div($dividend, $divisor);
        // $remainder = n - (n / q) * q
        $remainder = $this->sub($dividend, $this->mul($div, $divisor));
        return [$div, $remainder];
    }

    /**
     * @param int $compact
     * @param bool|false $isNegative
     * @param bool|false $isOverflow
     * @return \GMP
     */
    public function decodeCompact($compact, &$isNegative, &$isOverflow): \GMP
    {
        if ($compact < 0 || $compact > pow(2, 32) - 1) {
            throw new \RuntimeException('Compact integer must be 32bit');
        }

        $compact = gmp_init($compact, 10);
        $size = $this->rightShift($compact, 24);
        $word = $this->bitwiseAnd($compact, gmp_init(0x007fffff, 10));
        if ($this->cmp($size, gmp_init(3)) <= 0) {
            $positions = (int) $this->toString($this->mul(gmp_init(8, 10), $this->sub(gmp_init(3, 10), $size)));
            $word = $this->rightShift($word, $positions);
        } else {
            $positions = (int) $this->toString($this->mul(gmp_init(8, 10), $this->sub($size, gmp_init(3, 10))));
            $word = $this->leftShift($word, $positions);
        }

        // isNegative: $word !== 0 && $uint32 & 0x00800000 !== 0
        // isOverflow: $word !== 0 && (($size > 34) || ($word > 0xff && $size > 33) || ($word > 0xffff && $size > 32))
        $zero = gmp_init(0);
        $isNegative = ($this->cmp($word, $zero) !== 0) && ($this->cmp($this->bitwiseAnd($compact, gmp_init(0x00800000)), $zero) === 1);
        $isOverflow = $this->cmp($word, $zero) !== 0 && (
                ($this->cmp($size, gmp_init(34, 10)) > 0)
                || ($this->cmp($word, gmp_init(0xff, 10)) > 0 && $this->cmp($size, gmp_init(33, 10)) > 0)
                || ($this->cmp($word, gmp_init(0xffff, 10)) > 0 && $this->cmp($size, gmp_init(32, 10)) > 0)
            );

        return $word;
    }

    /**
     * @param \GMP $integer
     * @return \GMP
     */
    public 