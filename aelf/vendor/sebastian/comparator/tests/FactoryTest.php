<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Curves;

use Mdanter\Ecc\Math\GmpMathInterface;
use Mdanter\Ecc\Primitives\CurveParameters;
use Mdanter\Ecc\Primitives\GeneratorPoint;
use Mdanter\Ecc\Random\RandomNumberGeneratorInterface;

/**
 * *********************************************************************
 * Copyright (C) 2012 Matyas Danter
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES
 * OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,
 * ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
 * ***********************************************************************
 */

/**
 * This class encapsulates the NIST recommended curves
 * - fields are Mersenne primes, i.e.
 * for some p, Mersenne_prine = 2^p - 1
 */
class NistCurve
{

    const NAME_P192 = 'nistp192';
    const NAME_P224 = 'nistp224';
    const NAME_P256 = 'nistp256';
    const NAME_P384 = 'nistp384';
    const NAME_P521 = 'nistp521';

    /**
     * @var GmpMathInterface
     */
    private $adapter;

    /**
     * @param GmpMathInterface $adapter
     */
    public function __construct(GmpMathInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Returns an NIST P-192 curve.
     *
     * @return NamedCurveFp
     */
    public function curve192(): NamedCurveFp
    {
        $p = gmp_init('6277101735386680763835789423207666416083908700390324961279', 10);
        $b = gmp_init('64210519e59c80e70fa7e9ab72243049feb8deecc146b9b1', 16);

        $parameters = new CurveParameters(192, $p, gmp_init('-3', 10), $b);

        return new NamedCurveFp(self::NAME_P192, $parameters, $this->adapter);
    }

    /**
     * Returns an NIST P-192 generator.
     *
     * @param  RandomNumberGeneratorInterface $randomGenerator
     * @return GeneratorPoint
     */
    public function generator192(RandomNumberGeneratorInterface $randomGenerator = null): GeneratorPoint
    {
        $curve = $this->curve192();
        $order = gmp_init('6277101735386680763835789423176059013767194773182842284081', 10);

        $x = gmp_init('188da80eb03090f67cbf20eb43a18800f4ff0afd82ff1012', 16);
        $y = gmp_init('07192b95ffc8da78631011ed6b24cdd573f977a11e794811', 16);

        return $curve->getGenerator($x, $y, $order, $randomGenerator);
    }

    /**
     * Returns an NIST P-224 curve
     *
     * @return NamedCurveFp
     */
    public function curve224(): NamedCurveFp
    {
        $p = gmp_init('26959946667150639794667015087019630673557916260026308143510066298881', 10);
        $b = gmp_init('b4050a850c04b3abf54132565044b0b7d7bfd8ba270b39432355ffb4', 16);

        $parameters = new CurveParameters(224, $p, gmp_init(-3, 10), $b);

        return new NamedCurveFp(self::NAME_P224, $parameters, $this->adapter);
    }

    /**
     * Returns an NIST P-224 generator.
     *
     * @param  RandomNumberGeneratorInterface $randomGenerator
     * @return GeneratorPoint
     */
    public function generator224(RandomNumberGeneratorInterface $randomGenerator = null): GeneratorPoint
    {
        $curve = $this->curve224();
        $order = gmp_init('26959946667150639794667015087019625940457807714424391721682722368061', 10);

        $x = gmp_init('b70e0cbd6bb4bf7f321390b94a03c1d356c21122343280d6115c1d21', 16);
        $y = gmp_init('bd376388b5f723fb4c22dfe6cd4375a05a07476444d5819985007e34', 16);

        return $curve->getGenerator($x, $y, $order, $randomGenerator);
    }

    /**
     * Returns an NIST P-256 curve.
     *
     * @return NamedCurveFp
     */
    public function curve256(): NamedCurveFp
    {
        $p = gmp_init('115792089210356248762697446949407573530086143415290314195533631308867097853951', 10);
        $b = gmp_init('0x5ac635d8aa3a93e7b3ebbd55769886bc651d06b0cc53b0f63bce3c3e27d2604b', 16);

        $parameters = new CurveParameters(256, $p, gmp_init(-3, 10), $b);

        return new NamedCurveFp(self::NAME_P256, $parameters, $this->adapter);
    }

    /**
     * Returns an NIST P-256 generator.
     *
     * @param  RandomNumberGeneratorInterface $randomGenerator
     * @return GeneratorPoint
     */
    public function generator256(RandomNumberGeneratorInterface $randomGenerator = null): GeneratorPoint
    {
        $curve = $this->curve256();
        $order = gmp_init('115792089210356248762697446949407573529996955224135760342422259061068512044369', 10);

        $x = gmp_init('0x6b17d1f2e12c4247f8bce6e563a440f277037d812deb33a0f4a13945d898c296', 16);
 