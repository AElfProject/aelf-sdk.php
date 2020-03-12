       }
        if (is_a($this, "Google\Protobuf\Timestamp")) {
            $this->mergeFrom(GPBUtil::parseTimestamp($array));
            return;
        }
        if (is_a($this, "Google\Protobuf\Struct")) {
            $fields = $this->getFields();
            foreach($array as $key => $value) {
                $v = new Value();
                $v->mergeFromJsonArray($value);
                $fields[$key] = $v;
            }
        }
        if (is_a($this, "Google\Protobuf\Value")) {
            if (is_bool($array)) {
                $this->setBoolValue($array);
            } elseif (is_string($array)) {
                $this->setStringValue($array);
            } elseif (is_null($array)) {
                $this->setNullValue(0);
            } elseif (is_double($array) || is_integer($array)) {
                $this->setNumberValue($array);
            } elseif (is_array($array)) {
                if (array_values($array) !== $array) {
                    // Associative array
                    $struct_value = $this->getStructValue();
                    if (is_null($struct_value)) {
                        $struct_value = new Struct();
                        $this->setStructValue($struct_value);
                    }
                    foreach ($array as $key => $v) {
                        $value = new Value();
                        $value->mergeFromJsonArray($v);
                        $values = $struct_value->getFields();
                        $values[$key]= $value;
                    }
                } else {
                    // Array
                    $list_value = $this->getListValue();
                    if (is_null($list_value)) {
                        $list_value = new ListValue();
                        $this->setListValue($list_value);
                    }
                    foreach ($array as $v) {
                        $value = new Value();
                        $value->mergeFromJsonArray($v);
         