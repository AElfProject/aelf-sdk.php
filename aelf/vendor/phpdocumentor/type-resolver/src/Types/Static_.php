s()] = $descriptor;
        $this->class_to_enum_desc[$descriptor->getLegacyClass()] = $descriptor;
    }

    public function getDescriptorByClassName($klass)
    {
        if (isset($this->class_to_desc[$klass])) {
            return $this->class_to_desc[$klass];
        } else {
            return null;
        }
    }

    public function getEnumDescriptorByClassName($klass)
    {
        if (isset($this->class_to_enum_desc[$klass])) {
            return $this->class_to_enum_desc[$klass];
        } else {
            return null;
        }
    }

    public function getDescriptorByProtoName($proto)
    {
        if (isset($this->proto_to_class[$proto])) {
            $klass = $this->proto_to_class[$proto];
            return $this->class_to_desc[$klass];
        } else {
          return null;
        }
    }

    public function getEnumDescriptorByProtoName($proto)
    {
        $klass = $this->proto_to_class[$proto];
        return $this->class_to_enum_desc[$klass];
    }