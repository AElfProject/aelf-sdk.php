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
use FG\ASN1\Base128;
use FG\ASN1\OID;
use FG\ASN1\ASNObject;
use FG\ASN1\Parsable;
use FG\ASN1\Identifier;
use FG\ASN1\Exception\ParserException;

class ObjectIdentifier extends ASNObject implements Parsable
{
    protected $subIdentifiers;
    protected $value;

    public function __construct($value)
    {
        $this->subIdentifiers = explode('.', $value);
        $nrOfSubIdentifiers = count($this->subIdentifiers);

        for ($i = 0; $i < $nrOfSubIdentifiers; $i++) {
            if (is_numeric($this->subIdentifiers[$i])) {
                // enforce the integer type
                $this->subIdentifiers[$i] = intval($this->subIdentifiers[$i]);
            } else {
                throw new Exception("[{$value}] is no valid object identifier (sub identifier ".($i + 1).' is not numeric)!');
            }
        }

        // Merge the first to arcs of the OID registration tree (per ASN definition!)
        if ($nrOfSubIdentifiers >= 2) {
            $this->subIdentifiers[1] = ($this->subIdentifiers[0] * 40) + $this->subIdentifiers[1];
            unset($this->subIdentifiers[0]);
        }

        $this->value = $value;
    }

    public function getContent()
    {
        return $this->value;
    }

    public function getType()
    {
        return Identifier::OBJECT_IDENTIFIER;
    }

    protected function calculateContentLength()
    {
        $length = 0;
        foreach ($this->subIdentifiers as $subIdentifier) {
            do {
                $subIdentifier = $subIdentifier >> 7;
                $length++;
            } while ($subIdentifier > 0);
        }

        return $length;
    }

    protected function getEncodedValue()
    {
        $encodedValue = '';
        foreach ($this->subIdentifiers a