<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: types.proto

namespace Aelf\Protobuf\Generated;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;
use Google\Protobuf\Internal\GPBWrapperUtils;

/**
 * Generated from protobuf message <code>aelf.protobuf.generated.GetTokenInfoInput</code>
 */
class GetTokenInfoInput extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>string symbol = 1;</code>
     */
    private $symbol = '';

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $symbol
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Types::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>string symbol = 1;</code>
     * @return string
     */
    public function getSymbol()
    {
        return $this->symbol;
    }

    /**
     * Generated from protobuf field <code>string symbol = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setSymbol($var)
    {
        GPBUtil::checkString($var, True);
        $this->symbol = $var;

        return $this;
    }

}
