$buffer
     * @param bool $flipBytes
     * @return Parser
     */
    public function appendBuffer(BufferInterface $buffer, bool $flipBytes = false): Parser
    {
        $this->appendBinary($buffer->getBinary(), $flipBytes);
        return $this;
    }

    /**
     * @param string $binary
     * @param bool $flipBytes
     * @return Parser
     */
    public function appendBinary(string $binary, bool $flipBytes = false): Parser
    {
        if ($flipBytes) {
            $binary = Buffertools::flipBytes($binary);
        }

        $this->string .= $binary;
        $this->size += strlen($binary);
        return $this;
    }

    /**
     * Take an array containing serializable objects.
     * @param array<mixed|SerializableInterface|BufferInterface> $serializable
     * @return Parser
     */
    public function writeArray(array $serializable): Parser
    {
        $parser = new Parser(Buffertools::numToVarInt(count($serializable)));
        foreach ($serializable as $object) {
            if ($object instanceof SerializableInterface) {
                $object = $object->getBuffer();
            }

            if ($object instanceof BufferInterface) {
                $parser->writeBytes($object->getSize(), $object);
            } else {
                throw new \RuntimeExceptio