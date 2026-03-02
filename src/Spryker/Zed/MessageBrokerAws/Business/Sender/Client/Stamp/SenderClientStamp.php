<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MessageBrokerAws\Business\Sender\Client\Stamp;

use Symfony\Component\Messenger\Stamp\StampInterface;

class SenderClientStamp implements StampInterface
{
    /**
     * @var string
     */
    protected string $senderClientName;

    public function __construct(string $senderClientName)
    {
        $this->senderClientName = $senderClientName;
    }

    public function getSenderClientName(): string
    {
        return $this->senderClientName;
    }
}
