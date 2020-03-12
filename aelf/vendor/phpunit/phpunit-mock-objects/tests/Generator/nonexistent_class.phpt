               $map_field = new MapField(
                            $key_field->getType(),
                            $value_field->getType(),
                            $value_field->getEnumType()->getClass());
                        $this->$setter($map_field);
                        break;
                    default:
                        $map_field = new MapField(
                            $key_field->getType(),
                            $value_field->getType());
                        $this->$setter($map_field);
                        break;
                }
            } else if ($field->getLabel() === GPBLabel::REPEATED) {
                switch ($field->getType()) {
                    case GPBType::MESSAGE:
                    case GPBType::GROUP:
                        $repeated_field = new RepeatedField(
                            $field->getType(),
                            $field->getMessageType()->getClass());
                        $this->$setter($repeated_field);
                        break;
                    case GPBType::ENUM:
                        $repeated_field = new RepeatedField(
                            $field->getType(),
                            $field->getEnumType()->getClass());
                        $this->$setter($repeated_field);
                        break;
                    default:
                        $repeated_field = new RepeatedField($field->getType());
                        $this->$setter($repeated_field);
                        break;
                }
            } else if ($field->getOneofIndex() !== -1) {
                $oneof = $this->desc->getOneofDecl()[$field->getOneofIndex()];
                $oneof_name = $oneof->getName();
                $this->$oneof_name = new OneofField($oneof);
            } else if ($field->getLabel() === GPBLabel::OPTIONAL) {
                switch ($field->getType()) {
                    case GPBType::DOUBLE   :
                