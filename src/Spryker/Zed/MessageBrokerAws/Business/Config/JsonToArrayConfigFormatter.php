<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MessageBrokerAws\Business\Config;

use Exception;

class JsonToArrayConfigFormatter implements ConfigFormatterInterface
{
    /**
     * @param string $config
     *
     * @throws \Exception
     *
     * @return array<string, mixed>
     */
    public function format(string $config): array
    {
        $formattedConfig = json_decode($config, true);

        if (json_last_error()) {
            throw new Exception(json_last_error_msg());
        }

        return $formattedConfig;
    }
}
