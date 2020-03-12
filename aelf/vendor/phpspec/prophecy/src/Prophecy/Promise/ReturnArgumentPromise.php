                   self::appendHelper($field, $proto_value);
                }
            } else {
                $setter = $field->getSetter();
                $proto_value = $this->convertJsonValueToProtoValue(
                    $value,
                    $field);
                if ($field->getType() === GPBType::MESSAGE) {
                    if (is_null($proto_value)) {
                        continue;
                    }
                    $getter = $field->getGetter();
                    $submsg = $this->$getter();
                    if (!is_null($submsg)) {
                        $submsg->mergeFrom($proto_value);
                        continue;
                    }
                }
                $this->$setter($proto_value);
            }
        }
    }

    /**
     * @ignore
     */
    public function parseFromJsonStream($input)
    {
        $array = json_decode($input->getData(), true, 512, JSON_BIGINT_AS_STRING);
        if ($this instanceof \Google\Protobuf\ListValue) {
            $array = ["values"=>$array];
        }
        if (is_null($array)) {
            if ($this instanceof \Google\Protobuf\Value) {
              $this->setNullValue(\Google\Protobuf\NullValue::NULL_VALUE);
              return;
            } else {
              throw new GPBDecodeException(
                  "Cannot decode json string: " . $input->getData());
            }
        }
        try {
            $this->mergeFromJsonArray($array);
        } catch (\Exception $e) {
            throw new GPBDecodeException($e->getMessage());
    