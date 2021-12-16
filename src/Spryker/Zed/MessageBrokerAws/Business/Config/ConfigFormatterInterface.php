<?php

namespace Spryker\Zed\MessageBrokerAws\Business\Config;

interface ConfigFormatterInterface
{
    /**
     * @param string $config
     * @return array
     */
    public function format(string $config): array;
}
