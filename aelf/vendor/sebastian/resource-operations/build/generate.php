<?php
/*
 * This file is part of the PHPASN1 library.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FG\Utility;

/**
 * Class BigIntegerBcmath
 * Integer representation of big numbers using the bcmath library to perform large operations.
 * @package FG\Utility
 * @internal
 */
class BigIntegerBcmath extends BigInteger
{
    protected $_str;

    public function __clone()
    {
        // nothing needed to copy
    }

    protected function _fromString($str)
    {
        $this->_str = (string)$str;
    }

    protected function _fromInteger($integer)
    {
        $this->_str = (string)$integer;
    }

    public function __toString()
    {
        return $this->_str;
    }

    public function toInteger()
    {
        if ($this->compare(PHP_INT_MAX) > 0 || $this->compare(PHP_INT_MIN) < 0) {
            throw new \OverflowException(sprintf('Can not represent %s as integer.', $this->_str));
        }
        return (int)$this->_str;
    }

    public function isNegative()
    {
        return bccomp($this->_str, '0', 0) < 0;
    }

    protected function _unwrap($number)
    {
        if ($number instanceof self) {
            return $number->_str;
        }
        re