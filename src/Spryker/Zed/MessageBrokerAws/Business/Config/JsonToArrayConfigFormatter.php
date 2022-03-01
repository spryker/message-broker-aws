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

        $defaultConfiguration = $this->getDefaultConfiguration($formattedConfig);
        $storeConfiguration = $this->getStoreConfiguration($formattedConfig);

        if ($defaultConfiguration === [] && $storeConfiguration === []) {
            throw new InvalidArgumentException(
                sprintf('No default configuration or "%s" store configuration found', $this->getCurrentStore())
            );
        }

        return array_merge($defaultConfiguration, $storeConfiguration);
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
     * @return array
     */
    protected function getStoreConfiguration(array $formattedConfig): array
    {
        $currentStoreName = $this->getCurrentStore();

        if (!array_key_exists($currentStoreName, $formattedConfig)) {
            return [];
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

    /**
     * @return string
     */
    protected function getCurrentStore(): string
    {
        return $this->messageBrokerAwsToStoreFacade->getCurrentStore()->getName();
    }
}
