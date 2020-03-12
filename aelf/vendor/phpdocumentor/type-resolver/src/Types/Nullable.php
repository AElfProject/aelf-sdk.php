d .google.protobuf.DescriptorProto.ExtensionRange extension_range = 5;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getExtensionRange()
    {
        return $this->extension_range;
    }

    /**
     * Generated from protobuf field <code>repeated .google.protobuf.DescriptorProto.ExtensionRange extension_range = 5;</code>
     * @param \Google\Protobuf\Internal\DescriptorProto\ExtensionRange[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setExtensionRange($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::MESSAGE, \Google\Protobuf\Internal\DescriptorProto\ExtensionRange::class);
        $this->extension_range = $arr;
        $this->has_extension_range = true;

        return $this;
    }

    public function hasExtensionRange()
    {
        return $this->has_extension_range;
    }

    /**
     * Generated from protobuf field <code>repeated .google.protobuf.OneofDescriptorProto oneof_decl = 8;</code>
     * @return \Go