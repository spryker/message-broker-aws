<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MessageBrokerAws\Business\Config;

use Exception;
use InvalidArgumentException;
use Spryker\Zed\MessageBrokerAws\Dependency\MessageBrokerAwsToStoreFacadeInterface;

class JsonToArrayConfigFormatter implements ConfigFormatterInterface
{
    /**
     * @var string
     */
    protected const DEFAULT_CONFIG_KEY = 'default';

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
        $formattedConfig = $this->getFormattedConfiguration($config);

        if ($this->isSimpleConfiguration($formattedConfig)) {
            return $formattedConfig;
        }

        return array_merge(
            $this->getDefaultConfiguration($formattedConfig),
            $this->getStoreConfiguration($formattedConfig)
        );
    }

    /**
     * @param string $config
     *
     * @throws \Exception
     *
     * @return array
     */
    protected function getFormattedConfiguration(string $config): array
    {
        $formattedConfig = json_decode($config, true);

        if (json_last_error()) {
            throw new InvalidArgumentException(json_last_error_msg());
        }

        return $formattedConfig;
    }

    /**
     * @param array $formattedConfig
     *
     * @return bool
     */
    protected function isSimpleConfiguration(array $formattedConfig): bool
    {
        if (empty($formattedConfig)) {
            return true;
        }

        $firstConfigurationValue = reset($formattedConfig);

        return !is_array($firstConfigurationValue);
    }

    /**
     * @param array $formattedConfig
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    protected function getStoreConfiguration(array $formattedConfig): array
    {
        $currentStoreName = $this->messageBrokerAwsToStoreFacade->getCurrentStore()->getName();

        if (!array_key_exists($currentStoreName, $formattedConfig)) {
            throw new InvalidArgumentException(
                sprintf('No configuration for "%s" store found', $currentStoreName)
            );
        }

        return $formattedConfig[$currentStoreName];
    }

    /**
     * @param array $formattedConfig
     *
     * @return array
     */
    protected function getDefaultConfiguration(array $formattedConfig): array
    {
        if (!array_key_exists(static::DEFAULT_CONFIG_KEY, $formattedConfig)) {
            return [];
        }

        return $formattedConfig[static::DEFAULT_CONFIG_KEY];
    }
}
