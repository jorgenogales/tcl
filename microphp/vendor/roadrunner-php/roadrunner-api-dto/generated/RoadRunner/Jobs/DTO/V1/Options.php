<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# NO CHECKED-IN PROTOBUF GENCODE
# source: jobs/v1/jobs.proto

namespace RoadRunner\Jobs\DTO\V1;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Options message represents all Jobs' options
 *
 * Generated from protobuf message <code>jobs.v1.Options</code>
 */
class Options extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>int64 priority = 1;</code>
     */
    protected $priority = 0;
    /**
     * Generated from protobuf field <code>string pipeline = 2;</code>
     */
    protected $pipeline = '';
    /**
     * Generated from protobuf field <code>int64 delay = 3;</code>
     */
    protected $delay = 0;
    /**
     * new in 2.10
     *
     * Generated from protobuf field <code>bool auto_ack = 6;</code>
     */
    protected $auto_ack = false;
    /**
     *--------------
     * new in 2.11 (kafka related)
     *
     * Generated from protobuf field <code>string topic = 7;</code>
     */
    protected $topic = '';
    /**
     * Generated from protobuf field <code>string metadata = 8;</code>
     */
    protected $metadata = '';
    /**
     * Generated from protobuf field <code>int64 offset = 9;</code>
     */
    protected $offset = 0;
    /**
     * Generated from protobuf field <code>int32 partition = 10;</code>
     */
    protected $partition = 0;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type int|string $priority
     *     @type string $pipeline
     *     @type int|string $delay
     *     @type bool $auto_ack
     *           new in 2.10
     *     @type string $topic
     *          --------------
     *           new in 2.11 (kafka related)
     *     @type string $metadata
     *     @type int|string $offset
     *     @type int $partition
     * }
     */
    public function __construct($data = NULL) {
        \RoadRunner\Jobs\DTO\V1\GPBMetadata\Jobs::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>int64 priority = 1;</code>
     * @return int|string
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Generated from protobuf field <code>int64 priority = 1;</code>
     * @param int|string $var
     * @return $this
     */
    public function setPriority($var)
    {
        GPBUtil::checkInt64($var);
        $this->priority = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string pipeline = 2;</code>
     * @return string
     */
    public function getPipeline()
    {
        return $this->pipeline;
    }

    /**
     * Generated from protobuf field <code>string pipeline = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setPipeline($var)
    {
        GPBUtil::checkString($var, True);
        $this->pipeline = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int64 delay = 3;</code>
     * @return int|string
     */
    public function getDelay()
    {
        return $this->delay;
    }

    /**
     * Generated from protobuf field <code>int64 delay = 3;</code>
     * @param int|string $var
     * @return $this
     */
    public function setDelay($var)
    {
        GPBUtil::checkInt64($var);
        $this->delay = $var;

        return $this;
    }

    /**
     * new in 2.10
     *
     * Generated from protobuf field <code>bool auto_ack = 6;</code>
     * @return bool
     */
    public function getAutoAck()
    {
        return $this->auto_ack;
    }

    /**
     * new in 2.10
     *
     * Generated from protobuf field <code>bool auto_ack = 6;</code>
     * @param bool $var
     * @return $this
     */
    public function setAutoAck($var)
    {
        GPBUtil::checkBool($var);
        $this->auto_ack = $var;

        return $this;
    }

    /**
     *--------------
     * new in 2.11 (kafka related)
     *
     * Generated from protobuf field <code>string topic = 7;</code>
     * @return string
     */
    public function getTopic()
    {
        return $this->topic;
    }

    /**
     *--------------
     * new in 2.11 (kafka related)
     *
     * Generated from protobuf field <code>string topic = 7;</code>
     * @param string $var
     * @return $this
     */
    public function setTopic($var)
    {
        GPBUtil::checkString($var, True);
        $this->topic = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string metadata = 8;</code>
     * @return string
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * Generated from protobuf field <code>string metadata = 8;</code>
     * @param string $var
     * @return $this
     */
    public function setMetadata($var)
    {
        GPBUtil::checkString($var, True);
        $this->metadata = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int64 offset = 9;</code>
     * @return int|string
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * Generated from protobuf field <code>int64 offset = 9;</code>
     * @param int|string $var
     * @return $this
     */
    public function setOffset($var)
    {
        GPBUtil::checkInt64($var);
        $this->offset = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int32 partition = 10;</code>
     * @return int
     */
    public function getPartition()
    {
        return $this->partition;
    }

    /**
     * Generated from protobuf field <code>int32 partition = 10;</code>
     * @param int $var
     * @return $this
     */
    public function setPartition($var)
    {
        GPBUtil::checkInt32($var);
        $this->partition = $var;

        return $this;
    }

}

