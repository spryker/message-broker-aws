<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MessageBrokerAws\Dependency\Facade;

use Generated\Shared\Transfer\StoreTransfer;

/**
 * @deprecated Will be removed without replacement.
 */
interface MessageBrokerAwsToStoreFacadeInterface
{
    public function getCurrentStore(): StoreTransfer;
}
