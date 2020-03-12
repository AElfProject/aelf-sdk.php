<?php

declare(strict_types=1);

namespace BitWasp\Bitcoin\Network;

interface NetworkInterface
{
    /**
     * Return a byte for the networks regular version
     *
     * @return string
     */
    public function getAddressByte(): string;

    /**
     * Return a address prefix length in bytes
     *
     * @return int
     */
    public function getAddressPrefixLength(): int;

    /**
     * Return the string that binds address signed messages to
     * this network
     *
     * @return string
     */
    public function getSignedMessageMagic(): string;

    /**
     * Returns the prefix f