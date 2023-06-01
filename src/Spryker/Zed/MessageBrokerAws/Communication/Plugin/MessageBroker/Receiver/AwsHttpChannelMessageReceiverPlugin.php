<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MessageBrokerAws\Communication\Plugin\MessageBroker\Receiver;

use Exception;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\MessageBrokerAws\MessageBrokerAwsConfig;
use Spryker\Zed\MessageBrokerExtension\Dependency\Plugin\MessageReceiverPluginInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Receiver\QueueReceiverInterface;

/**
 * @method \Spryker\Zed\MessageBrokerAws\MessageBrokerAwsConfig getConfig()
 * @method \Spryker\Zed\MessageBrokerAws\Business\MessageBrokerAwsFacadeInterface getFacade()
 */
class AwsHttpChannelMessageReceiverPlugin extends AbstractPlugin implements MessageReceiverPluginInterface, QueueReceiverInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return string
     */
    public function getTransportName(): string
    {
        return MessageBrokerAwsConfig::HTTP_CHANNEL_TRANSPORT;
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param array<string> $queueNames
     *
     * @return array<\Symfony\Component\Messenger\Envelope>
     */
    public function getFromQueues(array $queueNames): iterable
    {
        return [];
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     *
     * @api
     *
     * @return array<\Symfony\Component\Messenger\Envelope>
     */
    public function get(): iterable
    {
        return $this->getFacade()->get();
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Symfony\Component\Messenger\Envelope $envelope
     *
     * @return void
     */
    public function ack(Envelope $envelope): void
    {
        $this->getFacade()->ack($envelope);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Symfony\Component\Messenger\Envelope $envelope
     *
     * @return void
     */
    public function reject(Envelope $envelope): void
    {
        $this->getFacade()->reject($envelope);
    }
}
