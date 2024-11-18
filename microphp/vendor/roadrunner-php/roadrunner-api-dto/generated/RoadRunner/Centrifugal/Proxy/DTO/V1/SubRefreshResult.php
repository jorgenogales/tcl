<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# NO CHECKED-IN PROTOBUF GENCODE
# source: centrifugo/proxy/v1/proxy.proto

namespace RoadRunner\Centrifugal\Proxy\DTO\V1;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>centrifugal.centrifugo.proxy.SubRefreshResult</code>
 */
class SubRefreshResult extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>bool expired = 1;</code>
     */
    protected $expired = false;
    /**
     * Generated from protobuf field <code>int64 expire_at = 2;</code>
     */
    protected $expire_at = 0;
    /**
     * Generated from protobuf field <code>bytes info = 3;</code>
     */
    protected $info = '';
    /**
     * Generated from protobuf field <code>string b64info = 4;</code>
     */
    protected $b64info = '';

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type bool $expired
     *     @type int|string $expire_at
     *     @type string $info
     *     @type string $b64info
     * }
     */
    public function __construct($data = NULL) {
        \RoadRunner\Centrifugal\Proxy\DTO\V1\GPBMetadata\Proxy::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>bool expired = 1;</code>
     * @return bool
     */
    public function getExpired()
    {
        return $this->expired;
    }

    /**
     * Generated from protobuf field <code>bool expired = 1;</code>
     * @param bool $var
     * @return $this
     */
    public function setExpired($var)
    {
        GPBUtil::checkBool($var);
        $this->expired = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int64 expire_at = 2;</code>
     * @return int|string
     */
    public function getExpireAt()
    {
        return $this->expire_at;
    }

    /**
     * Generated from protobuf field <code>int64 expire_at = 2;</code>
     * @param int|string $var
     * @return $this
     */
    public function setExpireAt($var)
    {
        GPBUtil::checkInt64($var);
        $this->expire_at = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>bytes info = 3;</code>
     * @return string
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Generated from protobuf field <code>bytes info = 3;</code>
     * @param string $var
     * @return $this
     */
    public function setInfo($var)
    {
        GPBUtil::checkString($var, False);
        $this->info = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string b64info = 4;</code>
     * @return string
     */
    public function getB64Info()
    {
        return $this->b64info;
    }

    /**
     * Generated from protobuf field <code>string b64info = 4;</code>
     * @param string $var
     * @return $this
     */
    public function setB64Info($var)
    {
        GPBUtil::checkString($var, True);
        $this->b64info = $var;

        return $this;
    }

}

