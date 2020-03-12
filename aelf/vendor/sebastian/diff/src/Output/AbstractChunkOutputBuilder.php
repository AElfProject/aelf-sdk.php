<?php

declare(strict_types=1);

namespace BitWasp\Bitcoin\Crypto;

use BitWasp\Buffertools\Buffer;
use BitWasp\Buffertools\BufferInterface;
use lastguest\Murmur;

class Hash
{
    /**
     * Calculate Sha256(RipeMd160()) on the given data
     *
     * @param BufferInterface $data
     * @return BufferInterface
     */
    public static function sha256ripe160(BufferInterface $data): BufferInterface
    {
        return new Buffer(hash('ripemd160', hash('sha256', $data->getBinary(), true), true), 20);
    }

    /**
     * Perform SHA256
     *
     * @param BufferInterface $data
     * @return BufferInterface
     */
    public static function sha256(BufferInterface $data): BufferInterface
    {
        return new Buffer(hash('sha256', $data->getBinary(), true), 32);
    }

    /**
     * Perform SHA256 twice
     *
     * @param BufferInterface $data
     * @return BufferInterface
     */
    public static function sha256d(BufferInterface $data): BufferInterface
    {
        return new Buffer(hash('sha256', hash('sha256', $data->getBinary(), true), true), 32);
    }

    /**
     * RIPEMD160
     *
     * @param BufferInterface $data
     * @return BufferInterface
     */
    public static function ripemd160(BufferInterface $data): BufferInterface
    {
        return new Buffer(hash('ripemd160', $data->getBinary(), true), 20);
    }

    /**
     * RIPEMD160 twice
     *
     * @param BufferInterface $data
     * @return BufferInterface
     */
    public static function ripemd160d(BufferInterface $data): BufferInterface
    {
        return new Buffer(hash('ripemd160', hash('ripemd16