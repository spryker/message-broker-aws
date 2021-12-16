<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MessageBrokerAws\Business\Receiver\Stamp;

use Symfony\Component\Messenger\Stamp\StampInterface;

class ReceiverStamp implements StampInterface
{
    protected ?string $receiverName = null;

    /**
     * @param string|null $receiverName
     */
    public function __construct(?string $receiverName)
    {
        $this->receiverName = $receiverName;
    }

    /**
     * @return string
     */
    public function getReceiverName(): string
    {
        return $this->receiverName;
    }
}
