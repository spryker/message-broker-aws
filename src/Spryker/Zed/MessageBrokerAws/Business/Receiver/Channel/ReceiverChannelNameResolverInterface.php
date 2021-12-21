<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MessageBrokerAws\Business\Receiver\Channel;

use Symfony\Component\Messenger\Envelope;

interface ReceiverChannelNameResolverInterface
{
    /**
     * @param \Symfony\Component\Messenger\Envelope $envelope
     *
     * @return mixed
     */
    public function getReceiverChannelNameForMessage(Envelope $envelope);
}
