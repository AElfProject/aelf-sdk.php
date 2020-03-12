<?php

declare(strict_types=1);

namespace BitWasp\Bitcoin\Mnemonic\Bip39;

use BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface;
use BitWasp\Bitcoin\Crypto\Hash;
use BitWasp\Bitcoin\Crypto\Random\Random;
use BitWasp\Bitcoin\Mnemonic\MnemonicInterface;
use BitWasp\Buffertools\Buffer;
use BitWasp\Buffertools\BufferInterface;

class Bip39Mnemonic implements MnemonicInterface
{
    /**
     * @var EcAdapterInterface
     */
    private $ecAdapter;

    /**
     * @var Bip39WordListInterface
     */
    private $wordList;

    const MIN_ENTROPY_BYTE_LEN = 16;
    const MAX_ENTROPY_BYTE_LEN = 32;
    const DEFAULT_ENTROPY_BYTE_LEN = self::MAX_ENTROPY_BYTE_LEN;

    private $validEntropySizes = [
        self::MIN_ENTROPY_BYTE_LEN * 8, 160, 192, 224, self::MAX_ENTROPY_BYTE_LEN * 8,
    ];

    /**
     * @param EcAdapterInterface $ecAdapter
     * @param Bip39WordListInterface $wordList
     */
    public function __construct(EcAdapterInterface $ecAdapter, Bip39WordListInterface $wordList)
    {
        $this->ecAdapter = $ecAdapter;
        $this->wordList = $wordList;
    }

    /**
     * Creates a new Bip39 mnemonic string.
     *
     * @param int $entropySize
     * @return string
     * @throws \BitWasp\Bitcoin\Exceptions\RandomBytesFailure
     */
    public function create(int $entropySize = null): string
    {
        if (null === $entropySize) {
            $entropySize = self::DEFAULT_ENTROPY_BYTE_LEN * 8;
        }

        if (!in_array($entropySize, $this->validEntropySizes)) {
            throw new \InvalidArgumentException("Invalid entropy length");
        }

        $random = new Random();
        $entropy = $random->bytes($entropySize / 8);

        return $this->entropyToMnemonic($entropy);
    }

    /**
     * @param BufferInterface $entropy
     * @param integer $CSlen
     * @return string
     */
    private function calculateChecksum(BufferInterface $entropy, int $CSlen): string
    {
        // entropy range (128, 256) yields (4, 8) bits of checksum
        $checksumChar = ord(Hash::sha256($entropy)->getBinary()[0]);
        $cs = '';
        for ($i = 0; $i < $CSlen; $i++) {
            $cs .= $checksumChar >> (7 - $i) & 1;
        }

        return $cs;
    }

    /**
     * @param BufferInterface $entropy
     * @return string[] - array of words from the word list
     */
    public function entropyToWords(BufferInterface $entropy): array
    {
        $ENT = $entropy->getSize() * 8;
        if (!in_array($entropy->getSize() * 8, $this->validEntropySizes)) {
            throw new \InvalidArgumentException("Invalid entropy length");
        }

        $CS = $ENT 