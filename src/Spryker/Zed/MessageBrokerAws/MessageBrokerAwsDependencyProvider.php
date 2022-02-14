<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MessageBrokerAws;

use Aws\Sns\SnsClient;
use Aws\Sqs\SqsClient;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

/**
 * @method \Spryker\Zed\MessageBrokerAws\MessageBrokerAwsConfig getConfig()
 */
class MessageBrokerAwsDependencyProvider extends AbstractBundleDependencyProvider
{
    /**
     * @var string
     */
    public const CLIENT_AWS_SQS = 'CLIENT_AWS_SQS';

    /**
     * @var string
     */
    public const CLIENT_AWS_SNS = 'CLIENT_AWS_SNS';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container)
    {
        $container = parent::provideBusinessLayerDependencies($container);
        $container = $this->addSqsAwsClient($container);
        $container = $this->addSnsAwsClient($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addSqsAwsClient(Container $container): Container
    {
        $container->set(static::CLIENT_AWS_SQS, function () {
            return new SqsClient([
                'credentials' => [
                    'key' => $this->getConfig()->getSqsAwsAccessKey(),
                    'secret' => $this->getConfig()->getSqsAwsAccessSecret(),
                ],
                'endpoint' => $this->getConfig()->getSqsAwsEndpoint(),
                'region' => $this->getConfig()->getSqsAwsRegion(),
                'version' => $this->getConfig()->getSqsAwsVersion(),
            ]);
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addSnsAwsClient(Container $container): Container
    {
        $container->set(static::CLIENT_AWS_SNS, function () {
            return new SnsClient([
                'credentials' => [
                    'key' => $this->getConfig()->getSqsAwsAccessKey(),
                    'secret' => $this->getConfig()->getSqsAwsAccessSecret(),
                ],
                'endpoint' => $this->getConfig()->getSqsAwsEndpoint(),
                'region' => $this->getConfig()->getSqsAwsRegion(),
                'version' => '2010-03-31',
            ]);
        });

        return $container;
    }
}
