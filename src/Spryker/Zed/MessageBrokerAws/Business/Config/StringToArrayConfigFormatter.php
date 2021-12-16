<?php

namespace Spryker\Zed\MessageBrokerAws\Business\Config;

class StringToArrayConfigFormatter implements ConfigFormatterInterface
{
    /**
     * @param string $config
     * @return array
     */
    public function format(string $config): array
    {
        $formattedConfiguration = [];

        $configOptions = explode('&', rtrim($config, '&'));

        foreach ($configOptions as $configOption) {
            [$key, $value] = explode('=', $configOption);
            $formattedConfiguration[$key] = $value;
        }

        return $formattedConfiguration;
    }
}
