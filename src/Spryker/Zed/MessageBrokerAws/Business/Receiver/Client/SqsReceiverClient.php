<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MessageBrokerAws\Business\Receiver\Client;

use AsyncAws\Sns\SnsClient;
use AsyncAws\Sns\SnsClient as AsyncAwsSnsClient;
use AsyncAws\Sqs\SqsClient;
use Spryker\Zed\MessageBrokerAws\Business\Config\ConfigFormatterInterface;
use Spryker\Zed\MessageBrokerAws\MessageBrokerAwsConfig;
use Symfony\Component\Messenger\Bridge\AmazonSqs\Transport\AmazonSqsReceiver;
use Symfony\Component\Messenger\Bridge\AmazonSqs\Transport\Connection;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Receiver\ReceiverInterface;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

class SqsReceiverClient implements ReceiverClientInterface
{
    /**
     * @var string
     */
    protected const MESSAGE_ATTRIBUTE_NAME = 'X-Symfony-Messenger';

    /**
     * @var \Spryker\Zed\MessageBrokerAws\MessageBrokerAwsConfig
     */
    protected MessageBrokerAwsConfig $config;

    /**
     * @var \Symfony\Component\Messenger\Transport\Serialization\SerializerInterface
     */
    protected SerializerInterface $serializer;

    /**
     * @var \Spryker\Zed\MessageBrokerAws\Business\Config\ConfigFormatterInterface
     */
    protected ConfigFormatterInterface $configFormatter;

    /**
     * @var array|null
     */
    protected ?array $sqsConfiguration = null;

    /**
     * @param \Spryker\Zed\MessageBrokerAws\MessageBrokerAwsConfig $config
     * @param \Symfony\Component\Messenger\Transport\Serialization\SerializerInterface $serializer
     */
    public function __construct(MessageBrokerAwsConfig $config, SerializerInterface $serializer, ConfigFormatterInterface $configFormatter)
    {
        $this->config = $config;
        $this->serializer = $serializer;
        $this->configFormatter = $configFormatter;
    }

    /**
     * @return iterable
     */
    public function get(): iterable
    {
        return $this->createReceiverClient()->get();
    }

    /**
     * @return void
     */
    public function ack(Envelope $envelope): void
    {
        $this->createReceiverClient()->ack($envelope);
    }

    /**
     * @return void
     */
    public function reject(Envelope $envelope): void
    {
        $this->createReceiverClient()->reject($envelope);
    }

    /**
     * @return AmazonSqsReceiver
     */
    protected function createReceiverClient(): AmazonSqsReceiver
    {
        $configuration = $this->getConfiguration();
        $connection = new Connection($configuration, $this->createSqsClient(), $connectionConfiguration['queueUrl'] ?? null);

        return new AmazonSqsReceiver($connection, $this->serializer);
    }

    /**
     * @return SqsClient
     */
    protected function createSqsClient(): SqsClient
    {
        $configuration = $this->getConfiguration();
        $options = [
            'endpoint' => null,
            'accessKeyId' => null,
            'accessKeySecret' => null,
            'region' => null,
        ];

        return new SqsClient(array_intersect_key($configuration, $options));
    }

    /**
     * @return array
     */
    protected function getConfiguration(): array
    {
        if (!$this->sqsConfiguration){
            $sqsReceiverConfig = $this->config->getSqsReceiverConfig();

            if (is_string($sqsReceiverConfig)) {
                $sqsReceiverConfig = $this->configFormatter->format($sqsReceiverConfig);
            }

            $sqsReceiverConfig['debug'] = $this->config->getIsDebugEnabled();

            $this->sqsConfiguration = $sqsReceiverConfig;
        }

        return $this->sqsConfiguration;
    }
}
