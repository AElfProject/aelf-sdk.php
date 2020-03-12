  {
        $this->tolerateInvalidPublicKey = $setting;
        return $this;
    }

    /**
     * @param bool $setting
     * @return $this
     */
    public function allowComplexScripts(bool $setting)
    {
        $this->allowComplexScripts = $setting;
        return $this;
    }

    /**
     * @param BufferInterface $vchPubKey
     * @return PublicKeyInterface|null
     * @throws \Exception
     */
    protected function parseStepPublicKey(BufferInterface $vchPubKey)
    {
        try {
            return $this->pubKeySerializer->parse($vchPubKey);
        } catch (\Exception $e) {
            if ($this->tolerateInvalidPublicKey) {
                return null;
            }

            throw $e;
        }
    }

    /**
     * @param ScriptInterface $script
     * @param Buffer