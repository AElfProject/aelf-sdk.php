e) {
                return substr($this->buffer, 0, $this->size);
            }
        }

        return $this->buffer;
    }

    /**
     * @return string
     */
    public function getHex(): string
    {
        return unpack("H*", $this->getBinary())[1];
    }

    /**
     * @return \GMP
     */
    public function getGmp(): \GMP
    {
        $gmp = gmp_init($this->getHex(), 16);
        return $gmp;
    }

    /**
     * @return int|string
     */
    public function getInt()
    {
        return gmp_strval($this->getGmp(), 10);
    }

    /**
     * @return Buffer
     */
    public function flip(): BufferInterface
    {
        /** @var Buffer $buffer */
        $buf