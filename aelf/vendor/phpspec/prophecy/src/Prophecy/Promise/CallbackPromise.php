$size += strlen(strval($value));
                break;
            case GPBType::FIXED32:
            case GPBType::UINT32:
                if ($value < 0) {
                    $value = bcadd($value, "4294967296");
                }
                $size += strlen(strval($value));
                break;
            case GPBType::FIXED64:
            case GPBType::UINT64:
                if ($value < 0) {
                    $value = bcadd($value, "18446744073709551616");
                }
                // Intentional fall through.
            case GPBType::SFIXED64:
            case GPBType::INT64:
            case GPBType::SINT64:
                $size += 2;  // size for ""
                $size += strlen(strval($value));
                break;
            case GPBType::FLOAT:
                if (is_nan($value)) {
                    $size += strlen("NaN") + 2;
                } elseif ($value === INF) {
                    $size += strlen("Infinity") + 2;
                } elseif ($value === -INF) {
                    $size += strlen("-Infinity") + 2;
                } else {
                    $size += strlen(sprintf("%.8g", $value));
                }
                break;
            case GPBType::DOUBLE:
                if (is_nan($value)) {
                    $size += strlen("NaN") + 2;
                } elseif ($value === INF) {
                    $size += strlen("Infinity") + 2;
                } elseif ($value === -INF) {
                    $size += strlen("-Infinity") + 2;
                } else {
                    $size += strlen(sprintf("%.17g", $value));
                }
                break;
            case GPBType::ENU