<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\MessageBrokerAws\Helper;

use AsyncAws\Core\Exception\Http\NetworkException;
use AsyncAws\Sns\Result\PublishResponse;
use AsyncAws\Sns\SnsClient;
use Codeception\Module;
use Codeception\Stub;
use Codeception\TestInterface;
use Exception;
use Generated\Shared\DataBuilder\MessageAttributesBuilder;
use Generated\Shared\DataBuilder\MessageBrokerTestMessageBuilder;
use Generated\Shared\Transfer\MessageAttributesTransfer;
use Generated\Shared\Transfer\MessageBrokerTestMessageTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Ramsey\Uuid\Uuid;
use Spryker\Zed\MessageBrokerAws\Business\MessageBrokerAwsBusinessFactory;
use Spryker\Zed\MessageBrokerAws\Business\Sender\Client\SnsSenderClient;
use Spryker\Zed\MessageBrokerAws\Business\Sender\Client\SqsSenderClient;
use Spryker\Zed\MessageBrokerAws\Communication\Plugin\MessageBroker\Sender\AwsSnsMessageSenderPlugin;
use Spryker\Zed\MessageBrokerAws\Communication\Plugin\MessageBroker\Sender\AwsSqsMessageSenderPlugin;
use Spryker\Zed\MessageBrokerAws\Dependency\Facade\MessageBrokerAwsToStoreBridge;
use Spryker\Zed\MessageBrokerAws\Dependency\Service\MessageBrokerAwsToUtilEncodingServiceBridge;
use SprykerTest\Shared\Testify\Helper\DependencyHelperTrait;
use SprykerTest\Zed\Testify\Helper\Business\BusinessHelperTrait;
use Symfony\Component\Messenger\Bridge\AmazonSqs\Transport\AmazonSqsSender;
use Symfony\Component\Messenger\Envelope;

class MessageBrokerAwsHelper extends Module
{
    use BusinessHelperTrait;
    use DependencyHelperTrait;

    /**
     * @var string
     */
    protected string $localstackEndpoint = 'http://localhost.localstack.cloud:4566';

    public function _before(TestInterface $test): void
    {
        parent::_before($test);

        putenv('SPRYKER_MESSAGE_TO_CHANNEL_MAP');
        putenv('SPRYKER_CHANNEL_TO_SENDER_TRANSPORT_MAP');
        putenv('SPRYKER_CHANNEL_TO_RECEIVER_TRANSPORT_MAP');
        putenv('SPRYKER_MESSAGE_BROKER_SNS_SENDER_CONFIG');
        putenv('SPRYKER_MESSAGE_BROKER_SQS_SENDER_CONFIG');
        putenv('SPRYKER_MESSAGE_BROKER_SQS_RECEIVER_CONFIG');
    }

    public function createMessageAttributesTransfer(array $seed = []): MessageAttributesTransfer
    {
        return (new MessageAttributesBuilder($seed))
            ->withMetadata()
            ->build();
    }

    public function createMessageBrokerTestMessageTransfer(array $seed = []): MessageBrokerTestMessageTransfer
    {
        return (new MessageBrokerTestMessageBuilder($seed))->build();
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

        $messageAttributesTransfer = new MessageAttributesTransfer();
        $messageAttributesTransfer->setEvent('MessageBrokerTestMessage');
        $messageAttributesTransfer->setCorrelationId(Uuid::uuid4()->toString());
        $messageAttributesTransfer->setTimestamp(microtime());

        $messageBrokerTestMessageTransfer->setMessageAttributes($messageAttributesTransfer);

        $envelope = Envelope::wrap($messageBrokerTestMessageTransfer);

        $this->setMessageToChannelMap(MessageBrokerTestMessageTransfer::class, $channelName);
        $this->setChannelToSenderTransportMap($channelName, 'sns');
        $this->setSnsSenderConfiguration();

        // Act
        $awsMessageSenderPlugin = new AwsSnsMessageSenderPlugin();
        $awsMessageSenderPlugin->setFacade($this->getBusinessHelper()->getFacade());

        return $awsMessageSenderPlugin->send($envelope);
    }

    /**
     * This needs proper localstack setup!
     *
     * @param string $channelName
     *
     * @return \Symfony\Component\Messenger\Envelope
     */
    public function haveSqsMessage(string $channelName = 'channel'): Envelope
    {
        $messageBrokerTestMessageTransfer = $this->createMessageWithRequiredMessageAttributes();

        $envelope = Envelope::wrap($messageBrokerTestMessageTransfer);

        $this->setMessageToChannelMap(MessageBrokerTestMessageTransfer::class, $channelName);
        $this->setChannelToSenderTransportMap($channelName, 'sqs');
        $this->setSqsSenderConfiguration();

        // Act
        $awsSqsMessageSenderPlugin = new AwsSqsMessageSenderPlugin();
        $awsSqsMessageSenderPlugin->setFacade($this->getBusinessHelper()->getFacade());

        try {
            $envelope = $awsSqsMessageSenderPlugin->send($envelope);
        } catch (NetworkException $e) {
            $this->markTestSkipped('Localstack is not running.');
        }

        return $envelope;
    }

    public function createMessageWithRequiredMessageAttributes(): MessageBrokerTestMessageTransfer
    {
        $messageBrokerTestMessageTransfer = new MessageBrokerTestMessageTransfer();
        $messageBrokerTestMessageTransfer->setKey('value');

        $messageAttributesTransfer = new MessageAttributesTransfer();
        $messageAttributesTransfer->setTransferName('MessageBrokerTestMessage');
        $messageAttributesTransfer->setEmitter('Publisher');

        $messageBrokerTestMessageTransfer->setMessageAttributes($messageAttributesTransfer);

        return $messageBrokerTestMessageTransfer;
    }

    public function setMessageToChannelMap(string $messageClassName, string $channelName): void
    {
        putenv(sprintf('SPRYKER_MESSAGE_TO_CHANNEL_MAP={"%s": "%s"}', str_replace('\\', '\\\\', $messageClassName), $channelName));
    }

    public function mockSuccessfulSnsClientSendResponse(): SnsSenderClient
    {
        $publishResponseMock = Stub::make(PublishResponse::class, [
            'getMessageId' => Uuid::uuid4()->toString(),
        ]);

        $awsSnsSenderClientMock = Stub::make(SnsClient::class, [
            'publish' => $publishResponseMock,
        ]);

        return $this->mockSnsSenderClient($awsSnsSenderClientMock);
    }

    public function mockFailingSnsClient(): SnsSenderClient
    {
        $awsSnsSenderClientMock = Stub::make(SnsClient::class, [
            'publish' => function (): void {
                throw new Exception('Some connection error.');
            },
        ]);

        return $this->mockSnsSenderClient($awsSnsSenderClientMock);
    }

    public function mockFailingSnsClientSendResponse(): SnsSenderClient
    {
        $publishResponseMock = Stub::make(PublishResponse::class, [
            'getMessageId' => null,
        ]);
        $awsSnsSenderClientMock = Stub::make(SnsClient::class, [
            'publish' => $publishResponseMock,
        ]);

        return $this->mockSnsSenderClient($awsSnsSenderClientMock);
    }

    protected function mockSnsSenderClient(SnsClient $awsSnsSenderClientMock): SnsSenderClient
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

        $this->mockStoreFacade();
        $this->mockUtilEncodingService();

        return $snsSenderClientMock;
    }

    public function mockSuccessfulSqsClientSendResponse(): SqsSenderClient
    {
        $awsSqsSenderClientMock = Stub::make(AmazonSqsSender::class, [
            'send' => function ($envelope) {
                return $envelope;
            },
        ]);

        return $this->mockSqsSenderClient($awsSqsSenderClientMock);
    }

    protected function mockSqsSenderClient(AmazonSqsSender $awsSqsSenderClientMock): SqsSenderClient
    {
        $sqsSenderClientMock = Stub::construct(
            SqsSenderClient::class,
            [
                $this->getFactory()->getConfig(),
                $this->getFactory()->createSerializer(),
                $this->getFactory()->createConfigFormatter(),
            ],
            [
                'createSenderClient' => $awsSqsSenderClientMock,
            ],
        );

        $this->getBusinessHelper()->mockFactoryMethod('createSqsSenderClient', $sqsSenderClientMock);

        $this->mockStoreFacade();
        $this->mockUtilEncodingService();

        return $sqsSenderClientMock;
    }

    protected function mockStoreFacade(): void
    {
        $storeFacadeMock = Stub::make(
            MessageBrokerAwsToStoreBridge::class,
            [
                'getCurrentStore' => (new StoreTransfer())->setName('foo'),
            ],
        );

        $this->getBusinessHelper()->mockFactoryMethod('getStoreFacade', $storeFacadeMock);
    }

    protected function mockUtilEncodingService(): void
    {
        $encodingService = Stub::make(
            MessageBrokerAwsToUtilEncodingServiceBridge::class,
            [
                'decodeJson' => function ($jsonValue, $assoc, $depth = null, $options = null) {
                    return json_decode($jsonValue, $assoc);
                },
                'encodeJson' => function ($value, $options = null, $depth = null) {
                    return json_encode($value);
                },
            ],
        );

        $this->getBusinessHelper()->mockFactoryMethod('getUtilEncodingService', $encodingService);
    }

    protected function getFactory(): MessageBrokerAwsBusinessFactory
    {
        /** @var \Spryker\Zed\MessageBrokerAws\Business\MessageBrokerAwsBusinessFactory $messageBrokerAwsBusinessFactory */
        $messageBrokerAwsBusinessFactory = $this->getBusinessHelper()->getFactory();

        return $messageBrokerAwsBusinessFactory;
    }

    public function setSnsSenderConfiguration(string $topic = 'arn:aws:sns:eu-central-1:000000000000:message-broker.fifo'): void
    {
        putenv(sprintf('SPRYKER_MESSAGE_BROKER_SNS_SENDER_CONFIG={"endpoint": "http://localhost.localstack.cloud:4566", "accessKeyId": "test", "accessKeySecret": "test", "region": "eu-central-1", "topic": "%s"}', $topic));
    }

    public function resetSnsSenderConfiguration(): void
    {
        putenv('SPRYKER_MESSAGE_BROKER_SNS_SENDER_CONFIG=[]');
    }

    public function setInvalidSnsSenderConfiguration(): void
    {
        putenv('SPRYKER_MESSAGE_BROKER_SNS_SENDER_CONFIG={"endpoint": "invalidValue"}');
    }

    public function setSqsSenderConfiguration(string $queueName = 'message-broker'): void
    {
        putenv(sprintf('SPRYKER_MESSAGE_BROKER_SQS_SENDER_CONFIG={"endpoint": "%s", "accessKeyId": "test", "accessKeySecret": "test", "region": "eu-central-1", "queue_name": "%s", "poll_timeout": "5"}', $this->localstackEndpoint, $queueName));
    }

    public function resetSqsSenderConfiguration(): void
    {
        putenv('SPRYKER_MESSAGE_BROKER_SQS_SENDER_CONFIG=[]');
    }

    public function setInvalidSqsSenderConfiguration(): void
    {
        putenv('SPRYKER_MESSAGE_BROKER_SQS_SENDER_CONFIG={"invalidKey": "invalidValue"}');
    }

    public function setSqsReceiverConfiguration(string $queueName = 'message-broker'): void
    {
        putenv(sprintf('SPRYKER_MESSAGE_BROKER_SQS_RECEIVER_CONFIG={"endpoint": "%s", "accessKeyId": "test", "accessKeySecret": "test", "region": "eu-central-1", "queue_name": "%s", "poll_timeout": "5"}', $this->localstackEndpoint, $queueName));
    }

    public function setHttpSenderConfiguration(): void
    {
        putenv(sprintf('SPRYKER_MESSAGE_BROKER_HTTP_SENDER_CONFIG={"endpoint": "0.0.0.0:8000", "timeout": 20}'));
    }

    public function setChannelToSenderTransportMap(string $channelName, string $client): void
    {
        putenv(sprintf('SPRYKER_CHANNEL_TO_SENDER_TRANSPORT_MAP={"%s": "%s"}', $channelName, $client));
    }

    public function setChannelToReceiverTransportMap(string $channelName, string $client): void
    {
        putenv(sprintf('SPRYKER_CHANNEL_TO_RECEIVER_TRANSPORT_MAP={"%s": "%s"}', $channelName, $client));
    }

    public function assertMessageHasStamp(Envelope $envelope, string $stampClass): void
    {
        $stamp = $envelope->last($stampClass);

        // Assert
        $this->assertNotNull($stamp, sprintf('Expected to have a "%s" stamp but it was not found.', $stampClass));
    }
}
