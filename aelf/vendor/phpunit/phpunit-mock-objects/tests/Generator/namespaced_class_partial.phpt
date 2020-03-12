takes a string in the (non-human-readable) binary wire
     * format, matching the encoding output by serializeToString().
     * See mergeFrom() for merging behavior, if the field is already set in the
     * specified message.
     *
     * @param string $data Binary protobuf data.
     * @return null.
     * @throws \Exception Invalid data.
     */
    public function mergeFromString($data)
    {
        $input = new CodedInputStream($data);
        $this->parseFromStream($input);
    }

    /**
     * Parses a json string to protobuf message.
     *
     * This function takes a string in the json wire format, matching the
     * encoding output by serializeToJsonString().
     * See mergeFrom() for merging behavior, if the field is already set in the
     * specified message.
     *
     * @param string $data Json protobuf data.
     * @return null.
     * @throws \Exception Invalid data.
     */
    public function mergeFromJsonString($data)
    {
        $input = new RawInputStream($data);
        $this->parseFromJsonStream($input);
    }

    /**
     * @ignore
     */
    public function parseFromStream($input)
    {
        while (true) {
            $tag = $input->readTag();
            // End of input.  This is a valid place to end, so return true.
            if ($tag === 0) {
                return true;
            }

            $number = GPBWire::getTagFieldNumber($tag);
            $field = $this->desc->getFieldByNumber($number);

            $this->parseFieldFromStream($tag, $input, $field);
        }
    }

    private function convertJsonValueToProtoValue(
        $value,
        $field,
        $is_map_key = false)
    {
        switch ($field->getType()) {
            case GPBType::MESSAGE:
                $klass = $field->getMessageType()->getClass();
                $submsg = new $klass;

                if (is_a($submsg, "Google\Protobuf\Duration")) {
                    if (is_null($value)) {
                        return $this->defaultValue($field);
                    } else if (!is_string($value)) {
                        throw new GPBDecodeException("Expect string.");
                    }
                    return GPBUtil::parseDuration($value);
                } else if ($field->isTimestamp()) {
                    if (is_null($value)) {
                        return $this->defaultValue($field);
                    } else if (!is_string($value)) {
                        throw new GPBDecodeException("Expect string.");
                    }
                    try {
                        $timestamp = GPBUtil::parseTimestamp($value)