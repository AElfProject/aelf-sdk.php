otected $leading_comments = '';
    private $has_leading_comments = false;
    /**
     * Generated from protobuf field <code>optional string trailing_comments = 4;</code>
     */
    protected $trailing_comments = '';
    private $has_trailing_comments = false;
    /**
     * Generated from protobuf field <code>repeated string leading_detached_comments = 6;</code>
     */
    private $leading_detached_comments;
    private $has_leading_detached_comments = false;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type int[]|\Google\Protobuf\Internal\RepeatedField $path
     *           Identifies which part of the FileDescriptorProto was defined at this
     *           location.
     *           Each element is a field number or an index.  They form a path from
     *           the root FileDescriptorProto to the place where the definition.  For
     *           example, this path:
     *             [ 4, 3, 2, 7, 1 ]
     *           refers to:
     *             file.message_type(3)  // 4, 3
     *                 .field(7)         // 2, 7
     *                 .name()           // 1
     *           This is because FileDescriptorProto.message_type has field number 4:
     *             repeated DescriptorProto message_type = 4;
     *           and DescriptorProto.field has field number 2:
     *             repeated FieldDescriptorProto field = 2;
     *           and FieldDescriptorProto.name has field number 1:
     *             optional string name = 1;
     *           Thus, the above path gives the location of a field name.  If we removed
     *           the last element:
     *             [ 4, 3, 2, 7 ]
     *           this path refers to the whole field declaration (from the beginning
     *           of the label to the terminating semicolon).
     *     @type int[]|\Google\Protobuf\Internal\RepeatedField $span
     *           Always has exactly three or four elements: start line, start column,
     *           end line (optional, otherwise assumed same