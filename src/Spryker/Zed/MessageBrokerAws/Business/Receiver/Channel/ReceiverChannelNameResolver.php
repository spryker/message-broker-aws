<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MessageBrokerAws\Business\Receiver\Channel;

use Spryker\Zed\MessageBrokerAws\Business\Config\ConfigFormatterInterface;
use Spryker\Zed\MessageBrokerAws\MessageBrokerAwsConfig;
use Symfony\Component\Messenger\Envelope;

class ReceiverChannelNameResolver implements ReceiverChannelNameResolverInterface
{
    /**
     * @var \Spryker\Zed\MessageBrokerAws\MessageBrokerAwsConfig
     */
    protected MessageBrokerAwsConfig $config;

    /**
     * @var \Spryker\Zed\MessageBrokerAws\Business\Config\ConfigFormatterInterface
     */
    protected ConfigFormatterInterface $configFormatter;

    /**
     * @param \Spryker\Zed\MessageBrokerAws\MessageBrokerAwsConfig $config
     * @param \Spryker\Zed\MessageBrokerAws\Business\Config\ConfigFormatterInterface $configFormatter
     */
    public function __construct(MessageBrokerAwsConfig $config, ConfigFormatterInterface $configFormatter)
    {
        $this->config = $config;
        $this->configFormatter = $configFormatter;
    }

    /**
     * @param \Symfony\Component\Messenger\Envelope $envelope
     *
     * @return string
     */
    public function getReceiverChannelNameForMessage(Envelope $envelope): string
    {
        $channelNameMap = $this->config->getMessageToReceiverChannelMap();

        if (is_string($channelNameMap)) {
            $channelNameMap = $this->configFormatter->format($channelNameMap);
        }

        $messageClassName = get_class($envelope->getMessage());

        return $channelNameMap[$messageClassName];
    }
}
