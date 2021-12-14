<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MessageBrokerAws\Communication\Plugin\Receiver;

use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\MessageBrokerExtension\Dependecy\Plugin\MessageReceiverPluginInterface;
use Symfony\Component\Messenger\Bridge\AmazonSqs\Transport\AmazonSqsReceiver;
use Symfony\Component\Messenger\Envelope;

/**
 * @method \Spryker\Zed\MessageBroker\MessageBrokerConfig getConfig()
 * @method \Spryker\Zed\MessageBroker\Business\MessageBrokerFacadeInterface getFacade()
 */
class AwsSqsMessageReceiverPlugin extends AbstractPlugin implements MessageReceiverPluginInterface
{
    protected AmazonSqsReceiver $receiver;

    /**
     * @param \Symfony\Component\Messenger\Bridge\AmazonSqs\Transport\AmazonSqsReceiver $receiver
     */
    public function __construct(AmazonSqsReceiver $receiver)
    {
        $this->receiver = $receiver;
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return string
     */
    public function getChannelName(): string
    {
        return 'async';
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return iterable
     */
    public function get(): iterable
    {
        return $this->receiver->get();
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
        $this->receiver->ack($envelope);
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
        $this->receiver->reject($envelope);
    }
}
