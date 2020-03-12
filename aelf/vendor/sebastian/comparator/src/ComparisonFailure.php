The value of the uninterpreted option, in whatever type the tokenizer
     * identified it as during parsing. Exactly one of these should be set.
     *
     * Generated from protobuf field <code>optional string identifier_value = 3;</code>
     * @return string
     */
    public function getIdentifierValue()
    {
        return $this->identifier_value;
    }

    /**
     * The value of the uninterpreted option, in whatever type the tokenizer
     * identified it as during parsing. Exactly one of these should be set.
     *
     * Generated from protobuf field <code>optional string identifier_value = 3;</code>
     * @param string $var
     * @return $this
     */
    public function setIdentifierValue($var)
    {
        GPBUtil::checkString($var, True);
        $this->identifier_value = $var;
        $this->has_identifier_value = true;

        return $this;
    }

    public function hasIdentifierValue()
    {
        return $this->has_identifier_value;
    }

    /**
     * Generated from protobuf field <code>optional uint64 positive_int_value = 4;</code>
     * @return int|string
     */
    public function getPositiveIntValue()
    {
        return $this->positive_int_value;
    }

    /**
     * Generated from protobuf field <code>optional uint64 positive_int_value = 4;</code>
     * @param int|string $var
     * @return $this
     */
    public function setPositiveIntValue($var)
    {
        GPBUtil::checkUint64($var);
        $this->positive_int_value = $var;
        $this->has_positive_int_value = true;

        return $this;
    }

    public function hasPositiveIntValue()
    {
        return $this->has_positive_int_value;
    }

    /**
     * Generated from protobuf field <code>optional int64 negative_int_value = 5;</code>
     * @return int|string
     */
    public function getNegativeIntValue()
    {
        return $this->negative_int_value;
    }

    /**
     * Generated from protobuf field <code>optional int64 negative_int_value = 5;</code>
     * @param int|string $var
     * @return $this
     */
    public function setNegativeIntValue($var)
    {
        GPBUtil::checkInt64($var);
        $this->negative_int_value = $var;
        $this->has_negative_int_value = true;

        return $this;
    }

    public function hasNegativeIntValue()
    {
        return $this->has_negative_int_value;
    }

    /**
     * Generated from protobuf field <code>optional double double_value = 6;</code>
     * @return float
     */
    public function getDoubleValue()
    {
        return $this->double_value;
    }

    /**
     * Generated from protobuf field <code>optional double double_value = 6;</code>
     * @param float $var
     * @return $this
     */
    public function setDoubleValue($var)
    {
        GPBUtil::checkDouble($var);
        $this->double_value = $var;
        $this->has_double_value = true;

        return $this;
    }

    public function hasDoubleValue()
    {
        return $this->has_double_value;
    }

    /**
     * Generated from protobuf field <code>optional bytes strin