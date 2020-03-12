 $message = '')
 * @method static void allIpv4($values, $message = '')
 * @method static void allIpv6($values, $message = '')
 * @method static void allEmail($values, $message = '')
 * @method static void allUniqueValues($values, $message = '')
 * @method static void allEq($values, $expect, $message = '')
 * @method static void allNotEq($values, $expect, $message = '')
 * @method static void allSame($values, $expect, $message = '')
 * @method static void allNotSame($values, $expect, $message = '')
 * @method static void allGreaterThan($values, $limit, $message = '')
 * @method static void allGreaterThanEq($values, $limit, $message = '')
 * @method static void allLessThan($values, $limit, $message = '')
 * @method static void allLessThanEq($values, $limit, $message = '')
 * @method static void allRange($values, $min, $max, $message = '')
 * @method static void allOneOf($values, $values, $message = '')
 * @method static void allContains($values, $subString, $message = '')
 * @method static void allNotContains($values, $subString, $message = '')
 * @method static void allNotWhitespaceOnly($values, $message = '')
 * @method static void allStartsWith($values, $prefix, $message = '')
 * @method static void allStartsWithLetter($values, $message = '')
 * @method static void allEndsWith($values, $suffix, $message = '')
 * @method static void allRegex($values, $pattern, $message = '')
 * @method static void allNotRegex($values, $pattern, $message = '')
 * @method static void allUnicodeLetters($values, $message = '')
 * @method static void allAlpha($values, $message = '')
 * @method static void allDigits($values, $message = '')
 * @method static void allAlnum($values, $message = '')
 * @method static void allLower($values, $message = '')
 * @method static void allUpper($values, $message = '')
 * @method static void allLength($values, $length, $message = '')
 * @method static void allMinLength($values, $min, $message = '')
 * @method static void allMaxLength($values, $max, $message = '')
 * @method static void allLengthBetween($values, $min, $max, $message = '')
 * @method static void allFileExists($values, $message = '')
 * @method static void allFile($values, $message = '')
 * @method static void allDirectory($values, $message = '')
 * @method static void allReadable($values, $message = '')
 * @method static void allWritable($values, $message = '')
 * @method static void allClassExists($values, $message = '')
 * @method static void allSubclassOf($values, $class, $message = '')
 * @method static void allInterfaceExists($values, $message = '')
 * @method static void allImplementsInterface($values, $interface, $message = '')
 * @method static void allPropertyExists($values, $property, $message = '')
 * @method static void allPropertyNotExists($values, $property, $message = '')
 * @method static void allMethodExists($values, $method, $message = '')
 * @method static void allMethodNotExists($values, $method, $message = '')
 * @method static void allKeyExists($values, $key, $message = '')
 * @method static void allKeyNotExists($values, $key, $message = '')
 * @method static void allValidArrayKey($values, $message = '')
 * @method static void allCount($values, $key, $message = '')
 * @method static void allMinCount($values, $min, $message = '')
 * @method static void allMaxCount($values, $max, $message = '')
 * @method static void allCountBetween($values, $min, $max, $message = '')
 * @method static void allIsList($values, $message = '')
 * @method static void allIsNonEmptyList($values, $message = '')
 * @method static void allIsMap($values, $message = '')
 * @method static void allIsNonEmptyMap($values, $message = '')
 * @method static void allUuid($values, $message = '')
 * @method static void allThrows($expressions, $class = 'Exception', $message = '')
 *
 * @since  1.0
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class Assert
{
    /**
     * @psalm-assert string $value
     *
     * @param mixed  $value
     * @param string $message
     *
     * @throws InvalidArgumentException
     */
    public static function string($value, $message = '')
    {
        if (!\is_string($value)) {
            static::reportInvalidArgument(\sprintf(
                $message ?: 'Expected a string. Got: %s',
              