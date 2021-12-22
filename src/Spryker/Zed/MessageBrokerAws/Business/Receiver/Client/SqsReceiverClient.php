<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MessageBrokerAws\Business\Receiver\Client;

use AsyncAws\Sqs\SqsClient;
use Spryker\Zed\MessageBrokerAws\Business\Config\ConfigFormatterInterface;
use Spryker\Zed\MessageBrokerAws\Business\Receiver\Client\Stamp\ChannelNameStamp;
use Spryker\Zed\MessageBrokerAws\MessageBrokerAwsConfig;
use Symfony\Component\Messenger\Bridge\AmazonSqs\Transport\AmazonSqsReceiver;
use Symfony\Component\Messenger\Bridge\AmazonSqs\Transport\Connection;
use Symfony\Component\Messenger\Envelope;
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
     * @param string $channelName
     *
     * @return iterable
     */
    public function get(string $channelName): iterable
    {
        // TODO the receiver client should be loaded with configuration matching the channel name
        // Channel name is added here
        foreach ($this->createReceiverClient()->get() as $envelope) {
            yield $envelope->with(new ChannelNameStamp($channelName));
        }
    }

    /**
     * @return void
     */
    public function ack(Envelope $envelope): void
    {
        // TODO the receiver client should be loaded with configuration matching the channel name
        // Channel name is added in get method
        $this->createReceiverClient()->ack($envelope);
    }

    /**
     * @return void
     */
    public function reject(Envelope $envelope): void
    {
        // TODO the receiver client should be loaded with configuration matching the channel name
        // Channel name is added in get method
        $this->createReceiverClient()->reject($envelope);
    }

    /**
     * @return \Symfony\Component\Messenger\Bridge\AmazonSqs\Transport\AmazonSqsReceiver
     */
    protected function createReceiverClient(): AmazonSqsReceiver
    {
        $configuration = $this->getConfiguration();
        $connection = new Connection($configuration, $this->createSqsClient(), $connectionConfiguration['queueUrl'] ?? null);

        return new AmazonSqsReceiver($connection, $this->serializer);
    }

    /**
     * @return \AsyncAws\Sqs\SqsClient
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
        if (!$this->sqsConfiguration) {
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
