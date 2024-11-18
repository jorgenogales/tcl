<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# NO CHECKED-IN PROTOBUF GENCODE
# source: centrifugo/proxy/v1/proxy.proto

namespace RoadRunner\Centrifugal\Proxy\DTO\V1;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>centrifugal.centrifugo.proxy.StreamSubscribeResponse</code>
 */
class StreamSubscribeResponse extends \Google\Protobuf\Internal\Message
{
    /**
     * SubscribeResponse may optionally be set in the first message from backend to Centrifugo.
     *
     * Generated from protobuf field <code>.centrifugal.centrifugo.proxy.SubscribeResponse subscribe_response = 1;</code>
     */
    protected $subscribe_response = null;
    /**
     * Publication goes to client. Can't be set in the first message from backend to Centrifugo.
     *
     * Generated from protobuf field <code>.centrifugal.centrifugo.proxy.Publication publication = 2;</code>
     */
    protected $publication = null;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type \RoadRunner\Centrifugal\Proxy\DTO\V1\SubscribeResponse $subscribe_response
     *           SubscribeResponse may optionally be set in the first message from backend to Centrifugo.
     *     @type \RoadRunner\Centrifugal\Proxy\DTO\V1\Publication $publication
     *           Publication goes to client. Can't be set in the first message from backend to Centrifugo.
     * }
     */
    public function __construct($data = NULL) {
        \RoadRunner\Centrifugal\Proxy\DTO\V1\GPBMetadata\Proxy::initOnce();
        parent::__construct($data);
    }

    /**
     * SubscribeResponse may optionally be set in the first message from backend to Centrifugo.
     *
     * Generated from protobuf field <code>.centrifugal.centrifugo.proxy.SubscribeResponse subscribe_response = 1;</code>
     * @return \RoadRunner\Centrifugal\Proxy\DTO\V1\SubscribeResponse|null
     */
    public function getSubscribeResponse()
    {
        return $this->subscribe_response;
    }

    public function hasSubscribeResponse()
    {
        return isset($this->subscribe_response);
    }

    public function clearSubscribeResponse()
    {
        unset($this->subscribe_response);
    }

    /**
     * SubscribeResponse may optionally be set in the first message from backend to Centrifugo.
     *
     * Generated from protobuf field <code>.centrifugal.centrifugo.proxy.SubscribeResponse subscribe_response = 1;</code>
     * @param \RoadRunner\Centrifugal\Proxy\DTO\V1\SubscribeResponse $var
     * @return $this
     */
    public function setSubscribeResponse($var)
    {
        GPBUtil::checkMessage($var, \RoadRunner\Centrifugal\Proxy\DTO\V1\SubscribeResponse::class);
        $this->subscribe_response = $var;

        return $this;
    }

    /**
     * Publication goes to client. Can't be set in the first message from backend to Centrifugo.
     *
     * Generated from protobuf field <code>.centrifugal.centrifugo.proxy.Publication publication = 2;</code>
     * @return \RoadRunner\Centrifugal\Proxy\DTO\V1\Publication|null
     */
    public function getPublication()
    {
        return $this->publication;
    }

    public function hasPublication()
    {
        return isset($this->publication);
    }

    public function clearPublication()
    {
        unset($this->publication);
    }

    /**
     * Publication goes to client. Can't be set in the first message from backend to Centrifugo.
     *
     * Generated from protobuf field <code>.centrifugal.centrifugo.proxy.Publication publication = 2;</code>
     * @param \RoadRunner\Centrifugal\Proxy\DTO\V1\Publication $var
     * @return $this
     */
    public function setPublication($var)
    {
        GPBUtil::checkMessage($var, \RoadRunner\Centrifugal\Proxy\DTO\V1\Publication::class);
        $this->publication = $var;

        return $this;
    }

}
