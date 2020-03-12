tValue($field);
                }
                if (is_integer($value)) {
                    return $value;
                }
                $enum_value = $field->getEnumType()->getValueByName($value);
                if (!is_null($enum_value)) {
                    return $enum_value->getNumber();
                }
                throw new GPBDecodeException(
                        "Enum field only accepts integer or enum value name");
            case GPBType::STRING:
                if (is_null($value)) {
                    return $this->defaultValue($field);
                }
                if (is_numeric($value)) {
                    return strval($value);
                }
                if (!is_string($value)) {
                    throw new GPBDecodeException(
                        "String field only accepts string value");
                }
                return $value;
            case GPBType::BYTES:
                if (is_null($value)) {
                    return $this->defaultValue($field);
                }
                if (!is_string($value)) {
                    throw new GPBDecodeException(
                        "Byte field only accepts string value");
                }
                $proto_value = base64_decode($value, true);
                if ($proto_value === false) {
                    throw new GPBDecodeException("Invalid base64 characters");
                }
                return $proto_value;
            case GPBType::BOOL:
                if (is_null($value)) {
                    return $this->defaultValue($field);
                }
                if ($is_map_key) {
                    if ($value === "true") {
                        return true;
                    }
                    if ($value === "false") {
                        return false;
                    }
                    throw new GPBDecodeException(
                        "Bool field only accepts bool value");
                }
                if (!is_bool($value)) {
                    throw new GPBDe