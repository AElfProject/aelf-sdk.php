    * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Generated from protobuf field <code>optional string name = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setName($var)
    {
        GPBUtil::checkString($var, True);
        $this->name = $var;
        $this->has_name = true;

        return $this;
    }

    public function hasName()
    {
        return $this->has_name;
    }

    /**
     * Generated from protobuf field <code>repeated .google.protobuf.FieldDescriptorProto field = 2;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Generated from protobuf field <code>repeated .google.protobuf.FieldDescriptorProto field = 2;</code>
     * @param \Google\Protobuf\Internal\FieldDescriptorProto[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setField($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::MESSAGE, \Google\Protobuf\Internal\FieldDescriptorProto::class);
        $this->field = $arr;
        $this->has_field = true;

        return $this;
    }

    public function hasField()
    {
        return $this->has_field;
    }

    /**
     * Generated from protobuf field <code>repeated .google.protobuf.FieldDescriptorProto extension = 6;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Generated from protobuf field <code>repeated .google.protobuf.FieldDescriptorProto extension = 6;</code>
     * @param \Google\Protobuf\Interna