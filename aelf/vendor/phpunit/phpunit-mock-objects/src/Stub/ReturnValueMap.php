<?php

declare(strict_types=1);

namespace BitWasp\Bitcoin\Key\Deterministic\Slip132;

use BitWasp\Bitcoin\Bitcoin;
use BitWasp\Bitcoin\Key\Deterministic\HdPrefix\ScriptPrefix;
use BitWasp\Bitcoin\Key\KeyToScript\ScriptDataFactory;
use BitWasp\Bitcoin\Key\KeyToScript\KeyToScriptHelper;

class Slip132
{
    /**
     * @var KeyToScriptHelper
     */
    private $helper;

    public function __construct(KeyToScriptHelper $helper = null)
    {
        $this->helper = $helper ?: new KeyToScriptHelper(Bitcoin::getEcAdapter());
    }

    /**
     * @param PrefixRegistry $registry
     * @param ScriptDataFactory $factory
     * @return ScriptPrefix
     * @throws \BitWasp\Bitcoin\Exceptions\InvalidNetworkParameter
     */
    private function loadPrefix(PrefixRegistry $registry, ScriptDataFactory $factory): ScriptPrefix
    {
        list ($private, $public) = $registry->getPrefixes($factory->getScriptType());
        return new ScriptPrefix($factory, $private, $public);
    }

    /**
     * xpub on bitcoin
     * @param PrefixRegistry $registry
     * @return ScriptPrefix
     * @throws \B