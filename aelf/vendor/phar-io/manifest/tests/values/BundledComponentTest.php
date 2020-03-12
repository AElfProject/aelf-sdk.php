<?php

declare(strict_types=1);

namespace BitWasp\Bitcoin\Network;

use BitWasp\Bitcoin\Exceptions\InvalidNetworkParameter;
use BitWasp\Bitcoin\Exceptions\MissingBase58Prefix;
use BitWasp\Bitcoin\Exceptions\MissingBech32Prefix;
use BitWasp\Bitcoin\Exceptions\MissingBip32Prefix;
use BitWasp\Bitcoin\Exceptions\MissingNetworkParameter;

class Network implements NetworkInterface
{
    const BECH32_PREFIX_SEGWIT = "segwit";

    const BASE58_ADDRESS_P2PKH = "p2pkh";
    const BASE58_ADDRESS_P2SH = "p2sh";
    const BASE58_WIF = "wif";
    const BIP32_PREFIX_XPUB = "xpub";
    const BIP32_PREFIX_XPRV = "xprv";

    /**
     * @var array map of base58 address type to byte
     */
    protected $base58PrefixMap = [];

    /**
     * @var array map of bech32 address type to HRP
     */
    protected $bech32PrefixMap = [];

    /**
     * @var array map of bip32 type to bytes
     */
    protected $bip32PrefixMap = [];

    /**
     * @var array map of bip32 key type to script type
     */
    protected $bip32ScriptTypeMap = [];

    /**
     * @var string - message prefix for bitcoin signed messages
     */
    protected $signedMessagePrefix;

    /**
     * @var string - 4 bytes for 