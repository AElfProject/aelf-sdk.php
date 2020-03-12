<?php
/*
 * This file is part of the PHPASN1 library.
 *
 * Copyright © Friedrich Große <friedrich.grosse@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FG\ASN1\Universal;

use Exception;
use FG\ASN1\Exception\ParserException;
use FG\ASN1\Parsable;
use FG\ASN1\Identifier;

class BitString extends OctetString implements Parsable
{
    private $nrOfUnusedBits;

    /**
     * Creates a new ASN.1 BitString object.
     *
     * @param string|int $value Either the hexadecimal value as a string (spaces are allowed - leading 0x is optional) or a numeric value
     * @param int $nrOfUnusedBits the number of unused bits in 