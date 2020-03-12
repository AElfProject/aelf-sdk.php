<?php

namespace Elliptic\Curve;

use Elliptic\Utils;
use \Exception;
use BN\BN;

abstract class BaseCurve
{
    public $type;
    public $p;
    public $red;
    public $zero;
    public $one;
    public $two;
    public $n;
    public $g;
    protected $_wnafT1;
    protected $_wnafT2;
    protected $_wnafT3;
    protected $_wnafT4;
    public $redN;
    public $_maxwellTrick;

    function __construct($type, $conf)
    {
        $this->type = $type;
        $this->p = new BN($conf["p"], 16);

        //Use Montgomery, when there is no fast reduction for the prime
        $this->red = isset($conf["prime"]) ? BN::red($conf["prime"]) : BN::mont($this->p);

        //Useful for many curves
        $this->zero = (new BN(0))->toRed($this->red);
        $this->one = (new BN(1))->toRed($this->red);
        $this->two = (new BN(2))->toRed($this->red);

        //Curve configuration, optional
        $th