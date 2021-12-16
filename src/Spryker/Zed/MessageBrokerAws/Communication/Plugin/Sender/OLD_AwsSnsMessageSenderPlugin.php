<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MessageBrokerAws\Communication\Plugin\Sender;

use AsyncAws\Sns\SnsClient;
use AsyncAws\Sns\ValueObject\MessageAttributeValue;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\MessageBrokerExtension\Dependecy\Plugin\MessageSenderPluginInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\TransportException;
use Symfony\Component\Messenger\Transport\Serialization\PhpSerializer;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Throwable;

/**
 * @method \Spryker\Zed\MessageBroker\MessageBrokerConfig getConfig()
 * @method \Spryker\Zed\MessageBroker\Business\MessageBrokerFacadeInterface getFacade()
 */
class OLDAwsSnsMessageSenderPlugin extends AbstractPlugin implements MessageSenderPluginInterface
{
    /**
     * @var string
     */
    private const MESSAGE_ATTRIBUTE_NAME = 'X-Symfony-Messenger';

    /**
     * @var \Symfony\Component\Messenger\Transport\Serialization\SerializerInterface
     */
    private $serializer;

    /**
     * @var \AsyncAws\Sns\SnsClient
     */
    private $sns;

    /**
     * @var string
     */
    private $topic;

    /**
     * @param \AsyncAws\Sns\SnsClient $sns
     * @param \Symfony\Component\Messenger\Transport\Serialization\SerializerInterface $serializer
     * @param string $topic
     */
    public function __construct(SnsClient $sns, SerializerInterface $serializer, string $topic)
    {
        $this->sns = $sns;
        $this->serializer = $serializer ?? new PhpSerializer();
        $this->topic = $topic;
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Symfony\Component\Messenger\Envelope $envelope
     *
     * @throws \Symfony\Component\Messenger\Exception\TransportException
     *
     * @return \Symfony\Component\Messenger\Envelope
     */
    public function send(Envelope $envelope): Envelope
    {
        $encodedMessage = $this->serializer->encode($envelope);
        $headers = $encodedMessage['headers'] ?? [];
        $arguments = [
            'Message' => $encodedMessage['body'],
            'TopicArn' => $this->topic,
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

        if (!empty($specialHeaders)) {
            $arguments['MessageAttributes'][static::MESSAGE_ATTRIBUTE_NAME] = new MessageAttributeValue([
                'DataType' => 'String',
                'StringValue' => json_encode($specialHeaders),
            ]);
        }

        try {
            $result = $this->sns->publish($arguments);
            $messageId = $result->getMessageId();
        } catch (Throwable $e) {
            throw new TransportException($e->getMessage(), 0, $e);
        }

        if ($messageId === null) {
            throw new TransportException('Could not add a message to the SNS topic');
        }

        return $envelope;
    }
}
