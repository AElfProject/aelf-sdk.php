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
 *
 */
class SecgCurve
{
    /**
     * @var GmpMathInterface
     */
    private $adapter;

    const NAME_SECP_112R1 = 'secp112r1';
    const NAME_SECP_192K1 = 'secp192k1';
    const NAME_SECP_256K1 = 'secp256k1';
    const NAME_SECP_256R1 = 'secp256r1';
    const NAME_SECP_384R1 = 'secp384r1';

    /**
     * @param GmpMathInterface $adapter
     */
    public function __construct(GmpMathInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @return NamedCurveFp
     */
    public function curve112r1(): NamedCurveFp
    {
        $p = gmp_init('0xDB7C2ABF62E35E668076BEAD208B', 16);
        $a = gmp_init('0xDB7C2ABF62E35E668076BEAD2088', 16);
        $b = gmp_init('0x659EF8BA043916EEDE8911702B22', 16);

        $parameters = new CurveParameters(112, $p, $a, $b);

        return new NamedCurveFp(self::NAME_SECP_112R1, $parameters, $this->adapter);
    }

    /**
     * @param RandomNumberGeneratorInterface $randomGenerator
     * @return GeneratorPoint
     */
    public function generator112r1(RandomNumberGeneratorInterface $randomGenerator = null): GeneratorPoint
    {
        $curve = $this->curve112r1();

        $order = gmp_init('0xDB7C2ABF62E35E7628DFAC6561C5', 16);
        $x = gmp_init('0x09487239995A5EE76B55F9C2F098', 16);
        $y = gmp_init('0xA89CE5AF8724C0A23E0E0FF77500', 16);

        return $curve->getGenerator($x, $y, $order, $randomGenerator);
    }

    /**
     * @return NamedCurveFp
     */
    public function curve192k1(): NamedCurveFp
    {
        $p = gmp_init('0xFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFEFFFFEE37', 16);
        $a = gmp_init(0, 10);
        $b = gmp_init(3, 10);

        $parameters = new CurveParameters(192, $p, $a, $b);

        return new NamedCurveFp(self::NAME_SECP_192K1, $parameters, $this->adapter);
    }

    /**
     * @param RandomNumberGeneratorInterface $randomGenerator
     * @return \Mdanter\Ecc\Primitives\GeneratorPoint
     */
    public function generator192k1(RandomNumberGeneratorInterface $randomGenerator = null): GeneratorPoint
    {
        $curve = $this->curve192k1();

        $order = gmp_init('0xFFFFFFFFFFFFFFFFFFFFFFFE26F2FC170F69466A74DEFD8D', 16);
        $x = gmp_init('0xDB4FF10EC057E9AE26B07D0280B7F4341DA5D1B1EAE06C7D', 16);
        $y = gmp_init('0x9B2F2F6D9C5628A7844163D015BE86344082AA88D95E2F9D', 16);

        return $curve->getGenerator($x, $y, $order, $randomGenerator);
    }

    /**
     * @return NamedCurveFp
     */
    public function curve256k1(): NamedCurveFp
    {
        $p = gmp_init('0xFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFEFFFFFC2F', 16);
        $a = gmp_init(0, 10);
        $b = gmp_init(7, 10);

        $parameters = new CurveParameters(256, $p, $a, $b)