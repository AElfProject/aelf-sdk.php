<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: types.proto

namespace Aelf\Protobuf\Generated;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;
use Google\Protobuf\Internal\GPBWrapperUtils;

/**
 * Generated from protobuf message <code>aelf.protobuf.generated.Transaction</code>
 */
class Transaction extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>.aelf.protobuf.generated.Address from_address = 1;</code>
     */
    private $from_address = null;
    /**
     * Generated from protobuf field <code>.aelf.protobuf.generated.Address to_address = 2;</code>
     */
    private $to_address = null;
    /**
     * Generated from protobuf field <code>int64 ref_block_number = 3;</code>
     */
    private $ref_block_number = 0;
    /**
     * Generated from protobuf field <code>bytes ref_block_prefix = 4;</code>
     */
    private $ref_block_prefix = '';
    /**
     * Generated from protobuf field <code>string method_name = 5;</code>
     */
    private $method_name = '';
    /**
     * Generated from protobuf field <code>bytes params = 6;</code>
     */
    private $params = '';
    /**
     * Generated from protobuf field <code>bytes signature = 10000;</code>
     */
    private $signature = '';

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type \Aelf\Protobuf\Generated\Address $from_address
     *     @type \Aelf\Protobuf\Generated\Address $to_address
     *     @type int|string $ref_block_number
     *     @type string $ref_block_prefix
     *     @type string $method_name
     *     @type string $params
     *     @type string $signature
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Types::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>.aelf.protobuf.generated.Address from_address = 1;</code>
     * @return \Aelf\Protobuf\Generated\Address
     */
    public function getFromAddress()
    {
        return $this->from_address;
    }

    /**
     * Generated from protobuf field <code>.aelf.protobuf.generated.Address from_address = 1;</code>
     * @param \Aelf\Protobuf\Generated\Address $var
     * @return $this
     */
    public function setFromAddress($var)
    {   
        
        GPBUtil::checkMessage($var, \Aelf\Protobuf\Generated\Address::class);
        $this->from_address = $var;
        return $this;
    }

    /**
     * Generated from protobuf field <code>.aelf.protobuf.generated.Address to_address = 2;</code>
     * @return \Aelf\Protobuf\Generated\Address
     */
    public function getToAddress()
    {
        return $this->to_address;
    }

    /**
     * Generated from protobuf field <code>.aelf.protobuf.generated.Address to_address = 2;</code>
     * @param \Aelf\Protobuf\Generated\Address $var
     * @return $this
     */
    public function setToAddress($var)
    {
        GPBUtil::checkMessage($var, \Aelf\Protobuf\Generated\Address::class);
        $this->to_address = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int64 ref_block_number = 3;</code>
     * @return int|string
     */
    public function getRefBlockNumber()
    {
        return $this->ref_block_number;
    }

    /**
     * Generated from protobuf field <code>int64 ref_block_number = 3;</code>
     * @param int|string $var
     * @return $this
     */
    public function setRefBlockNumber($var)
    {
        GPBUtil::checkInt64($var);
        $this->ref_block_number = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>bytes ref_block_prefix = 4;</code>
     * @return string
     */
    public function getRefBlockPrefix()
    {
        return $this->ref_block_prefix;
    }

    /**
     * Generated from protobuf field <code>bytes ref_block_prefix = 4;</code>
     * @param string $var
     * @return $this
     */
    public function setRefBlockPrefix($var)
    {
        GPBUtil::checkString($var, False);
        $this->ref_block_prefix = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string method_name = 5;</code>
     * @return string
     */
    public function getMethodName()
    {
        return $this->method_name;
    }

    /**
     * Generated from protobuf field <code>string method_name = 5;</code>
     * @param string $var
     * @return $this
     */
    public function setMethodName($var)
    {
        GPBUtil::checkString($var, True);
        $this->method_name = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>bytes params = 6;</code>
     * @return string
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Generated from protobuf field <code>bytes params = 6;</code>
     * @param string $var
     * @return $this
     */
    public function setParams($var)
    {
        GPBUtil::checkString($var, False);
        $this->params = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>bytes signature = 10000;</code>
     * @return string
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * Generated from protobuf field <code>bytes signature = 10000;</code>
     * @param string $var
     * @return $this
     */
    public function setSignature($var)
    {
        GPBUtil::checkString($var, False);
        $this->signature = $var;

        return $this;
    }

}

