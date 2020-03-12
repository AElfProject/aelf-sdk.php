d->isMap()) {
                $value_field = $field->getMessageType()->getFieldByNumber(2);
                if ($value_field->getType() != GPBType::MESSAGE) {
                    continue;
                }
                $getter = $field->getGetter();
                $map = $this->$getter();
                foreach ($map as $key => $value) {
                    $value->discardUnknownFields();
                }
            } else if ($field->getLabel() === GPBLabel::REPEATED) {
                $getter = $field->getGetter();
                $arr = $this->$getter();
                foreach ($arr as $sub) {
                    $sub->discardUnknownFields();
                }
            } else if ($field->getLabel() === GPBLabel::OPTIONAL) {
                $getter = $field->getGetter();
                $sub = $this->$getter();
                if (!is_null($sub)) {
                    $sub->discardUnknownFields();
                }
            }
        }
    }

    /**
     * Merges the contents of the specified message into current message.
     *
     * This method merges the contents of the specified message into the
     * current message. Singular fields that are set in the specified message
     * overwrite the corresponding fields in the current message.  Repeated
     * fields are appended. Map fields key-value pairs are overwritten.
     * Singular/Oneof sub-messages are recursively merged. All overwritten
     * sub-messages are deep-copied.
     *
     * @param object $msg Protobuf message to be merged from.
     * @return null.
     */
    public function mergeFrom($msg)
    {
        if (get_class($this) !== get_class($msg)) {
            user_error("Cannot merge messages with different class.");
            return;
        }

        foreach ($this->desc->getField() as $field) {
            $setter = $field->getSetter();
            $getter = $field->getGetter();
            if ($field->isMap()) {
                if (count($msg->$getter()) != 0) {
                    $value_field = $field->getMessageType()->getFieldByNumber(2);
                    foreach ($msg->$getter() as $key => $value) {
                        if ($value_field->getType() == GPBType::MESSAGE) {
                            $klass = $value_field->getMessageType()->getClass();
                            $copy = new $klass;
                            $copy->mergeFrom($value);

                            $this->kvUpdateHelper($field, $key, $copy);
                        } else {
                            $this->kv