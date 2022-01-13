<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\MessageBrokerAws\Helper;

use AsyncAws\Sns\Result\PublishResponse;
use AsyncAws\Sns\SnsClient;
use Codeception\Module;
use Codeception\Stub;
use Codeception\TestInterface;
use Exception;
use Generated\Shared\Transfer\MessageBrokerTestMessageTransfer;
use Ramsey\Uuid\Uuid;
use Spryker\Zed\MessageBrokerAws\Business\MessageBrokerAwsBusinessFactory;
use Spryker\Zed\MessageBrokerAws\Business\Receiver\Client\Stamp\ChannelNameStamp;
use Spryker\Zed\MessageBrokerAws\Business\Sender\Client\SnsSenderClient;
use Spryker\Zed\MessageBrokerAws\Communication\Plugin\MessageBroker\Sender\AwsSnsMessageSenderPlugin;
use SprykerTest\Zed\Testify\Helper\Business\BusinessHelperTrait;
use Symfony\Component\Messenger\Envelope;

class MessageBrokerAwsHelper extends Module
{
    use BusinessHelperTrait;

    /**
     * @param \Codeception\TestInterface $test
     *
     * @return void
     */
    public function _before(TestInterface $test): void
    {
        parent::_before($test);

        putenv('AOP_MESSAGE_TO_CHANNEL_MAP');
        putenv('AOP_CHANNEL_TO_SENDER_CLIENT_MAP');
        putenv('AOP_CHANNEL_TO_RECEIVER_CLIENT_MAP');
        putenv('AOP_MESSAGE_BROKER_SNS_SENDER_CONFIG');
        putenv('AOP_MESSAGE_BROKER_SQS_SENDER_CONFIG');
        putenv('AOP_MESSAGE_BROKER_SQS_RECEIVER_CONFIG');
    }

    /**
     * This needs proper localstack setup!
     *
     * @param string $channelName
     *
     * @return \Symfony\Component\Messenger\Envelope
     */
    public function haveMessage(string $channelName = 'channel'): Envelope
    {
        $messageBrokerTestMessageTransfer = new MessageBrokerTestMessageTransfer();
        $messageBrokerTestMessageTransfer->setKey('value');

        $channelNameStamp = new ChannelNameStamp($channelName);
        $envelope = Envelope::wrap($messageBrokerTestMessageTransfer, [$channelNameStamp]);

        $this->setMessageSenderChannelNameMap(MessageBrokerTestMessageTransfer::class, $channelName);
        $this->setChannelNameSenderClientMap($channelName, 'sns');
        $this->setSnsSenderClientConfiguration();

        // Act
        $awsMessageSenderPlugin = new AwsSnsMessageSenderPlugin();
        $awsMessageSenderPlugin->setFacade($this->getBusinessHelper()->getFacade());

        return $awsMessageSenderPlugin->send($envelope);
    }

    /**
     * @param string $messageClassName
     * @param string $channelName
     *
     * @return void
     */
    public function setMessageToReceiverSenderChannelNameMap(string $messageClassName, string $channelName): void
    {
        putenv(sprintf('AOP_MESSAGE_TO_CHANNEL_MAP={"%s": "%s"}', str_replace('\\', '\\\\', $messageClassName), $channelName));
    }

    /**
     * @return void
     */
    public function mockSuccessfulSnsClientSendResponse(): void
    {
        $publishResponseMock = Stub::make(PublishResponse::class, [
            'getMessageId' => Uuid::uuid4()->toString(),
        ]);

        $awsSnsSenderClientMock = Stub::make(SnsClient::class, [
            'publish' => $publishResponseMock,
        ]);

        $this->mockSnsSenderClient($awsSnsSenderClientMock);
    }

    /**
     * @return void
     */
    public function mockFailingSnsClient(): void
    {
        $awsSnsSenderClientMock = Stub::make(SnsClient::class, [
            'publish' => function (): void {
                throw new Exception('Some connection error.');
            },
        ]);

        $this->mockSnsSenderClient($awsSnsSenderClientMock);
    }

    /**
     * @return void
     */
    public function mockFailingSnsClientSendResponse(): void
    {
        $publishResponseMock = Stub::make(PublishResponse::class, [
            'getMessageId' => null,
        ]);
        $awsSnsSenderClientMock = Stub::make(SnsClient::class, [
            'publish' => $publishResponseMock,
        ]);

        $this->mockSnsSenderClient($awsSnsSenderClientMock);
    }

    /**
     * @param \AsyncAws\Sns\SnsClient $awsSnsSenderClientMock
     *
     * @return void
     */
    protected function mockSnsSenderClient(SnsClient $awsSnsSenderClientMock): void
    {
        $snsSenderClientMock = Stub::construct(
            SnsSenderClient::class,
            [
                $this->getFactory()->getConfig(),
                $this->getFactory()->createSerializer(),
                $this->getFactory()->createConfigFormatter(),
            ],
            [
                'createSenderClient' => $awsSnsSenderClientMock,
            ],
        );

        $this->getBusinessHelper()->mockFactoryMethod('createSnsSenderClient', $snsSenderClientMock);
    }

    /**
     * @return \Spryker\Zed\MessageBrokerAws\Business\MessageBrokerAwsBusinessFactory
     */
    protected function getFactory(): MessageBrokerAwsBusinessFactory
    {
        /** @var \Spryker\Zed\MessageBrokerAws\Business\MessageBrokerAwsBusinessFactory $messageBrokerAwsBusinessFactory */
        $messageBrokerAwsBusinessFactory = $this->getBusinessHelper()->getFactory();

        return $messageBrokerAwsBusinessFactory;
    }

    /**
     * @param string $topic
     *
     * @return void
     */
    public function setSnsSenderClientConfiguration(string $topic = 'arn:aws:sns:eu-central-1:000000000000:message-broker'): void
    {
        putenv(sprintf('AOP_MESSAGE_BROKER_SNS_SENDER_CONFIG={"endpoint": "http://localhost.localstack.cloud:4566", "accessKeyId": "test", "accessKeySecret": "test", "region": "eu-central-1", "topic": "%s"}', $topic));
    }

    /**
     * @param string $queueName
     *
     * @return void
     */
    public function setSqsSenderClientConfiguration(string $queueName = 'message-broker'): void
    {
//        putenv(sprintf('AOP_MESSAGE_BROKER_SQS_SENDER_CONFIG={"endpoint": "http://localhost.localstack.cloud:4566", "accessKeyId": "test", "accessKeySecret": "test", "region": "eu-central-1", "queue_name": "%s"}', $queueName));
        putenv(sprintf('AOP_MESSAGE_BROKER_SQS_SENDER_CONFIG={"endpoint": "http://host.docker.internal:4566", "accessKeyId": "", "accessKeySecret": "", "region": "eu-central-1", "queue_name": "%s"}', $queueName));
    }

    /**
     * @return void
     */
    public function setHttpSenderClientConfiguration(): void
    {
        putenv(sprintf('AOP_MESSAGE_BROKER_HTTP_SENDER_CONFIG={"endpoint": "0.0.0.0:8000", "timeout": 20}'));
    }

    /**
     * @param string $queueName
     *
     * @return void
     */
    public function setSqsReceiverClientConfiguration(string $queueName = 'message-broker'): void
    {
        putenv(sprintf('AOP_MESSAGE_BROKER_SQS_RECEIVER_CONFIG={"endpoint": "http://localhost.localstack.cloud:4566", "accessKeyId": "test", "accessKeySecret": "test", "region": "eu-central-1", "queue_name": "%s"}', $queueName));
    }

    /**
     * @param string $messageClassName
     * @param string $channelName
     *
     * @return void
     */
    public function setMessageSenderChannelNameMap(string $messageClassName, string $channelName): void
    {
        putenv(sprintf('AOP_MESSAGE_TO_CHANNEL_MAP={"%s": "%s"}', str_replace('\\', '\\\\', $messageClassName), $channelName));
    }

    /**
     * @param string $channelName
     * @param string $client
     *
     * @return void
     */
    public function setChannelNameSenderClientMap(string $channelName, string $client): void
    {
        putenv(sprintf('AOP_CHANNEL_TO_SENDER_CLIENT_MAP={"%s": "%s"}', $channelName, $client));
    }

    /**
     * @param string $channelName
     * @param string $client
     *
     * @return void
     */
    public function setChannelNameReceiverClientMap(string $channelName, string $client): void
    {
        putenv(sprintf('AOP_CHANNEL_TO_RECEIVER_CLIENT_MAP={"%s": "%s"}', $channelName, $client));
    }

    /**
     * @param \Symfony\Component\Messenger\Envelope $envelope
     * @param string $stampClass
     *
     * @return void
     */
    public function assertMessageHasStamp(Envelope $envelope, string $stampClass): void
    {
        $stamp = $envelope->last($stampClass);

        // Assert
        $this->assertNotNull($stamp, sprintf('Expected to have a "%s" stamp but it was not found.', $stampClass));
    }
}
