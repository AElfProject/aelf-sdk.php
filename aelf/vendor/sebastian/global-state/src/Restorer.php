<?php

namespace Mdanter\Ecc\Math;

/***********************************************************************
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
     ************************************************************************/

/**
 * Implementation of some number theoretic algorithms
 *
 * @author Matyas Danter
 */

use Mdanter\Ecc\Exception\NumberTheoryException;
use Mdanter\Ecc\Exception\SquareRootException;

/**
 * Rewritten to take a MathAdaptor to handle different environments. Has
 * some desireable functions for public key compression/recovery.
 */
class NumberTheory
{
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
        $this->zero = gmp_init(0, 10);
        $this->one = gmp_init(1, 10);
        $this->two = gmp_init(2, 10);
    }

    /**
     * @param \GMP[] $poly
     * @param \GMP[] $polymod
     * @param \GMP $p
     * @return \GMP[]
     */
    public function polynomialReduceMod(array $poly, array $polymod, \GMP $p): array
    {
        $adapter = $this->adapter;

        // Only enter if last value is set, implying count > 0
        if ((($last = end($polymod)) instanceof \GMP) && $adapter->equals($last, $this->one)) {
            $count_polymod = count($polymod);
            while (count($poly) >= $count_polymod) {
                if (!$adapter->equals(end($poly), $this->zero)) {
                    for ($i = 2; $i < $count_polymod + 1; $i++) {
                        $poly[count($poly) - $i] =
                            $adapter->mod(
                                $adapter->sub(
                                    $poly[count($poly) - $i],
                                    $adapter->mul(
                                        end($poly),
                                        $polymod[$count_polymod - $i]
                                    )
                                ),
                                $p
                            );
                    }
                }

                $poly = array_slice($poly, 0, count($poly) - 1);
            }

            return $poly;
        }

        throw new NumberTheoryException('Unable to calculate polynomialReduceMod');
    }

    /**
     * @param \GMP[] $m1
     * @param \GMP[] $m2
     * @param \GMP[] $polymod
     * @param \GMP $p
     * @return \GMP[]
     */
    public function polynomialMultiplyMod(array $m1, array $m2, array $polymod, \GMP $p): array
    {
        $prod = array();
        $cm1 = count($m1);
        $cm2 = count($m2);

        for ($i = 0; $i < $cm1; $i++) {
            for ($j = 0; $j < $cm2; $j++) {
                $index = $i + $j;
                if (!isset($prod[$index])) {
                    $prod[$index] = $this->zero;
                }
                $prod[$index] =
                    $this->adapter->mod(
                        $this->adapter->add(
                            $prod[$index],
                            $this->adapter->mul(
                                $m1[$i],
                                $m2[$j]
                            )
                        ),
          