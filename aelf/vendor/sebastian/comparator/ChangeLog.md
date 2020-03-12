<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Primitives;

use Mdanter\Ecc\Crypto\Key\PrivateKeyInterface;
use Mdanter\Ecc\Crypto\Key\PublicKeyInterface;
use Mdanter\Ecc\Math\GmpMathInterface;
use Mdanter\Ecc\Crypto\Key\PrivateKey;
use Mdanter\Ecc\Crypto\Key\PublicKey;
use Mdanter\Ecc\Random\RandomGeneratorFactory;
use Mdanter\Ecc\Random\RandomNumberGeneratorInterface;

/**
 * Curve point from which public and private keys can be derived.
 */
class GeneratorPoint extends Point
{
    /**
     * @var RandomNumberGeneratorInterface
     */
    private $generator;

    /**
     * @param GmpMathInterface               $adapter
     * @param CurveFpInterface               $curve
     * @param \GMP                           $x
     * @param \GMP                           $y
     * @param \GMP                           $order
     * @param RandomNumberGeneratorInterface $generator
     */
    public function __construct(
        GmpMathInterface $adapter,
        CurveFpInterfa