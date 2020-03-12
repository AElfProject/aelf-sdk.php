public function getName()
    {
        return $this->name;
    }

    /**
     * The fully qualified name of the interface which is included.
     *
     * Generated from protobuf field <code>string name = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setName($var)
    {
        GPBUtil::checkString($var, True);
        $this->name = $var;

        return $this;
    }

    /**
     * If non-empty specifies a path under which inherited HTTP paths
     * are rooted.
     *
     * Generated from protobuf field <code>string root = 2;</code>
     * @return string
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * If non-emp