 has therefore not filled into
 * the request will be reset to their default. If this is unwanted
 * behavior, a specific service may require a client to always specify
 * a field mask, producing an error if not.
 * As with get operations, the location of the resource which
 * describes the updated values in the request message depends on the
 * operation kind. In any case, the effect of the field mask is
 * required to be honored by the API.
 * ## Considerations for HTTP REST
 * The HTTP kind of an update operation which uses a field mask must
 * be set to PATCH instead of PUT in order to satisfy HTTP semantics
 * (PUT must only be used for full updates).
 * # JSON Encoding of Field Masks
 * In JSON, a field mask is encoded as a single string where paths are
 * separated by a comma. Fields name in each path are converted
 * to/from lower-camel naming conventions.
 * As an example, consider the following message declarations:
 *     message Profile {
 *       User user = 1;
 *       Photo photo = 2;
 *     }
 *     message User {
 *       string display_name = 1;
 *       string address = 2;
 *     }
 * In proto a field mask for `Profile` may look as such:
 *     mask {
 *       paths: "user.display_name"
 *       paths: "photo"
 *     }
 * In JSON, the same mask is represented as below:
 *     {
 *       mask: "user.displayName,photo"
 *     }
 * # Field Masks and Oneof Fields
 * Field masks treat fields in oneofs just as regular fields. Consider the
 * following message:
 *     message SampleMessage {
 *       oneof test_oneof {
 *         string name = 4;
 *         SubMessage sub_message = 9;
 *       }
 *     }
 * The field mask can be:
 *     mask {
 *       paths: "name"
 *     }
 * Or:
 *     mask {
 *       paths: "sub_message"
 *     }
 * Note that oneof type names ("test_oneof" in this case) cannot be used in
 * paths.
 * ## Field Mask Verification
 * The implementation of any API method which has a FieldMask type field in the
 * request should verify the included field paths, and return an
 * `INVALID_ARGUMENT` error if any path is duplicated or unmappable.
 *
 * Generated from protobuf message <code>google.protobuf.FieldMask</code>
 */
class FieldMask extends \Google\Protobuf\Internal\Message
{
    /**
     * The set of field mask paths.
     *
     * Generated from protobuf field <code>repeated string paths = 1;</code>
     */
    private $paths;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string[]|\Google\Protobuf\Internal\RepeatedField $paths
     *           The set of field mask paths.
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Google\Protobuf\FieldMask::initOnce();
        parent::__construct($data);
    }

    /**
     * The set of field mask paths.
     *
