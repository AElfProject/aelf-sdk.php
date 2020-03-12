<?php

namespace Mdanter\Ecc\Math;

class ModularArithmetic
{
    /**
     * @var GmpMathInterface
     */
    private $adapter;

    /**
     * @var \GMP
     */
    private $modulus;

    /**
     * @param GmpMathInterface $adapter
     * @param \GMP $modulus
     */
    public function __construct(GmpMathInterface $adapter, \GMP $modulus)
    {
        $this->adapter = $adapter;
        $this->modulus = $modulus;
    }

    /**
     * @param \GMP $augend
     * @param \GMP $addend
     * @return \GMP
     */
    public function add(\GMP $augend, \GMP $addend): \GMP
    {
        return $