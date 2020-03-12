m 0001-01-01T00:00:00Z to
     * 9999-12-31T23:59:59Z inclusive.
     *
     * Generated from protobuf field <code>int64 seconds = 1;</code>
     */
    private $seconds = 0;
    /**
     * Non-negative fractions of a second at nanosecond resolution. Negative
     * second values with fractions must still have non-negative nanos values
     * that count forward in time. Must be from 0 to 999,999,999
     * inclusive.
     *
     * Generated from protobuf field <code>int32 nanos = 2;</code>
     */
    private $nanos = 0;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type int|string $seconds
     *           Represents seconds of UTC time since Unix epoch
     *           1970-01-01T00:00:00Z. Must be from 0001-01-01T00:00:00Z to
     *           9999-12-31T23:59:59Z inclusive.
     *     @type int $nanos
     *           Non-negative fractions of a second at nanosecond resolution. Negative
     *           second values with fractions must still have non-negative nanos values
     *           that count forward in time. Must be from 0 to 999,999,999
     *           inclusive.
     * }
     */
    public function __construct($data = NULL) {
       