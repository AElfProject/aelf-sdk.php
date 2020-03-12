, $field)) {
                        return false;
                    }
                }
            }
            if (!GPBUtil::hasSpecialJsonMapping($this)) {
                $output->writeRaw("}", 1);
            }
        }
        return true;
    }

    /**
     * Serialize the message to string.
     * @return string Serialized binary protobuf data.
     */
    public function serializeToString()
    {
        $output = new CodedOutputStream($this->byteSize());
        $this->serializeToStream($output);
        return $output->getData();
    }

    /**
     * Serialize the message to json string.
     * @return string Serialized json protobuf data.
     */
    public function serializeToJsonString()
    {
        $output = new CodedOutputStream($this->jsonByteSize());
        $this->serializeToJsonStream($output);
        