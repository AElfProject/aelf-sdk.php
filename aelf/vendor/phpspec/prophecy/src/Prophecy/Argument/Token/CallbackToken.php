<?php

namespace StephenHill;

use InvalidArgumentException;

class BCMathService implements ServiceInterface
{
    /**
     * @var string
     * @since v1.1.0
     */
    protected $alphabet;

    /**
     * @var int
     * @since v1.1.0
     */
    protected $base;

    /**
     * Constructor
     *
     * @param string $alphabet optional
     * @since v1.1.0
     */
    public function __construct($alphabet = null)
    {
        // Handle null alphabet
        if (is_null($alphabet) === true) {
            $alphabet = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';
        }

        // Type validation
        if (is_string($alphabet) === false) {
            throw new InvalidArgumentException('Argument $alphabet must be a string.');
        }

        // The alphabet must contain 58 characters
        if (strlen($alphabet) !== 58) {
            throw new InvalidArgumentException('Argument $alphabet must contain 58 characters.');
        }

        $this->alphabet = $alphabet;
        $this->base = strlen($alphabet);
    }
    /**
     * Encode a string into base58.
     *
     * @param  string $string The string you wish to encode.
     * @since Release v1.1.0
     * @return string The Base58 encoded string.
     */
    public function encode($string)
    {
        // Type validation
        if (is_string($string) === false) {
            throw new InvalidArgumentException('Argument $string must be a string.');
        }

        // If the string is empty, then the encoded stri