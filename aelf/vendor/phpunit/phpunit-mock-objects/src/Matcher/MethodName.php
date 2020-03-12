 than 32 bits, discard the high order bits.
     * @param $var.
     */
    public function readVarint32(&$var)
    {
        if (!$this->readVarint64($var)) {
            return false;
        }

        if (PHP_INT_SIZE == 4) {
            $var = bcmod($var, 4294967296);
        } else {
            $var &= 0xFFFFFFFF;
        }

        // Convert large uint32 to int32.
        if ($var > 0x7FFFFFFF) {
            if (PHP_INT_SIZE === 8) {
                $var = $var | (0xFFFFFFFF << 32);
            } else {
                $var = bcsub($var, 4294967296);
            }
        }

        $var = intval($var);
        return true;
    }

    /**
     * Read Uint64 into $var. Advance buffer with consumed bytes.
     * @param $var.
     */
    public function readVarint64(&$var)
    {
        $count = 0;

        if (PHP_INT_SIZE == 4) {
            $high = 0;
            $low = 0;
            $b = 0;

            do {
                if ($this->current === $this->buffer_end) {
                    return false;
                }
                if ($count === self::MAX_VARINT_BYTES) {
                    return false;
                }
                $b = ord($this->buffer[$this->current]);
                $bits = 7 * $count;
                if ($bits >= 32) {
                    $high |= (($b & 0x7F) << ($bits - 32));
                } else if ($bits > 25){
                    // $bits is 28 in this case.
                    $low |= (($b & 0x7F) << 28);
                    $high = ($b & 0x7F) >> 4;
                } else {
                    $low |= (($b & 0x7F) << $bits);
                }

                $this->advance(1);
                $count += 1;
            } while ($b & 0x80);

            $var = GPBUtil::combineInt32ToInt64($high, $low);
            if (bccomp