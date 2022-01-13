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
    public const SQS_RECEIVER_CONFIG = 'MESSAGE_BROKER:AWS_SQS_RECEIVER_CONFIG';

    /**
     * @var string
     */
    public const CHANNEL_TO_SENDER_CLIENT_MAP = 'MESSAGE_BROKER:CHANNEL_TO_SENDER_CLIENT_MAP';

    /**
     * @var string
     */
    public const CHANNEL_TO_RECEIVER_CLIENT_MAP = 'MESSAGE_BROKER:CHANNEL_TO_RECEIVER_CLIENT_MAP';

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
}
