on(
                       "Invalid data type for int32 field");
                }
                if (bccomp($value, 4294967295) > 0) {
                    throw new GPBDecodeException(
                        "Uint32 too large");
                }
                return $value;
            case GPBType::INT64:
            case GPBType::SINT64:
            case GPBType::SFIXED64:
                if (is_null($value)) {
                    return $this->defaultValue($field);
                }
                if (!is_numeric($value)) {
                   throw new GPBDecodeException(
                       "Invalid data type for int64 field");
                }
                if (is_string($value) && trim($value) !== $value) {
                   throw new GPBDecodeException(
                       "Invalid data type for int64 field");
                }
                if (bccomp($value, "9223372036854775807") > 0) {
                    throw new GPBDecodeException(
                        "Int64 too large");
                }
                if (bccomp($value, "-9223372036854775808") < 0) {
                    throw new GPBDecodeException(
                        "Int64 too small");
                }
                return $value;
            case GPBType::UINT64:
            case GPBType::FIXED64:
                if (is_null($value)) {
                    return $this->defaultValue($field);
                }
                if (!is_numeric($value)) {
                   throw new GPBDecodeException(
                       "Invalid data type for int64 field");
                }
                if (is_string($value) && trim($value) !== $value) {
                   throw new GPBDecodeException(
                       "Invalid data type for int64 field");
                }
                if (bccomp($value, "18446744073709551615") > 0) {
                    throw new GPBDecodeException(
                        "Uint64 too large");
                }
                if (bccomp($value, "9223372036854775807") > 0) {
                   