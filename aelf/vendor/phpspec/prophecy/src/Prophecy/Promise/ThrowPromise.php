<?php

declare(strict_types=1);

namespace BitWasp\Buffertools;

use BitWasp\Buffertools\Exceptions\ParserOutOfRange;

class Parser
{
    /**
     * @var string
     */
    private $string;

    /**
     * @var int
     */
    private $size = 0;

    /**
     * @var int
     */
    private $position = 0;

    /**
     * Instantiate class, optionally taking Buffer or HEX.
     *
     * @param null|string|BufferInterface $input
     */
    public function __construct($input = null)
    {
        if (null === $input) {
            $input = '';
        }

        if (is_string($input)) {
            $bin = Buffer::hex($input, null)->getBinary();
        } elseif ($input instanceof BufferInterface) {
            $bin = $input->getBinary();
        } else {
            throw new \InvalidArgumentException("Invalid argument to Parser");
        }

        $this->string = $bin;
        $this->position = 0;
        $this->size = strlen($this->string);
    }

    /**
     * Get the position pointer of the parser - ie, how many bytes from 0
     *
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * Get the total size of the parser
     *
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Parse $bytes bytes from the string, and return the obtained buffer
     *
     * @param  int $numBytes
     * @param  bool $flipBytes
     * @return BufferInterface
     * @throws \Exception
     */
    public function readBytes(int $numBytes, bool $flipBytes = false): BufferInterface
    {
        $string = substr($this->string, $this->getPosition(), $numBytes);
        $length = strlen($string);

        if ($length === 0) {
            throw new ParserOutOfRange('Could not parse string of required length (empty)');
        } elseif ($length < $numBytes) {
            throw new ParserOutOfRange('Could not parse string of required length (too short)');
        }

        $this->position += $numBytes;

        if ($flipBytes) {
            $string = Buffertools::flipBytes($string);
            /** @var string $string */
        }

        return new Buffer($string, $length);
    }

    /**
     * Write $data as $bytes bytes. Can be flipped if needed.
     *
     * @param  integer $numBytes - number of bytes to write
     * @param  SerializableInterface|BufferInterface|string $data - buffer, serializable or hex
     * @param  bool $flipBytes
     * @return Parser
     */
    public function writeBytes(int $numBytes, $data, bool $flipBytes = false): Parser
    {
        // Treat $data to ensure it's a buffer, with the correct size
        if ($data instanceof SerializableInterface) {
            $data = $data->getBuffer();
        }

        if (is_string($data)) {
            // Convert to a buffer
            $data = Buffer::hex($data, $numBytes);
        } else if (!($data instanceof Buf