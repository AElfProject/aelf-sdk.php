->$setter($value);
        }
    }

    /**
     * Tries to normalize the elements in $value into a provided protobuf
     * wrapper type $class. If $value is any type other than array, we do
     * not do any conversion, and instead rely on the existing protobuf
     * type checking. If $value is an array, we process each element and
     * try to convert it to an instance of $class.
     *
     * @param mixed $value The array of values to normalize.
     * @param string $class The expected wrapper class name
     */
    private static function normalizeArrayElementsToMessageType(&$value, $class)
    {
        if (!is_array($value)) {
            // In the case that $value is not an array, we do not want to
            // attempt any conversion. Note that this includes the cases
            // when $value is a RepeatedField of MapField. In those cases,
            // we do not need to convert the elements, as they should
            // already be the correct types.
            return;
        } else {
            // Normalize each element in the array.
            foreach ($value as $key => &$elementValue) {
              self::normalizeToMessageType($elementValue, $class);
            }
        }
    }

    /**
     * Tries to normalize $value into a provided protobuf wrapper type $class.
     * If $value is any type other than an object, we attempt to construct an
     * instance of $class and assign $value to it using the setValue method
     * shared by all wrapper types.
     *
     * This method will raise an error if it receives a type that cannot be
     * assigned to the wrapper type via setValue.
     *
     * @param mixed $value The value to normalize.
     * @param string $class The expected wrapper class name
     */
    private static function normalizeToMessageType(&$value, $class)
    {
        if (is_null($value) || is_object($value)) {
            // This handles the case that $value is an instance of $class. We
            // cho