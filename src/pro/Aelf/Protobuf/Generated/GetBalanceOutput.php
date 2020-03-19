<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: types.proto

namespace Aelf\Protobuf\Generated;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>aelf.protobuf.generated.GetBalanceOutput</code>
 */
class GetBalanceOutput extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>string symbol = 1;</code>
     */
    protected $symbol = '';
    /**
     * Generated from protobuf field <code>.aelf.protobuf.generated.Address owner = 2;</code>
     */
    protected $owner = null;
    /**
     * Generated from protobuf field <code>sint64 balance = 3;</code>
     */
    protected $balance = 0;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $symbol
     *     @type \Aelf\Protobuf\Generated\Address $owner
     *     @type int|string $balance
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

    /**
     * Generated from protobuf field <code>.aelf.protobuf.generated.Address owner = 2;</code>
     * @return \Aelf\Protobuf\Generated\Address
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Generated from protobuf field <code>.aelf.protobuf.generated.Address owner = 2;</code>
     * @param \Aelf\Protobuf\Generated\Address $var
     * @return $this
     */
    public function setOwner($var)
    {
        GPBUtil::checkMessage($var, \Aelf\Protobuf\Generated\Address::class);
        $this->owner = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>sint64 balance = 3;</code>
     * @return int|string
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * Generated from protobuf field <code>sint64 balance = 3;</code>
     * @param int|string $var
     * @return $this
     */
    public function setBalance($var)
    {
        GPBUtil::checkInt64($var);
        $this->balance = $var;

        return $this;
    }

}

