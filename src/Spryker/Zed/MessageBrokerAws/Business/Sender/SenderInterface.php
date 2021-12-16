<?php

namespace Spryker\Zed\MessageBrokerAws\Business\Sender;

use Symfony\Component\Messenger\Envelope;

interface SenderInterface
{
    /**
     * @param Envelope $envelope
     * @return Envelope
     */
    public function send(Envelope $envelope): Envelope;
}
