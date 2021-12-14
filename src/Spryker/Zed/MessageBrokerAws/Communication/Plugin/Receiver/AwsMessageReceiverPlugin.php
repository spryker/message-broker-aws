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
use Symfony\Component\Messenger\Transport\SetupableTransportInterface;

/**
 * @method \Spryker\Zed\MessageBroker\MessageBrokerConfig getConfig()
 * @method \Spryker\Zed\MessageBrokerAws\Business\MessageBrokerAwsFacadeInterface getFacade()
 */
class AwsMessageReceiverPlugin extends AbstractPlugin implements MessageReceiverPluginInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return iterable
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
