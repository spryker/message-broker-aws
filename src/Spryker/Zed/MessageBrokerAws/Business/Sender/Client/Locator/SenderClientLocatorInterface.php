<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MessageBrokerAws\Business\Sender\Client\Locator;

use Spryker\Zed\MessageBrokerAws\Business\Sender\Client\SenderClientInterface;

/**
 * @deprecated Will be removed without replacement.
 */
interface SenderClientLocatorInterface
{
    public function getSenderClientByChannelName(string $channelName): SenderClientInterface;
}
