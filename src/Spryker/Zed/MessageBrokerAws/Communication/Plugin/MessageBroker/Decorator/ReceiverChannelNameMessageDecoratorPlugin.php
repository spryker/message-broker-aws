<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MessageBrokerAws\Communication\Plugin\MessageBroker\Decorator;

use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\MessageBrokerAws\Business\Sender\Stamp\ReceiverChannelNameStamp;
use Spryker\Zed\MessageBrokerExtension\Dependecy\Plugin\MessageDecoratorPluginInterface;
use Symfony\Component\Messenger\Envelope;

/**
 * @method \Spryker\Zed\MessageBrokerAws\MessageBrokerAwsConfig getConfig()
 * @method \Spryker\Zed\MessageBrokerAws\Business\MessageBrokerAwsFacadeInterface getFacade()
 */
class ReceiverChannelNameMessageDecoratorPlugin extends AbstractPlugin implements MessageDecoratorPluginInterface
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
    public function decorateMessage(Envelope $envelope): Envelope
    {
        $receiverChannelNameStamp = new ReceiverChannelNameStamp(
            $this->getFacade()->getReceiverChannelNameForMessage($envelope),
        );

        return $envelope->with($receiverChannelNameStamp);
    }
}
