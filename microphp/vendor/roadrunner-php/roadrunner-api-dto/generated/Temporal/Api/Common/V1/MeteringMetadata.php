<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# NO CHECKED-IN PROTOBUF GENCODE
# source: temporal/api/common/v1/message.proto

namespace Temporal\Api\Common\V1;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Metadata relevant for metering purposes
 *
 * Generated from protobuf message <code>temporal.api.common.v1.MeteringMetadata</code>
 */
class MeteringMetadata extends \Google\Protobuf\Internal\Message
{
    /**
     * Count of local activities which have begun an execution attempt during this workflow task,
     * and whose first attempt occurred in some previous task. This is used for metering
     * purposes, and does not affect workflow state.
     * (-- api-linter: core::0141::forbidden-types=disabled
     *     aip.dev/not-precedent: Negative values make no sense to represent. --)
     *
     * Generated from protobuf field <code>uint32 nonfirst_local_activity_execution_attempts = 13;</code>
     */
    protected $nonfirst_local_activity_execution_attempts = 0;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type int $nonfirst_local_activity_execution_attempts
     *           Count of local activities which have begun an execution attempt during this workflow task,
     *           and whose first attempt occurred in some previous task. This is used for metering
     *           purposes, and does not affect workflow state.
     *           (-- api-linter: core::0141::forbidden-types=disabled
     *               aip.dev/not-precedent: Negative values make no sense to represent. --)
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Temporal\Api\Common\V1\Message::initOnce();
        parent::__construct($data);
    }

    /**
     * Count of local activities which have begun an execution attempt during this workflow task,
     * and whose first attempt occurred in some previous task. This is used for metering
     * purposes, and does not affect workflow state.
     * (-- api-linter: core::0141::forbidden-types=disabled
     *     aip.dev/not-precedent: Negative values make no sense to represent. --)
     *
     * Generated from protobuf field <code>uint32 nonfirst_local_activity_execution_attempts = 13;</code>
     * @return int
     */
    public function getNonfirstLocalActivityExecutionAttempts()
    {
        return $this->nonfirst_local_activity_execution_attempts;
    }

    /**
     * Count of local activities which have begun an execution attempt during this workflow task,
     * and whose first attempt occurred in some previous task. This is used for metering
     * purposes, and does not affect workflow state.
     * (-- api-linter: core::0141::forbidden-types=disabled
     *     aip.dev/not-precedent: Negative values make no sense to represent. --)
     *
     * Generated from protobuf field <code>uint32 nonfirst_local_activity_execution_attempts = 13;</code>
     * @param int $var
     * @return $this
     */
    public function setNonfirstLocalActivityExecutionAttempts($var)
    {
        GPBUtil::checkUint32($var);
        $this->nonfirst_local_activity_execution_attempts = $var;

        return $this;
    }

}

