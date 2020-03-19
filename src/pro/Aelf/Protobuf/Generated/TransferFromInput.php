<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: types.proto

namespace Aelf\Protobuf\Generated;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>aelf.protobuf.generated.TransferFromInput</code>
 */
class TransferFromInput extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>.aelf.protobuf.generated.Address from = 1;</code>
     */
    protected $from = null;
    /**
     * Generated from protobuf field <code>.aelf.protobuf.generated.Address to = 2;</code>
     */
    protected $to = null;
    /**
     * Generated from protobuf field <code>string symbol = 3;</code>
     */
    protected $symbol = '';
    /**
     * Generated from protobuf field <code>sint64 amount = 4;</code>
     */
    protected $amount = 0;
    /**
     * Generated from protobuf field <code>string memo = 5;</code>
     */
    protected $memo = '';

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type \Aelf\Protobuf\Generated\Address $from
     *     @type \Aelf\Protobuf\Generated\Address $to
     *     @type string $symbol
     *     @type int|string $amount
     *     @type string $memo
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Types::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>.aelf.protobuf.generated.Address from = 1;</code>
     * @return \Aelf\Protobuf\Generated\Address
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Generated from protobuf field <code>.aelf.protobuf.generated.Address from = 1;</code>
     * @param \Aelf\Protobuf\Generated\Address $var
     * @return $this
     */
    public function setFrom($var)
    {
        GPBUtil::checkMessage($var, \Aelf\Protobuf\Generated\Address::class);
        $this->from = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>.aelf.protobuf.generated.Address to = 2;</code>
     * @return \Aelf\Protobuf\Generated\Address
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Generated from protobuf field <code>.aelf.protobuf.generated.Address to = 2;</code>
     * @param \Aelf\Protobuf\Generated\Address $var
     * @return $this
     */
    public function setTo($var)
    {
        GPBUtil::checkMessage($var, \Aelf\Protobuf\Generated\Address::class);
        $this->to = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string symbol = 3;</code>
     * @return string
     */
    public function getSymbol()
    {
        return $this->symbol;
    }

    /**
     * Generated from protobuf field <code>string symbol = 3;</code>
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
     * Generated from protobuf field <code>sint64 amount = 4;</code>
     * @return int|string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Generated from protobuf field <code>sint64 amount = 4;</code>
     * @param int|string $var
     * @return $this
     */
    public function setAmount($var)
    {
        GPBUtil::checkInt64($var);
        $this->amount = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string memo = 5;</code>
     * @return string
     */
    public function getMemo()
    {
        return $this->memo;
    }

    /**
     * Generated from protobuf field <code>string memo = 5;</code>
     * @param string $var
     * @return $this
     */
    public function setMemo($var)
    {
        GPBUtil::checkString($var, True);
        $this->memo = $var;

        return $this;
    }

}

