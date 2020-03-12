ferInterface
     */
    private function decodeNullData(array $decoded)
    {
        if (count($decoded) !== 2) {
            return false;
        }

        if ($decoded[0]->getOp() === Opcodes::OP_RETURN && $decoded[1]->isPush()) {
            return $decoded[1]->getData();
        }

        return false;
    }

    /**
     * @param ScriptInterface $script
     * @return bool
     */
    public function isNullData(ScriptInterface $script): bool
    {
        try {
            return $this->decodeNullData($script->getScriptParser()->decode()) !== false;
        } catch (\Exception $e) {
        }

        return false;
    }

    /**
     * @param array $decoded
     * @return bool|BufferInterface
     */
    private function decodeWitnessCoinbaseCommitment(array $decoded)
    {
        if (count($decoded) !== 2) {
            return false;
        }

        if ($decoded[0]->isPush() || $decoded[0]->getOp() !== Opcodes::OP_RETURN) {
            return false;
        }

        if ($decoded[1]->isPush()) {
            $data = $decoded[1]->getData()->getBinary();
            if ($decoded[1]->getDataSize() === 0x24 && substr($data, 0, 4) === "\xaa\x21\xa9\xed") {
                return new Buffer(substr($data, 4));
            }
        }

        return false;
    }

    /**
     * @param ScriptInterface $script
     * @return bool
     */
    public function isWitnessCoinbaseCommitment(ScriptInterface $script): bool
    {
        try {
            return $this->decodeWitnessCoinbaseCommitment($script->getScriptParser()->decode()) !== false;
        } catch (\Exception $e) {
        }

        return false;
    }

    /**
     * @param array $decoded
     * @param null $solution
     * @return string
     */
    priv