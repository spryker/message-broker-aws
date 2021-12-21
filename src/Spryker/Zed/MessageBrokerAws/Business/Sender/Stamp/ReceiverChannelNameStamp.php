<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MessageBrokerAws\Business\Sender\Stamp;

use Symfony\Component\Messenger\Stamp\StampInterface;

class ReceiverChannelNameStamp implements StampInterface
{
    /**
     * @var string|null
     */
    protected ?string $channelName = null;

    /**
     * @param string|null $channelName
     */
    public function __construct(?string $channelName = null)
    {
        $this->channelName = $channelName;
    }

    /**
     * @return string
     */
    public function getChannelName(): string
    {
        return $this->channelName;
    }
}
