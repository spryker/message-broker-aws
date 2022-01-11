<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MessageBrokerAws\Communication\Plugin\MessageBroker\Receiver;

use Exception;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\MessageBrokerExtension\Dependecy\Plugin\MessageReceiverPluginInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Receiver\QueueReceiverInterface;

/**
 * @method \Spryker\Zed\MessageBroker\MessageBrokerConfig getConfig()
 * @method \Spryker\Zed\MessageBrokerAws\Business\MessageBrokerAwsFacadeInterface getFacade()
 */
class AwsSqsMessageReceiverPlugin extends AbstractPlugin implements MessageReceiverPluginInterface, QueueReceiverInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return string
     */
    public function getClientName(): string
    {
        return 'sqs';
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param array $queueNames
     *
     * @return iterable
     */
    public function getFromQueues(array $queueNames): iterable
    {
        foreach ($queueNames as $channelName) {
            yield from $this->getFacade()->getSqs($channelName);
        }
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     *
     * @api
     *
     * @return iterable
     */
    public function get(): iterable
    {
        throw new Exception(sprintf('Since we are using channels we can only get messages through the "%s" interface.', QueueReceiverInterface::class));
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
