<?php

declare(strict_types=1);

namespace BitWasp\Bitcoin\Mnemonic\Electrum;

use BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface;
use BitWasp\Bitcoin\Mnemonic\MnemonicInterface;
use BitWasp\Buffertools\Buffer;
use BitWasp\Buffertools\BufferInterface;

class ElectrumMnemonic implements MnemonicInterface
{
    /**
     * @var EcAdapterInterface
     */
    private $ecAdapter;

    /**
     * @var ElectrumWordListInterface
     */
    private $wordList;

    /**
     * @param EcAdapterInterface $ecAdapter
     * @param ElectrumWordListInterface $wordList
     */
    public function __construct(EcAdapterInterface $ecAdapter, ElectrumWordListInterface $wordList)
    {
        $this->ecAdapter = $ecAdapter;
        $this->wordList = $wordList;
    }

    /**
     * @param BufferInterface $entropy
     * @return string[]
     * @throws \Exception
     */
    public function entropyToWords(BufferInterface $entropy): array
    {
        $math = $this->ecAdapter->getMath();
        $n = gmp_init(count($this->wordList), 10);
        $wordArray = []