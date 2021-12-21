<?php

/**
* Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
* Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
*/

namespace Spryker\Zed\MessageBrokerAws;

use Spryker\Shared\MessageBrokerAws\MessageBrokerAwsConstants;
use Spryker\Zed\Kernel\AbstractBundleConfig;

class MessageBrokerAwsConfig extends AbstractBundleConfig
{
    /**
     * @return string|array
     */
    public function getSnsSenderConfig()
    {
        if (getenv('AOP_MESSAGE_BROKER_SNS_SENDER_CONFIG') !== false) {
            return getenv('AOP_MESSAGE_BROKER_SNS_SENDER_CONFIG');
        }

        if ($this->getConfig()->hasKey(MessageBrokerAwsConstants::SNS_SENDER_CONFIG)) {
            return $this->get(MessageBrokerAwsConstants::SNS_SENDER_CONFIG);
        }

        return [];
    }

    /**
     * @return string|array
     */
    public function getSqsReceiverConfig()
    {
        if (getenv('AOP_MESSAGE_BROKER_SQS_RECEIVER_CONFIG') !== false) {
            return getenv('AOP_MESSAGE_BROKER_SQS_RECEIVER_CONFIG');
        }

        if ($this->getConfig()->hasKey(MessageBrokerAwsConstants::SQS_RECEIVER_CONFIG)) {
            return $this->get(MessageBrokerAwsConstants::SQS_RECEIVER_CONFIG);
        }

        return [];
    }

    /**
     * @return bool
     */
    public function getIsDebugEnabled(): bool
    {
        if (getenv('SPRYKER_DEBUG_ENABLED') !== false) {
            return getenv('SPRYKER_DEBUG_ENABLED');
        }

        if ($this->getConfig()->hasKey(MessageBrokerAwsConstants::DEBUG_ENABLED)) {
            return $this->get(MessageBrokerAwsConstants::DEBUG_ENABLED);
        }

        return false;
    }

    /**
     * @return string|array
     */
    public function getChannelToSenderClientMap()
    {
        if (getenv('AOP_CHANNEL_TO_SENDER_CLIENT_MAP') !== false) {
            return getenv('AOP_CHANNEL_TO_SENDER_CLIENT_MAP');
        }

        if ($this->getConfig()->hasKey(MessageBrokerAwsConstants::CHANNEL_TO_SENDER_CLIENT_MAP)) {
            return $this->get(MessageBrokerAwsConstants::CHANNEL_TO_SENDER_CLIENT_MAP);
        }

        return [];
    }

    /**
     * @return string|array
     */
    public function getChannelToReceiverClientMap()
    {
        if (getenv('AOP_CHANNEL_TO_RECEIVER_CLIENT_MAP') !== false) {
            return getenv('AOP_CHANNEL_TO_RECEIVER_CLIENT_MAP');
        }

        if ($this->getConfig()->hasKey(MessageBrokerAwsConstants::CHANNEL_TO_RECEIVER_CLIENT_MAP)) {
            return $this->get(MessageBrokerAwsConstants::CHANNEL_TO_RECEIVER_CLIENT_MAP);
        }

        return [];
    }

    /**
     * @return string|array
     */
    public function getMessageToSenderChannelMap()
    {
        if (getenv('AOP_MESSAGE_TO_SENDER_CHANNEL_MAP') !== false) {
            return getenv('AOP_MESSAGE_TO_SENDER_CHANNEL_MAP');
        }

        if ($this->getConfig()->hasKey(MessageBrokerAwsConstants::MESSAGE_TO_SENDER_CHANNEL_MAP)) {
            return $this->get(MessageBrokerAwsConstants::MESSAGE_TO_SENDER_CHANNEL_MAP);
        }

        return [];
    }

    /**
     * @return string|array
     */
    public function getMessageToReceiverChannelMap()
    {
        if (getenv('AOP_MESSAGE_TO_RECEIVER_CHANNEL_MAP') !== false) {
            return getenv('AOP_MESSAGE_TO_RECEIVER_CHANNEL_MAP');
        }

        if ($this->getConfig()->hasKey(MessageBrokerAwsConstants::MESSAGE_TO_RECEIVER_CHANNEL_MAP)) {
            return $this->get(MessageBrokerAwsConstants::MESSAGE_TO_RECEIVER_CHANNEL_MAP);
        }

        return [];
    }
}
