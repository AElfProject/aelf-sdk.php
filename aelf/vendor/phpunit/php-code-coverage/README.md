ile;
    }

    /**
     * Identifies the filesystem path to the original source .proto.
     *
     * Generated from protobuf field <code>optional string source_file = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setSourceFile($var)
    {
        GPBUtil::checkString($var, True);
        $this->source_file = $var;
        $this->has_source_file = true;

        return $this;
    }

    public function hasSourceFile()
    {
        return $this->has_source_file;
    }

    /**
     * Identifies the starting offset in bytes in the generated code
     * that relates to the identified object.
     *
     * Generated from protobuf field <code>optional int32 begin = 3;</code>
     * @return int
     */
    public function getBegin()
    {
        return $this->begin;
    }

    /**
     * Identifies the starting offset in bytes in the generated code
     * that relates to the identified object.
     *
     * Generated from protobuf field <code>optional int32 begin = 3;</code>
     * @param int $var
     * @return $this
     */
    public function setBegin($var)
    {
        GPBUtil::checkInt32($var);
        $this->begin = $var;
        $this->has_begin = true;

        return $this;
    }

    public function hasBegin()
    {
        return $this->has_begin;
    }

    /**
     * Identifies the ending offset in bytes in the genera