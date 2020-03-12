<?php
/*
 * This file is part of the PHPASN1 library.
 *
 * Copyright © Friedrich Große <friedrich.grosse@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FG\ASN1;

use FG\ASN1\Exception\ParserException;
use FG\ASN1\Universal\BitString;
use FG\ASN1\Universal\Boolean;
use FG\ASN1\Universal\Enumerated;
use FG\ASN1\Universal\GeneralizedTime;
use FG\ASN1\Universal\Integer;
use FG\ASN1\Universal\NullObject;
use FG\ASN1\Universal\ObjectIdentifier;
use FG\ASN1\Universal\RelativeObjectIdentifier;
use FG\ASN1\Universal\OctetString;
use FG\ASN1\Universal\Sequence;
use FG\ASN1\Universal\Set;
use FG\ASN1\Universal\UTCTime;
use FG\ASN1\Universal\IA5String;
use FG\ASN1\Universal\PrintableString;
use FG\ASN1\Universal\NumericString;
use FG\ASN1\Universal\UTF8String;
use FG\ASN1\Universal\UniversalString;
use FG\ASN1\Universal\CharacterString;
use FG\ASN1\Universal\GeneralString;
use FG\ASN1\Universal\VisibleString;
use FG\ASN1\Universal\GraphicString;
use FG\ASN1\Universal\BMPString;
use FG\ASN1\Universal\T61String;
use FG\ASN1\Universal\ObjectDescriptor;
use FG\Utility\BigInteger;
use LogicException;

/**
 * Class ASNObject is the base class for all concrete ASN.1 objects.
 */
abstract class ASNObject implements Parsable
{
    private $contentLength;
    private $nrOfLengthOctets;

    /**
     * Must return the number of octets of the content part.
     *
     * @return int
     */
    abstract protected function calculateContentLength();

    /**
     * Encode the object using DER encoding.
     *
     * @see http://en.wikipedia.org/wiki/X.690#DER_encoding
     *
     * @return string the binary representation of an objects value
     */
    abstract protected function getEncodedValue();

    /**
     * Return the content of this object in a non encoded form.
     * This can be used to print the value in human readable form.
     *
     * @return mixed
     */
    abstract public function getContent();

    /**
     * Return the object type octet.
     * This should use the class constants of Identifier.
     *
     * @see Identifier
     *
     * @return int
     */
    abstract public function getType();

    /**
     * Returns all identifier octets. If an inheriting class models a tag with
     * the long form identifier format, it MUST reimplement this method to
     * return all octets of the identifier.
     *
     * @throws LogicException If the identifier format is long form
     *
     * @return string Identifier as a set of octets
     */
    public function getIdentifier()
    {
        $firstOctet = $this->getType();

        if (Identifier::isLongForm($firstOctet)) {
            throw new LogicException(sprintf('Identifier of %s uses the long form and must therefor override "ASNObject::getIdentifier()".', get_class($this)));
        }

        return chr($firstOctet);
    }

    /**
     * Encode this object using DER encoding.
     *
     * @return string the full binary representation of the complete object
     */
    public function getBinary()
    {
        $result  = $this->getIdentifier();
        $result .= $this->createLengthPart();
        $result .= $this->getEncodedValue();

        return $result;
    }

    private function createLengthPart()
    {
        $contentLength = $this->getContentLength();
        $nrOfLengthOctets = $this->getNumberOfLengthOctets($contentLength);

        if ($nrOfLengthOctets == 1) {
            return chr($contentLength);
        } else {
            // the first length octet determines the number subsequent length octets
            $lengthOctets = chr(0x80 | ($nrOfLengthOctets - 1));
            for ($shiftLength = 8 * ($nrOfLengthOctets - 2); $shiftLength >= 0; $shiftLength -= 8) {
                $lengthOctets .= chr($contentLength >> $shiftLength);
            }

            return $lengthOctets;
        }
    }

    protected function getNumberOfLengthOctets($contentLength = null)
    {
        if (!isset($this->nrOfLengthOctets)) {
            if ($contentLength == null) {
                $contentLength = $this->getContentLength();
            }

            $this->nrOfLengthOctets = 1;
            if ($contentLength > 127) {
                do { // long form
                    $this->nrOfLengthOctets++;
                    $contentLength = $contentLength >> 8;
                } while ($contentLength > 0);
            }
        }

        return $this->nrOfLengthOctets;
    }

    protected function getContentLength()
    {
        if (!isset($this->contentLength)) {
            $this->contentLength = $this->calculateContentLength();
        }

        return $this->contentLength;
    }

    protected function setContentLength($newContentLength)
    {
        $this->contentLength = $newContentLength;
        $this->getNumberOfLengthOctets($newContentLength);
    }

    /**
     * Returns the length of the whole object (including the identifier and length octets).
     */
    public function getObjectLength()
    {
        $nrOfIdentifierOctets = strlen($this->getIdentifier());
        $contentLength = $this->getContentLength();
        $nrOfLengthOctets = $this->getNumberOfLengthOctets($contentLength);

        return $nrOfIdentifierOctets + $nrOfLengthOctets + $contentLength;
    }

    public function __toString()
    {
        return $this->getContent();
    }

    /**
     * Returns the name of the ASN.1 Type of this object.
     *
     * @see Identifier::getName()
     */
    public function getTypeName()
    {
        return Identifier::getName($this->getType());
    }

    /**
     * @param string $binaryData
     * @param int $offsetIndex
     *
     * @throws ParserException
     *
     * @return \FG\ASN1\ASNObject
     */
    public static function fromBinary(&$binaryData, &$offsetIndex = 0)
    {
        if (strlen($binaryData) <= $offsetIndex) {
            throw new ParserException('Can not parse binary from data: Offset index larger than input size', $offsetIndex);
        }

        $identifierOctet = ord($binaryData[$offsetIndex]);
        if (Identifier::isContextSpecificClass($identifierOctet) && Identifier::isConstructed($identifierOctet)) {
            return ExplicitlyTaggedObject::fromBinary($binaryData, $offsetIndex);
        }

        switch ($identifierOctet) {
            case Identifier::BITSTRING:
                return BitString::fromBinary($binaryData, $offsetIndex);
            case Identifier::BOOLEAN:
                return Boolean::fromBinary($binaryData, $offsetIndex);
            case Identifier::ENUMERATED:
                return Enumerated::fromBinary($binaryData, $offsetIndex);
            case Identifier::INTEGER:
         