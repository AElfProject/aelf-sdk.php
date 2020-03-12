on uint128le(): Uint128
    {
        if (!isset($this->cache[__FUNCTION__])) {
            $this->cache[__FUNCTION__] = call_user_func_array(['parent', __FUNCTION__], func_get_args());
        }
        return $this->cache[__FUNCTION__];
    }

    /**
     * Add a Uint256 serializer to the template
     *
     * @return Uint256
     */
    public function uint256(): Uint256
    {
        if (!isset($this->cache[__FUNCTION__])) {
            $this->cache[__FUNCTION__] = call_user_func_array(['parent', __FUNCTION__], func_get_args());
        }
        return $this->cache[__FUNCTION__];
    }

    /**
     * Add a little-endian Uint256 serializer to the template
     *
     * @return Uint256
     */
    public function uint256le(): Uint256
    {
        if (!isset($this->cache[__FUNCTION__])) {
            $this->cache[__FUNCTION__] = call_user_func_array(['parent', __FUNCTION__], func_get_args());
        }
        return $this->cache[__FUNCTION__];
    }

    /**
     * Add a int8 serializer to the template
     *
     * @return Int8
     */
    public function int8(): Int8
    {
        if (!isset($this->cache[__FUNCTION__])) {
            $this->cache[__FUNCTION__] = call_user_func_array(['parent', __FUNCTION__], func_get_args());
        }
        return $this->cache[__FUNCTION__];
    }

    /**
     * Add a little-endian Int8 serializer to the template
     *
     * @return Int8
     */
    public function int