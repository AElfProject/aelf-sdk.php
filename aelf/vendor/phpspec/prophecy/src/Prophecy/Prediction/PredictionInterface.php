alue) {
                    $data_size = 0;
                    if ($key != $this->defaultValue($key_field)) {
                        $data_size += $this->fieldDataOnlyByteSize(
                            $key_field,
                            $key);
                        $data_size += GPBWire::tagSize($key_field);
                    }
                    if ($value != $this->defaultValue($value_field)) {
                        $data_size += $this->fieldDataOnlyByteSize(
                            $value_field,
                            $value);
                        $data_size += GPBWire::tagSize($value_field);
                    }
                    $size += GPBWire::varint32Size($data_size) + $data_size;
                }
            }
        } elseif ($field->isRepeated()) {
            $getter = $field->getGetter();
            $values = $this->$getter();
            $count = count($values);
  