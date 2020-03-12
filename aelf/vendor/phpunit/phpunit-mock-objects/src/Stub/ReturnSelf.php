<?php

declare(strict_types=1);

namespace BitWasp\Bitcoin\Key\Factory;

use BitWasp\Bitcoin\Bitcoin;
use BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface;
use BitWasp\Bitcoin\Crypto\EcAdapter\Key\KeyInterface;
use BitWasp\Bitcoin\Key\Deterministic\ElectrumKey;
use BitWasp\Bitcoin\Mnemonic\Electrum\ElectrumWordListInterface;
use BitWasp\Bitcoin\Mnemonic\MnemonicFactory;
use BitWasp\Buffertools\Buffer;
use BitWasp\Buffertools\BufferInterface;

class ElectrumKeyFactory
{
    /**
     * @var EcAdapterInterface
     */
    private $adapter;

    /**
     * @var PrivateKeyFactory
     */
    private $privateFactory;

    /**
     * ElectrumKeyFactory constructor.
     * @param EcAdapterInterface|null $ecAdapter
     */
    public function __construct(EcAdapterInterface $ecAdapter = null)
    {
        $this->adapter = $ecAdapter ?: Bitcoin::getEcAdapter();
        $this->privateFactory = new PrivateKeyFactory($ecA