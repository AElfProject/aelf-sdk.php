<?php

namespace Elliptic\Curve\BaseCurve;

use Elliptic\Utils;

abstract class Point
{
    public $curve;
    public $type;
    public $precomputed;

    function __construct($curve, $type)
    {
        $this->curve = $curve;
        $this->type = $type;
        $this->precomputed = null;
    }

    abstract public function eq($other);

    public function validate() {
        return $this->curve->validate($this);
    }

    public function encodeCompressed($enc) {
        return $this->encode($enc, true);
    }

    public function encode($enc, $compact = false) {
        return Utils::encode($this->_encode($compact), $enc);
    }

    protected function _encode($compact)
    {
        $len = $this->curve->p->byteLength();
        $x = $this->getX()->toArray("be", $len);

        if( $compact )
        {
            array_unshift($x, ($this->getY()->isEven() ? 0x02 : 0x03));
            return $x;
        }

        return array_merge(array(0x04), $x, $this->getY()->toArray("be", $len));
    }

    public function precompute($power = null)
   