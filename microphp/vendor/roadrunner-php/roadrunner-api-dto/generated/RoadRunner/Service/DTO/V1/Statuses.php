<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# NO CHECKED-IN PROTOBUF GENCODE
# source: service/v1/service.proto

namespace RoadRunner\Service\DTO\V1;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>service.v1.Statuses</code>
 */
class Statuses extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>repeated .service.v1.Status status = 1;</code>
     */
    private $status;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type array<\RoadRunner\Service\DTO\V1\Status>|\Google\Protobuf\Internal\RepeatedField $status
     * }
     */
    public function __construct($data = NULL) {
        \RoadRunner\Service\DTO\V1\GPBMetadata\Service::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>repeated .service.v1.Status status = 1;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Generated from protobuf field <code>repeated .service.v1.Status status = 1;</code>
     * @param array<\RoadRunner\Service\DTO\V1\Status>|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setStatus($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::MESSAGE, \RoadRunner\Service\DTO\V1\Status::class);
        $this->status = $arr;

        return $this;
    }

}

