ip32Prefix(string $prefixType): bool
    {
        return array_key_exists($prefixType, $this->bip32PrefixMap);
    }

    /**
     * @param string $prefixType
     * @return string
     * @throws MissingBip32Prefix
     */
    protected function getBip32Prefix(string $prefixType): string
    {
        if (!$this->hasBip32Prefix($prefixType)) {
            throw new MissingBip32Prefix();
        }
        return $this->bip32PrefixMap[$prefixType];
    }

    /**
     * @return string
     * @throws MissingNetworkParameter
     * @see NetworkInterface::getSignedMessageMagic
     */
    public function getSignedMessageMagic(): string
    {
        if (null === $this->signedMessagePrefix) {
            throw new MissingNetworkParameter("Missing magic string for signed message");
        }
        return $this->signedMessagePrefix;
    }

    /**
     * @return string
     * @throws MissingNetworkParameter
     * @see NetworkInterface::getNetMagicBytes()
     */
    public function getNetMagicBytes(): string
    {
        if (null === $this->p2pMagic) {
            throw new MissingNetworkParameter("Missing network magic bytes");
        }
        return $this->p2pMagic;
    }

    /**
     * @return string
     * @throws MissingBase58Prefix
     */
    public function getPrivByte(): string
    {
        return $this->getBase58Prefix(self::BASE58_WIF);
    }

    /**
     * @return string
     * @throws MissingBase58Prefix
     * @see NetworkInterface::getAddressByte()
     */
    public function getAddressByte(): string
    {
        return $this->getBase58Prefix(self::BASE58_ADDRESS_P2PKH);
    }
    /**
     * @return int
     * @throws MissingBase58Prefix
     * @see NetworkInterface::getAddressPrefixLength()