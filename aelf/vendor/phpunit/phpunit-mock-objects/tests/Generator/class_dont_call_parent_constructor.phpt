     return $this->requiredSigs;
    }

    /**
     * @return bool
     */
    public function isFullySigned(): bool
    {
        if ($this->required) {
            return $this->requiredSigs === count($this->signatures);
        } else {
            return true;
        }
    }

    /**
     * @param int $idx
     * @return bool
     */
    public function hasSignature(int $idx): bool
    {
        if ($idx > $this->requiredSigs) {
            throw new \RuntimeException("Out of range signature queried");
        }

        return array_key_exists($idx, $this->signatures);
    }

    /**
     * @param int $idx
     * @param TransactionSignatureInterface $signature
     * @return $this
     */
    public function setSignature(int $idx, TransactionSignatureInterface $signature)
    {
        if ($idx < 0 || $idx > $this->keyCount) {
            throw new \RuntimeException("Out of range signature for operation");
        }

        $this->signatures[$idx] = $signature;
        return $this;
    }

    /**
     * @param int $idx
     * @return TransactionSignatureInterface|null
     */
    public function getSignature(int $idx)
    {
        if (!$this->hasSignature($idx)) {
            return null;
        }

        return $this->signatures[$idx];
    }

    /**
     * @return array
     */
    public function getSignatures(): array
    {
        return $this->signatures;
    }

    /**
     * @param int $idx
     * @return bool
     */
    public function hasKey(int $idx): bool
    {
        return array_key_exists($idx, $this->publicKeys);
    }

    /**
     * @param int $idx
     * @return PublicKeyInterface|null
     */
    public function getKey(int $idx)
    {
        if (!$this->hasKey($idx)) {
            return null;
        }

        return $this->publicKeys[$idx];
    }

    /**
     * @param int $idx
     * @param PublicKeyInterface|null $key
     * @return $this
     */
    public function setKey(int $idx, Pu