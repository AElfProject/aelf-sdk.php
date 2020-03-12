/**
     * Always has exactly three or four elements: start line, start column,
     * end line (optional, otherwise assumed same as start line), end column.
     * These are packed into a single field for efficiency.  Note that line
     * and column numbers are zero-based -- typically you will want to add
     * 1 to each before displaying to a user.
     *
     * Generated from protobuf field <code>repeated int32 span = 2 [packed = true];</code>
     * @param int[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setSpan($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::INT32);
        $this->span = $arr;
        $this->has_span = true;

        return $this;
    }

    public function hasSpan()
    {
        return $this->has_span;
    }

    /**
     * If this SourceCodeInfo