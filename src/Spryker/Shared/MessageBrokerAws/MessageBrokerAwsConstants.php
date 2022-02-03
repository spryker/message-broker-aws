<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Shared\MessageBrokerAws;

/**
 * Declares global environment configuration keys. Do not use it for other class constants.
 */
interface MessageBrokerAwsConstants
{
    /**
     * @var string
     */
    public const SNS_SENDER_CONFIG = 'MESSAGE_BROKER:AWS_SNS_SENDER_CONFIG';

    /**
     * @var string
     */
    public const SQS_SENDER_CONFIG = 'MESSAGE_BROKER:AWS_SQS_SENDER_CONFIG';

    /**
     * @var string
     */
    public const HTTP_SENDER_CONFIG = 'MESSAGE_BROKER:HTTP_SENDER_CONFIG';

    /**
     * @var string
     */
    public const SQS_RECEIVER_CONFIG = 'MESSAGE_BROKER:AWS_SQS_RECEIVER_CONFIG';

    /**
     * @var string
     */
    public const CHANNEL_TO_SENDER_TRANSPORT_MAP = 'MESSAGE_BROKER:CHANNEL_TO_SENDER_TRANSPORT_MAP';

    /**
     * @var string
     */
    public const CHANNEL_TO_RECEIVER_TRANSPORT_MAP = 'MESSAGE_BROKER:CHANNEL_TO_RECEIVER_TRANSPORT_MAP';

    /**
     * @uses \Spryker\Shared\MessageBroker\MessageBrokerConstants::MESSAGE_TO_CHANNEL_MAP
     *
     * @var string
     */
    public const MESSAGE_TO_CHANNEL_MAP = 'MESSAGE_BROKER:MESSAGE_TO_CHANNEL_MAP';

    /**
     * @var string
     */
    public const DEBUG_ENABLED = 'MESSAGE_BROKER:DEBUG_ENABLED';

    /**
     * Specification:
     * - Defines the list of AWS SQS queue names.
     *
     * @api
     *
     * @var string
     */
    public const SQS_AWS_CREATOR_QUEUES_NAMES= 'MESSAGE_BROKER_AWS:SQS_AWS_CREATOR_QUEUES_NAMES';

    /**
     * Specification:
     * - Defines AWS SQS API secret key.
     *
     * @api
     *
     * @var string
     */
    public const SQS_AWS_SECRET_ACCESS = 'MESSAGE_BROKER_AWS:SQS_AWS_SECRET_ACCESS';

    /**
     * Specification:
     * - Defines AWS SQS API access secret.
     *
     * @api
     *
     * @var string
     */
    public const SQS_AWS_ACCESS_KEY = 'MESSAGE_BROKER_AWS:SQS_AWS_ACCESS_KEY';

    /**
     * Specification:
     * - Defines AWS SQS API endpoint.
     *
     * @api
     *
     * @var string
     */
    public const SQS_AWS_ENDPOINT = 'MESSAGE_BROKER_AWS:SQS_AWS_ENDPOINT';

    /**
     * Specification:
     * - Defines AWS SQS API region.
     *
     * @api
     *
     * @var string
     */
    public const SQS_AWS_REGION = 'MESSAGE_BROKER_AWS:SQS_AWS_REGION';
}
