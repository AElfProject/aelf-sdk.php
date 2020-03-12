     }
        return $this->cache[__FUNCTION__];
    }

    /**
     * Add a little-endian Int128 serializer to the template
     *
     * @return Int128
     */
    public function int128le(): Int128
    {
        if (!isset($this->cache[__FUNCTION__])) {
            $this->cache[__FUNCTION__] = call_user_func_array(['parent', __FUNCTION__], func_get_args());
        }
        return $this->cache[__FUNCTION__];
    }

    /**
     * Add a int256 serializer to the template
     *
     * @return Int256
     */
    public function int256(): Int256
    {
        if (!isset($this->cache[__FUNCTION__])) {
            $this->cache[__FUNCTION__] = call_user_func_array(['parent', __FUNCTION__], func_get_args());
        }
        return $this->cache[__FUNCTION__];
    }

    /**
     * Add a little-endian Int256 serializer to the template
     *
     * @return Int256
     */
    public function int256le(): Int256
    {
        if (!isset($this->cache[__FUNCTION__])) {
            $this->cache[__FUNCTION__] = call_user_func_array(['parent', __FUNCTION__], func_get_args());
        }
        return $this->cache[__FUNCTION__];
    }

    /**
     * Add a VarInt serializer to the template
     *
     * @return VarInt
     */
    public function varint(): VarInt
    {
        if (!isset($this->cache[__FUNCTION__])) {
            $this->cache[__FUNCTION__] = call_user_func_array(['parent', __FUNCTION__], func_get_args());
        }
        return $this->cache[__FUNCTION__];
    }

    /**
     * Add a V