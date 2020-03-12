<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Primitives;

use Mdanter\Ecc\Exception\PointException;
use Mdanter\Ecc\Exception\PointNotOnCurveException;
use Mdanter\Ecc\Math\GmpMathInterface;
use Mdanter\Ecc\Math\ModularArithmetic;

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
 * This class is where the elliptic curve arithmetic takes place.
 * The important methods are:
 * - add: adds two points according to ec arithmetic
 * - double: doubles a point on the ec field mod p
 * - mul: uses double and add to achieve multiplication The rest of the methods are there for supporting the ones above.
 */
class Point implements PointInterface
{
    /**
     * @var CurveFpInterface
     */
    private $curve;

    /**
     * @var GmpMathInterface
     */
    private $adapter;

    /**
     * @var ModularArithmetic
     */
    private $modAdapter;

    /**
     * @var \GMP
     */
    private $x;

    /**
     * @var \GMP
     */
    private $y;

    /**
     * @var \GMP
     */
    private $order;

    /**
     * @var bool
     */
    private $infinity = false;

    /**
     * Initialize a new instance
     *
     * @param GmpMathInterface     $adapter
     * @param CurveFpInterface     $curve
     * @param \GMP                 $x
     * @param \GMP                 $y
     * @param \GMP                 $order
     * @param bool                 $infinity
     *
     * @throws \RuntimeException    when either the curve does not contain the given coordinates or
     *                                      when order is not null and P(x, y) * order is not equal to infinity.
     */
    public function __construct(GmpMathInterface $adapter, CurveFpInterface $curve, \GMP $x, \GMP $y, \GMP $order = null, bool $infinity = false)
    {
        $this->adapter    = $adapter;
        $this->modAdapter = $curve->getModAdapter();
        $this->curve      = $curve;
        $this->x          = $x;
        $this->y          = $y;
        $this->order      = $order !== null ? $order : gmp_init(0, 10);
        $this->infinity   = (bool) $infinity;
        if (! $infinity && ! $curve->contains($x, $y)) {
            throw new PointNotOnCurveException($x, $y, $curve);
        }

        if (!is_null($order)) {
            $mul = $this->mul($order);
            if (!$mul->isInfinity()) {
                throw new PointException("SELF * ORDER MUST EQUAL INFINITY. (" . (string)$mul . " found instead)");
            }
        }
    }

    /**
     * @return GmpMathInterface
     */
    public function getAdapter(): GmpMathInterface
    {
        return $this->adapter;
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Primitives\PointInterface::isInfinity()
     */
    public function isInfinity(): bool
    {
        return (bool) $this->infinity;
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Primitives\PointInterface::getCurve()
     */
    public function getCurve(): CurveFp