<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MessageBrokerAws\Business\Receiver;

use Symfony\Component\Messenger\Envelope;

interface ReceiverInterface
{
    /**
     * @param string $channelName
     *
     * @return iterable
     */
    public function get(string $channelName): iterable;

    /**
     * @var \Symfony\Component\Messenger\Envelope $envelope
     *
     * @return void
     */
    public function ack(Envelope $envelope): void;

    /**
     * @var \Symfony\Component\Messenger\Envelope $envelope
     *
     * @return void
     */
    public function reject(Envelope $envelope): void;
}
