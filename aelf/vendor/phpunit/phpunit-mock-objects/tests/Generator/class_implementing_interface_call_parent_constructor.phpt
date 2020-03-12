<?php

declare(strict_types=1);

namespace BitWasp\Bitcoin\Transaction\Factory;

use BitWasp\Bitcoin\Crypto\EcAdapter\Key\PublicKeyInterface;
use BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Key\PublicKeySerializerInterface;
use BitWasp\Bitcoin\Script\ScriptInfo\Multisig;
use BitWasp\Bitcoin\Script\ScriptInfo\PayToPubkey;
use BitWasp\Bitcoin\Script\ScriptInfo\PayToPubkeyHash;
use BitWasp\Bitcoin\Script\ScriptType;
use BitWasp\Bitcoin\Serializer\Signature\TransactionSignatureSerializer;
use BitWasp\Bitcoin\Signature\TransactionSignatureInterface;
use BitWasp\Buffertools\Buffer;
use BitWasp\Buffertools\BufferInterface;

class Checksig
{
    /**
     * @var string
     */
    private $scriptType;

    /**
     * @var bool
     */
    private $required = true;

    /**
     * @var PayToPubkeyHash|PayToPubkey|Multisig
     */
    private $info;

    /**
     * @var int
     */
    protected $requiredSigs;

    /**
     * @var int
     */
    protected $keyCount;

    /**
     * @var TransactionSignatureInterface[]
     */
    protected $signatures = [];

    /**
     * @var PublicKeyInterface[]|null[]
     */
    protected $publicKeys = [];

    /**
     * Checksig constructor.
     * @param Multisig|PayToPubkeyHash|PayToPubkey $info
     */
    public function __construct($info)
    {
        if (!is_object($info)) {
            throw new \RuntimeException("First value to checksig must be an object");
        }

        $infoClass = get_class($info);
        switch ($infoClass) {
            case PayToPubkey::class:
                /** @var PayToPubkey $info */
                $this->scriptType = $info->getType();
                $this->requiredSigs = $info->getRequiredSigCount();
                $this->keyCount = 1;
                break;
            case PayToPubkeyHash::class:
                /** @var PayToPubkeyHash $info */
                $this->scriptType = ScriptType::P2PKH;
                $this->requiredSigs = $info->getRequiredSigCount();
                $this->keyCount = 1;
                break