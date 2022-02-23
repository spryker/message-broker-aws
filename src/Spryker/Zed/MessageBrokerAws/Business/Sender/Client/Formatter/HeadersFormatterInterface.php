<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MessageBrokerAws\Business\Sender\Client\Formatter;

interface HeadersFormatterInterface
{
    /**
     * @param array<string, string> $headers
     *
     * @return array<string, string>
     */
    public function formatHeadersForHttpRequest(array $headers): array;
}
