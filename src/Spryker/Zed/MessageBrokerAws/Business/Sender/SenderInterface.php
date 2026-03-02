<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MessageBrokerAws\Business\Sender;

use Symfony\Component\Messenger\Envelope;

/**
 * @deprecated Will be removed without replacement.
 */
interface SenderInterface
{
    public function send(Envelope $envelope): Envelope;
}
