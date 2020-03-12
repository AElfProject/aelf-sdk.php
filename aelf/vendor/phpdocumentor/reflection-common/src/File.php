<?php

namespace Elliptic\Curve;

use Elliptic\Curve\MontCurve\Point;
use Elliptic\Utils;
use BN\BN;

class MontCurve extends BaseCurve
{
    public $a;
    public $b;
    public $i4;
    public $a24;

    function __construct($conf)
    {
        parent::__construct("mont", $conf);

        $this->a = (new BN($conf["a"], 16))->toRed($this->red);
        $this->b = (new BN($conf["b"], 16))->toRed($this->red);
        $this->i4 = (new BN(4))->toRed($this->red)->redInvm();
        $this->a24 = $this->i4->redMul($this->a->redAdd($this->two));
    }

    public function validate($point)
    {
        $x = $point->normalize()->x;
        $x2 = $x->redSqr();
        $rhs = $x2->redMul($x)->redAdd($x2->redMul($this->a))->redAdd($x);
        $y = $rhs->redSqr();

        return $y->r