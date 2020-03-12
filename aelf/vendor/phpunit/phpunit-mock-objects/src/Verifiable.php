<?php

declare(strict_types=1);

namespace BitWasp\Bitcoin\Key\Deterministic\Slip132;

class PrefixRegistry
{
    /**
     * @var array
     */
    private $registry = [];

    /**
     * PrefixRegistry constructor.
     * @param array $registry
     */
    public function __construct(array $registry)
    {
        foreach ($registry as $scriptType => $prefixes) {
            if (!is_string($scriptType)) {
                throw new \InvalidArgumentException("Expecting script type as key");
            }
            if (count($prefixes) !== 2) {
                throw new \InvalidArgumentException("Expecting two BIP32 prefixes");
            }
            // private