<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MessageBrokerAws\Business;

use Spryker\Zed\Kernel\Business\AbstractFacade;
use Symfony\Component\Messenger\Envelope;

/**
 * @method \Spryker\Zed\MessageBrokerAws\Business\MessageBrokerAwsBusinessFactory getFactory()
 */
class MessageBrokerAwsFacade extends AbstractFacade implements MessageBrokerAwsFacadeInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Symfony\Component\Messenger\Envelope $envelope
     *
     * @return \Symfony\Component\Messenger\Envelope
     */
    public function sendSns(Envelope $envelope): Envelope
    {
        return $this->getFactory()->createSender()->send($envelope);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param string $channelName
     *
     * @return iterable
     */
    public function getSqs(string $channelName): iterable
    {
        return $this->getFactory()->createReceiver()->get($channelName);
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
        $this->getFactory()->createReceiver()->ack($envelope);
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
        $this->getFactory()->createReceiver()->reject($envelope);
    }
}