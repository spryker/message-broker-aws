<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MessageBrokerAws\Business\Sender\Client;

use AsyncAws\Sns\SnsClient;
use AsyncAws\Sns\SnsClient as AsyncAwsSnsClient;
use AsyncAws\Sns\ValueObject\MessageAttributeValue;
use Spryker\Zed\MessageBrokerAws\Business\Config\ConfigFormatterInterface;
use Spryker\Zed\MessageBrokerAws\MessageBrokerAwsConfig;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\TransportException;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Throwable;

class SnsSenderClient implements SenderClientInterface
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
     * @param \Spryker\Zed\MessageBrokerAws\MessageBrokerAwsConfig $config
     * @param \Symfony\Component\Messenger\Transport\Serialization\SerializerInterface $serializer
     * @param \Spryker\Zed\MessageBrokerAws\Business\Config\ConfigFormatterInterface $configFormatter
     */
    public function __construct(MessageBrokerAwsConfig $config, SerializerInterface $serializer, ConfigFormatterInterface $configFormatter)
    {
        $this->config = $config;
        $this->serializer = $serializer;
        $this->configFormatter = $configFormatter;
    }

    /**
     * @param \Symfony\Component\Messenger\Envelope $envelope
     *
     * @throws \Symfony\Component\Messenger\Exception\TransportException
     *
     * @return \Symfony\Component\Messenger\Envelope
     */
    public function send(Envelope $envelope): Envelope
    {
        $snsConfiguration = $this->getConfiguration();
        $topic = $snsConfiguration['topic'] ?? '';
        unset($snsConfiguration['topic']);

        $snsClient = $this->createSenderClient($snsConfiguration);

        $encodedMessage = $this->serializer->encode($envelope);
        $headers = $encodedMessage['headers'] ?? [];
        $arguments = [
            'Message' => $encodedMessage['body'],
            'TopicArn' => $topic,
        ];

        $specialHeaders = [];
        foreach ($headers as $name => $value) {
            if ($name[0] === '.' || $name === static::MESSAGE_ATTRIBUTE_NAME || strlen($name) > 256 || substr($name, -1) === '.' || substr($name, 0, strlen('AWS.')) === 'AWS.' || substr($name, 0, strlen('Amazon.')) === 'Amazon.' || preg_match('/([^a-zA-Z0-9_\.-]+|\.\.)/', $name)) {
                $specialHeaders[$name] = $value;

                continue;
            }

            $arguments['MessageAttributes'][$name] = new MessageAttributeValue([
                'DataType' => 'String',
                'StringValue' => $value,
            ]);
        }

        if ($specialHeaders) {
            $arguments['MessageAttributes'][static::MESSAGE_ATTRIBUTE_NAME] = new MessageAttributeValue([
                'DataType' => 'String',
                'StringValue' => json_encode($specialHeaders),
            ]);
        }

        try {
            $result = $snsClient->publish($arguments);
            $messageId = $result->getMessageId();
        } catch (Throwable $e) {
            throw new TransportException($e->getMessage(), 0, $e);
        }

        if ($messageId === null) {
            throw new TransportException('Could not add a message to the SNS topic');
        }

        return $envelope;
    }

    /**
     * @param array $configuration
     *
     * @return \AsyncAws\Sns\SnsClient
     */
    protected function createSenderClient(array $configuration): SnsClient
    {
        return new AsyncAwsSnsClient($configuration);
    }

    /**
     * TODO This method should be aware of configurations for channels.
     *
     * @return array
     */
    protected function getConfiguration(): array
    {
        $snsSenderConfig = $this->config->getSnsSenderConfig();

        if (is_string($snsSenderConfig)) {
            $snsSenderConfig = $this->configFormatter->format($snsSenderConfig);
        }

        $snsSenderConfig['debug'] = $this->config->getIsDebugEnabled();

        return $snsSenderConfig;
    }
}