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
    public function send(Envelope $envelope): Envelope
    {
        return $this->getFactory()->createSender()->send($envelope);
    }

    /**
     * Specification:
     *
     * @api
     *
     * @param \Symfony\Component\Messenger\Envelope $envelope
     *
     * @return \Symfony\Component\Messenger\Envelope
     */
    public function sendWithHttp(Envelope $envelope): Envelope
    {
        return $this->getFactory()->createHttpSenderClient()->send($envelope);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param string $channelName
     *
     * @return array<\Symfony\Component\Messenger\Envelope>
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

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return void
     */
    public function createQueues(): void
    {
        $this->getFactory()->createAwsSqsQueuesCreator()->createQueues();
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return void
     */
    public function createTopics(): void
    {
        $this->getFactory()->createAwsSnsTopicCreator()->createTopics();
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return void
     */
    public function subscribeSqsToSns(): void
    {
        $this->getFactory()->createAwsSqsQueueSubscriber()->subscribeSqsToSns();
    }

    /**
     * @param array<string, mixed> $encodedEnvelope
     *
     * @return \Symfony\Component\Messenger\Envelope
     */
    public function createEnvelope(array $encodedEnvelope): Envelope
    {
        return $this->getFactory()->createSerializer()->decode($encodedEnvelope);
    }

    /**
     * @param \Symfony\Component\Messenger\Envelope $envelope
     *
     * @return array
     */
    public function serializeEnvelope(Envelope $envelope): array
    {
        return $this->getFactory()->createSerializer()->encode($envelope);
    }
}
