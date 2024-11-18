<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# NO CHECKED-IN PROTOBUF GENCODE
# source: temporal/api/namespace/v1/message.proto

namespace Temporal\Api\PBNamespace\V1;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>temporal.api.namespace.v1.BadBinaryInfo</code>
 */
class BadBinaryInfo extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>string reason = 1;</code>
     */
    protected $reason = '';
    /**
     * Generated from protobuf field <code>string operator = 2;</code>
     */
    protected $operator = '';
    /**
     * Generated from protobuf field <code>.google.protobuf.Timestamp create_time = 3;</code>
     */
    protected $create_time = null;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $reason
     *     @type string $operator
     *     @type \Google\Protobuf\Timestamp $create_time
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Temporal\Api\PBNamespace\V1\Message::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>string reason = 1;</code>
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * Generated from protobuf field <code>string reason = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setReason($var)
    {
        GPBUtil::checkString($var, True);
        $this->reason = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string operator = 2;</code>
     * @return string
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * Generated from protobuf field <code>string operator = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setOperator($var)
    {
        GPBUtil::checkString($var, True);
        $this->operator = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>.google.protobuf.Timestamp create_time = 3;</code>
     * @return \Google\Protobuf\Timestamp|null
     */
    public function getCreateTime()
    {
        return $this->create_time;
    }

    public function hasCreateTime()
    {
        return isset($this->create_time);
    }

    public function clearCreateTime()
    {
        unset($this->create_time);
    }

    /**
     * Generated from protobuf field <code>.google.protobuf.Timestamp create_time = 3;</code>
     * @param \Google\Protobuf\Timestamp $var
     * @return $this
     */
    public function setCreateTime($var)
    {
        GPBUtil::checkMessage($var, \Google\Protobuf\Timestamp::class);
        $this->create_time = $var;

        return $this;
    }

}

