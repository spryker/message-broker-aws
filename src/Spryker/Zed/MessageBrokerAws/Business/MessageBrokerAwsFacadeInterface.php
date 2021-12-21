<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MessageBrokerAws\Business;

use Symfony\Component\Messenger\Envelope;

interface MessageBrokerAwsFacadeInterface
{
    /**
     * Specification:
     *
     * @api
     *
     * @param \Symfony\Component\Messenger\Envelope $envelope
     *
     * @return \Symfony\Component\Messenger\Envelope
     */
    public function sendSns(Envelope $envelope): Envelope;

    /**
     * Specification:
     *
     * @api
     *
     * @param string $channelName
     *
     * @return iterable
     */
    public function getSqs(string $channelName): iterable;

    /**
     * Specification:
     *
     * @api
     *
     * @param \Symfony\Component\Messenger\Envelope $envelope
     *
     * @return void
     */
    public function ack(Envelope $envelope): void;

    /**
     * Specification:
     *
     * @api
     *
     * @param \Symfony\Component\Messenger\Envelope $envelope
     *
     * @return void
     */
    public function reject(Envelope $envelope): void;

    /**
     * Specification:
     *
     * @api
     *
     * @param \Symfony\Component\Messenger\Envelope $envelope
     *
     * @return string
     */
    public function getReceiverChannelNameForMessage(Envelope $envelope): string;
}
