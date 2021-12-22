<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MessageBrokerAws\Business\Receiver;

use Spryker\Zed\MessageBroker\Business\Stamp\ChannelNameStamp;
use Spryker\Zed\MessageBrokerAws\Business\Receiver\Client\Locator\ReceiverClientLocatorInterface;
use Spryker\Zed\MessageBrokerAws\MessageBrokerAwsConfig;
use Symfony\Component\Messenger\Envelope;

class Receiver implements ReceiverInterface
{
    protected $currentChannelName;

    /**
     * @var \Spryker\Zed\MessageBrokerAws\MessageBrokerAwsConfig
     */
    protected MessageBrokerAwsConfig $config;

    /**
     * @var \Spryker\Zed\MessageBrokerAws\Business\Receiver\Client\Locator\ReceiverClientLocatorInterface
     */
    protected ReceiverClientLocatorInterface $receiverClientResolver;

    /**
     * @param \Spryker\Zed\MessageBrokerAws\MessageBrokerAwsConfig|null $config
     * @param \Spryker\Zed\MessageBrokerAws\Business\Receiver\Client\Locator\ReceiverClientLocatorInterface|null $receiverClientResolver
     */
    public function __construct(?MessageBrokerAwsConfig $config, ?ReceiverClientLocatorInterface $receiverClientResolver)
    {
        $this->config = $config;
        $this->receiverClientResolver = $receiverClientResolver;
    }

    /**
     * @param string $channelName
     *
     * @return iterable
     */
    public function get(string $channelName): iterable
    {
        return $this->receiverClientResolver
            ->getReceiverClientByChannelName($channelName)
            ->get();
    }

    /**
     * @param \Symfony\Component\Messenger\Envelope $envelope
     *
     * @return void
     */
    public function ack(Envelope $envelope): void
    {
        /** @var \Spryker\Zed\MessageBroker\Business\Stamp\ChannelNameStamp $channelNameStamp */
        $channelNameStamp = $envelope->last(ChannelNameStamp::class);

        if ($channelNameStamp) {
            $this->receiverClientResolver
                ->getReceiverClientByChannelName($channelNameStamp->getChannelName())
                ->ack($envelope);
        }
    }

    /**
     * @param \Symfony\Component\Messenger\Envelope $envelope
     *
     * @return void
     */
    public function reject(Envelope $envelope): void
    {
        /** @var \Spryker\Zed\MessageBroker\Business\Stamp\ChannelNameStamp $channelNameStamp */
        $channelNameStamp = $envelope->last(ChannelNameStamp::class);

        if ($channelNameStamp) {
            $this->receiverClientResolver
                ->getReceiverClientByChannelName($channelNameStamp->getChannelName())
                ->reject($envelope);
        }
    }
}
