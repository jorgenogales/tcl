<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# NO CHECKED-IN PROTOBUF GENCODE
# source: temporal/api/schedule/v1/message.proto

namespace Temporal\Api\Schedule\V1;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Range represents a set of integer values, used to match fields of a calendar
 * time in StructuredCalendarSpec. If end < start, then end is interpreted as
 * equal to start. This means you can use a Range with start set to a value, and
 * end and step unset (defaulting to 0) to represent a single value.
 *
 * Generated from protobuf message <code>temporal.api.schedule.v1.Range</code>
 */
class Range extends \Google\Protobuf\Internal\Message
{
    /**
     * Start of range (inclusive).
     *
     * Generated from protobuf field <code>int32 start = 1;</code>
     */
    protected $start = 0;
    /**
     * End of range (inclusive).
     *
     * Generated from protobuf field <code>int32 end = 2;</code>
     */
    protected $end = 0;
    /**
     * Step (optional, default 1).
     *
     * Generated from protobuf field <code>int32 step = 3;</code>
     */
    protected $step = 0;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type int $start
     *           Start of range (inclusive).
     *     @type int $end
     *           End of range (inclusive).
     *     @type int $step
     *           Step (optional, default 1).
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Temporal\Api\Schedule\V1\Message::initOnce();
        parent::__construct($data);
    }

    /**
     * Start of range (inclusive).
     *
     * Generated from protobuf field <code>int32 start = 1;</code>
     * @return int
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Start of range (inclusive).
     *
     * Generated from protobuf field <code>int32 start = 1;</code>
     * @param int $var
     * @return $this
     */
    public function setStart($var)
    {
        GPBUtil::checkInt32($var);
        $this->start = $var;

        return $this;
    }

    /**
     * End of range (inclusive).
     *
     * Generated from protobuf field <code>int32 end = 2;</code>
     * @return int
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * End of range (inclusive).
     *
     * Generated from protobuf field <code>int32 end = 2;</code>
     * @param int $var
     * @return $this
     */
    public function setEnd($var)
    {
        GPBUtil::checkInt32($var);
        $this->end = $var;

        return $this;
    }

    /**
     * Step (optional, default 1).
     *
     * Generated from protobuf field <code>int32 step = 3;</code>
     * @return int
     */
    public function getStep()
    {
        return $this->step;
    }

    /**
     * Step (optional, default 1).
     *
     * Generated from protobuf field <code>int32 step = 3;</code>
     * @param int $var
     * @return $this
     */
    public function setStep($var)
    {
        GPBUtil::checkInt32($var);
        $this->step = $var;

        return $this;
    }

}

