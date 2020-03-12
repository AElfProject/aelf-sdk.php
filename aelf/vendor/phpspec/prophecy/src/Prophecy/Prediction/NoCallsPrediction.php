          $size += 2;  // size for "[]".
                $size += $count - 1;                     // size for commas
                $getter = $field->getGetter();
                foreach ($values as $value) {
                    $size += $this->fieldDataOnlyJsonByteSize($field, $value);
                }
            }
        } elseif ($this->existField($field) || GPBUtil::hasJsonValue($this)) {
            if (!GPBUtil::hasSpecialJsonMapping($this)) {
                $size += 3;                              // size for "\"\":".
                $size += strlen($field->getJsonName());  // size for field name
            }
            $getter = $field->getGetter();
            $value = $this->$getter();
            $size += $this->fieldDataOnlyJsonByteSize($field, $value);
        }
        return $size;
    }

    /**
     * @ignore
     */
    public function byteSize()
    {
        $size = 0;

        $fields = $this->desc->getField();
        foreach ($fields as $field) {
            $size += $this->fieldByteSize($field);
        }
        $size += strlen($this->unknown);
        return $size;
    }

    private function appendHelper($field, $append_value)
    {
        $getter = $field->getGetter();
        $setter = $field->getSetter();

        $field_arr_value = $this->$getter();
        $field_arr_value[] = $append_value;

        if (!is_object($field_arr_value)) {
            $this->$setter($field_arr_value);
        }
    }

    private function kvUpdateHelper($field, $update_key, $update_value)
    {
        $getter = $field->getGetter();
        $setter = $field->getSetter();

        $field_arr_value = $this->$getter();
        $field_arr_value[$update_key] = $update_value;

        if (!is_object($field_arr_value)) {
  