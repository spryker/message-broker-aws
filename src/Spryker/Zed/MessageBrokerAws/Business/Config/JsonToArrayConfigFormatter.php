<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MessageBrokerAws\Business\Config;

use Exception;
use Spryker\Zed\MessageBrokerAws\Dependency\MessageBrokerAwsToStoreFacadeInterface;

class JsonToArrayConfigFormatter implements ConfigFormatterInterface
{
    /**
     * @var string
     */
    protected const DEFAULT_KEY_IN_CONFIG = 'default';

    /**
     * @var \Spryker\Zed\MessageBrokerAws\Dependency\MessageBrokerAwsToStoreFacadeInterface
     */
    protected $messageBrokerAwsToStoreFacade;

    /**
     * @param \Spryker\Zed\MessageBrokerAws\Dependency\MessageBrokerAwsToStoreFacadeInterface $messageBrokerAwsToStoreFacade
     */
    public function __construct(MessageBrokerAwsToStoreFacadeInterface $messageBrokerAwsToStoreFacade)
    {
        $this->messageBrokerAwsToStoreFacade = $messageBrokerAwsToStoreFacade;
    }

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

        $currentStoreName = $this->messageBrokerAwsToStoreFacade->getCurrentStore()->getName();

        if (
            !array_key_exists(static::DEFAULT_KEY_IN_CONFIG, $formattedConfig) &&
            !array_key_exists($currentStoreName, $formattedConfig)
        ) {
            return $formattedConfig;
        }

        if (
            array_key_exists(static::DEFAULT_KEY_IN_CONFIG, $formattedConfig) &&
            array_key_exists($currentStoreName, $formattedConfig)
        ) {
            $mergedFormattedConfig = array_merge($formattedConfig[static::DEFAULT_KEY_IN_CONFIG], $formattedConfig[$currentStoreName]);

            return $mergedFormattedConfig;
        }

        if (array_key_exists(static::DEFAULT_KEY_IN_CONFIG, $formattedConfig)) {
            return $formattedConfig[static::DEFAULT_KEY_IN_CONFIG];
        }

        return $formattedConfig[$currentStoreName];
    }
}
