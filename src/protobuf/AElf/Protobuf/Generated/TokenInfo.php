<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: types.proto

namespace AElf\Protobuf\Generated;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>AElf.protobuf.generated.TokenInfo</code>
 */
class TokenInfo extends \Google\Protobuf\Internal\Message
{
    /**
     * The symbol of the token.f
     *
     * Generated from protobuf field <code>string symbol = 1;</code>
     */
    protected $symbol = '';
    /**
     * The full name of the token.
     *
     * Generated from protobuf field <code>string token_name = 2;</code>
     */
    protected $token_name = '';
    /**
     * The current supply of the token.
     *
     * Generated from protobuf field <code>int64 supply = 3;</code>
     */
    protected $supply = 0;
    /**
     * The total supply of the token.
     *
     * Generated from protobuf field <code>int64 total_supply = 4;</code>
     */
    protected $total_supply = 0;
    /**
     * The precision of the token.
     *
     * Generated from protobuf field <code>int32 decimals = 5;</code>
     */
    protected $decimals = 0;
    /**
     * The address that created the token.
     *
     * Generated from protobuf field <code>.AElf.protobuf.generated.Address issuer = 6;</code>
     */
    protected $issuer = null;
    /**
     * A flag indicating if this token is burnable.
     *
     * Generated from protobuf field <code>bool is_burnable = 7;</code>
     */
    protected $is_burnable = false;
    /**
     * The chain id of the token.
     *
     * Generated from protobuf field <code>int32 issue_chain_id = 8;</code>
     */
    protected $issue_chain_id = 0;
    /**
     * The amount of issued tokens.
     *
     * Generated from protobuf field <code>int64 issued = 9;</code>
     */
    protected $issued = 0;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $symbol
     *           The symbol of the token.f
     *     @type string $token_name
     *           The full name of the token.
     *     @type int|string $supply
     *           The current supply of the token.
     *     @type int|string $total_supply
     *           The total supply of the token.
     *     @type int $decimals
     *           The precision of the token.
     *     @type \AElf\Protobuf\Generated\Address $issuer
     *           The address that created the token.
     *     @type bool $is_burnable
     *           A flag indicating if this token is burnable.
     *     @type int $issue_chain_id
     *           The chain id of the token.
     *     @type int|string $issued
     *           The amount of issued tokens.
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Types::initOnce();
        parent::__construct($data);
    }

    /**
     * The symbol of the token.f
     *
     * Generated from protobuf field <code>string symbol = 1;</code>
     * @return string
     */
    public function getSymbol()
    {
        return $this->symbol;
    }

    /**
     * The symbol of the token.f
     *
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
     * The full name of the token.
     *
     * Generated from protobuf field <code>string token_name = 2;</code>
     * @return string
     */
    public function getTokenName()
    {
        return $this->token_name;
    }

    /**
     * The full name of the token.
     *
     * Generated from protobuf field <code>string token_name = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setTokenName($var)
    {
        GPBUtil::checkString($var, True);
        $this->token_name = $var;

        return $this;
    }

    /**
     * The current supply of the token.
     *
     * Generated from protobuf field <code>int64 supply = 3;</code>
     * @return int|string
     */
    public function getSupply()
    {
        return $this->supply;
    }

    /**
     * The current supply of the token.
     *
     * Generated from protobuf field <code>int64 supply = 3;</code>
     * @param int|string $var
     * @return $this
     */
    public function setSupply($var)
    {
        GPBUtil::checkInt64($var);
        $this->supply = $var;

        return $this;
    }

    /**
     * The total supply of the token.
     *
     * Generated from protobuf field <code>int64 total_supply = 4;</code>
     * @return int|string
     */
    public function getTotalSupply()
    {
        return $this->total_supply;
    }

    /**
     * The total supply of the token.
     *
     * Generated from protobuf field <code>int64 total_supply = 4;</code>
     * @param int|string $var
     * @return $this
     */
    public function setTotalSupply($var)
    {
        GPBUtil::checkInt64($var);
        $this->total_supply = $var;

        return $this;
    }

    /**
     * The precision of the token.
     *
     * Generated from protobuf field <code>int32 decimals = 5;</code>
     * @return int
     */
    public function getDecimals()
    {
        return $this->decimals;
    }

    /**
     * The precision of the token.
     *
     * Generated from protobuf field <code>int32 decimals = 5;</code>
     * @param int $var
     * @return $this
     */
    public function setDecimals($var)
    {
        GPBUtil::checkInt32($var);
        $this->decimals = $var;

        return $this;
    }

    /**
     * The address that created the token.
     *
     * Generated from protobuf field <code>.AElf.protobuf.generated.Address issuer = 6;</code>
     * @return \AElf\Protobuf\Generated\Address
     */
    public function getIssuer()
    {
        return $this->issuer;
    }

    /**
     * The address that created the token.
     *
     * Generated from protobuf field <code>.AElf.protobuf.generated.Address issuer = 6;</code>
     * @param \AElf\Protobuf\Generated\Address $var
     * @return $this
     */
    public function setIssuer($var)
    {
        GPBUtil::checkMessage($var, \AElf\Protobuf\Generated\Address::class);
        $this->issuer = $var;

        return $this;
    }

    /**
     * A flag indicating if this token is burnable.
     *
     * Generated from protobuf field <code>bool is_burnable = 7;</code>
     * @return bool
     */
    public function getIsBurnable()
    {
        return $this->is_burnable;
    }

    /**
     * A flag indicating if this token is burnable.
     *
     * Generated from protobuf field <code>bool is_burnable = 7;</code>
     * @param bool $var
     * @return $this
     */
    public function setIsBurnable($var)
    {
        GPBUtil::checkBool($var);
        $this->is_burnable = $var;

        return $this;
    }

    /**
     * The chain id of the token.
     *
     * Generated from protobuf field <code>int32 issue_chain_id = 8;</code>
     * @return int
     */
    public function getIssueChainId()
    {
        return $this->issue_chain_id;
    }

    /**
     * The chain id of the token.
     *
     * Generated from protobuf field <code>int32 issue_chain_id = 8;</code>
     * @param int $var
     * @return $this
     */
    public function setIssueChainId($var)
    {
        GPBUtil::checkInt32($var);
        $this->issue_chain_id = $var;

        return $this;
    }

    /**
     * The amount of issued tokens.
     *
     * Generated from protobuf field <code>int64 issued = 9;</code>
     * @return int|string
     */
    public function getIssued()
    {
        return $this->issued;
    }

    /**
     * The amount of issued tokens.
     *
     * Generated from protobuf field <code>int64 issued = 9;</code>
     * @param int|string $var
     * @return $this
     */
    public function setIssued($var)
    {
        GPBUtil::checkInt64($var);
        $this->issued = $var;

        return $this;
    }

}

