<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MessageBrokerAws\Communication\Plugin\Sender;

use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\MessageBrokerExtension\Dependecy\Plugin\MessageSenderPluginInterface;
use Symfony\Component\Messenger\Envelope;

/**
 * @method \Spryker\Zed\MessageBroker\MessageBrokerConfig getConfig()
 * @method \Spryker\Zed\MessageBrokerAws\Business\MessageBrokerAwsFacadeInterface getFacade()
 */
class AwsMessageSenderPlugin extends AbstractPlugin implements MessageSenderPluginInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Symfony\Component\Messenger\Envelope $envelope
     *
     * @throws \Symfony\Component\Messenger\Exception\TransportException
     *
     * @return \Symfony\Component\Messenger\Envelope
     */
    public function send(Envelope $envelope): Envelope
    {
        return $this->getFacade()->send($envelope);
    }
}
