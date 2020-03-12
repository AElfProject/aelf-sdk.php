<?php
namespace BN;

use \JsonSerializable;
use \Exception;
use \BI\BigInteger;

class BN implements JsonSerializable
{
    public $bi;
    public $red;

    function __construct($number, $base = 10, $endian = null)
    {
        if( $number instanceof BN ) {
            $this->bi = $number->bi;
            $this->red = $number->red;
            return;
        }

        // Reduction context
        $this->red = null;

        if ( $number instanceof BigInteger ) {
            $this->bi = $number;
            return;
        }

        if( is_array($number) )
        {
            $number = call_user_func_array("pack", array_merge(array("C*"), $number));
            $number = bin2hex($number);
            $base = 16;
        }

        if( $base == "hex" )
          