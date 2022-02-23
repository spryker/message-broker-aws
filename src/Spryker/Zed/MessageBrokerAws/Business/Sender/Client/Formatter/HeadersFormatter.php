<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MessageBrokerAws\Business\Sender\Client\Formatter;

use Generated\Shared\Transfer\MessageAttributesTransfer;

class HeadersFormatter implements HeadersFormatterInterface
{
    /**
     * @var string
     */
    protected const X_SUFFIX_FOR_HEADER_NAME = 'X-';

    /**
     * @param array<string, string> $headers
     *
     * @return array<string, string>
     */
    public function formatHeadersForHttpRequest(array $headers): array
    {
        $formattedHeaders = [];
        foreach ($headers as $header => $value) {
            if (!property_exists(MessageAttributesTransfer::class, $header)) {
                continue;
            }

            $headerWrappedName = static::X_SUFFIX_FOR_HEADER_NAME . ucfirst(preg_replace('/(?<=[a-z])(?=[A-Z])/', '-', $header));

            if (is_array($value)) {
                $value = json_encode($value);
            }

            $formattedHeaders[$headerWrappedName] = $value;
        }

        return $formattedHeaders;
    }
}
