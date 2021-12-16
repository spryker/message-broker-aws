<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\MessageBrokerAws\Communication\Plugin\Sender;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\MessageBrokerTestMessageTransfer;
use Spryker\Zed\MessageBrokerAws\Communication\Plugin\Sender\AwsSnsMessageSenderPlugin;
use SprykerTest\Zed\MessageBrokerAws\MessageBrokerAwsCommunicationTester;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\TransportException;
use Symfony\Component\Messenger\Stamp\SentStamp;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group MessageBrokerAws
 * @group Communication
 * @group Plugin
 * @group Sender
 * @group AwsMessageSenderPluginTest
 * Add your own group annotations below this line
 */
class AwsMessageSenderPluginTest extends Unit
{
    /**
     * @var string
     */
    public const CHANNEL_NAME = 'channel';

    /**
     * @var \SprykerTest\Zed\MessageBrokerAws\MessageBrokerAwsCommunicationTester
     */
    protected MessageBrokerAwsCommunicationTester $tester;

    /**
     * The message will have a SendStamp only when handled properly.
     *
     * @return void
     */
    public function testSendReturnsUnHandledEnvelopeWhenChannelNameStampDoesNotExist(): void
    {
        // Arrange
        $messageBrokerTestMessageTransfer = new MessageBrokerTestMessageTransfer();
        $messageBrokerTestMessageTransfer->setKey('value');

        $envelope = Envelope::wrap($messageBrokerTestMessageTransfer);

        // Act
        $awsMessageSenderPlugin = new AwsSnsMessageSenderPlugin();
        $awsMessageSenderPlugin->setFacade($this->tester->getFacade());
        $envelope = $awsMessageSenderPlugin->send($envelope);

        // Assert
        /** @var \Symfony\Component\Messenger\Stamp\SentStamp $sentStamp */
        $sentStamp = $envelope->last(SentStamp::class);

        $this->assertNull($sentStamp, sprintf('Expected not to have a "%s" but it is given.', SentStamp::class));
    }

    /**
     * Happy case test.
     *
     * @return void
     */
    public function testSendUsesSnsSenderWhenSnsSenderIsConfiguredForChannel(): void
    {
        // Arrange
        $messageBrokerTestMessageTransfer = new MessageBrokerTestMessageTransfer();
        $messageBrokerTestMessageTransfer->setKey('value');

        $envelope = Envelope::wrap($messageBrokerTestMessageTransfer);

        $this->tester->setMessageSenderChannelNameMap(MessageBrokerTestMessageTransfer::class, static::CHANNEL_NAME);
        $this->tester->setChannelNameSenderClientMap(static::CHANNEL_NAME, 'sns');
        $this->tester->setSnsSenderClientConfiguration();
        $this->tester->mockSuccessfulSnsClientSendResponse();

        // Act
        $awsMessageSenderPlugin = new AwsSnsMessageSenderPlugin();
        $awsMessageSenderPlugin->setFacade($this->tester->getFacade());
        $envelope = $awsMessageSenderPlugin->send($envelope);

        // Assert
        /** @var \Symfony\Component\Messenger\Stamp\SentStamp $sentStamp */
        $sentStamp = $envelope->last(SentStamp::class);

        $this->assertNotNull($sentStamp, sprintf('Expected to have a "%s" but it is not given.', SentStamp::class));
        $senderAlias = $sentStamp->getSenderAlias();
        $this->assertSame('sns', $senderAlias, sprintf('Expected to have the "%s" client used but "%s" was used.', $senderAlias, $senderAlias));
    }

    /**
     * Exception will be thrown because the SnsClient configuration is empty.
     *
     * @return void
     */
    public function testSendWithSnsSenderThrowsExceptionWhenPublishThrowsAnException(): void
    {
        // Arrange
        $messageBrokerTestMessageTransfer = new MessageBrokerTestMessageTransfer();
        $messageBrokerTestMessageTransfer->setKey('value');

        $envelope = Envelope::wrap($messageBrokerTestMessageTransfer);

        $this->tester->setMessageSenderChannelNameMap(MessageBrokerTestMessageTransfer::class, static::CHANNEL_NAME);
        $this->tester->setChannelNameSenderClientMap(static::CHANNEL_NAME, 'sns');

        // Expect
        $this->expectException(TransportException::class);

        // Act
        $awsMessageSenderPlugin = new AwsSnsMessageSenderPlugin();
        $awsMessageSenderPlugin->setFacade($this->tester->getFacade());
        $awsMessageSenderPlugin->send($envelope);
    }

    /**
     * Exception will be thrown when the publish method call returns a response without a messageId.
     *
     * @return void
     */
    public function testSendWithSnsSenderThrowsExceptionWhenPublishResponseIsInvalid(): void
    {
        // Arrange
        $messageBrokerTestMessageTransfer = new MessageBrokerTestMessageTransfer();
        $messageBrokerTestMessageTransfer->setKey('value');

        $envelope = Envelope::wrap($messageBrokerTestMessageTransfer);

        $this->tester->setMessageSenderChannelNameMap(MessageBrokerTestMessageTransfer::class, static::CHANNEL_NAME);
        $this->tester->setChannelNameSenderClientMap(static::CHANNEL_NAME, 'sns');
        $this->tester->setSnsSenderClientConfiguration();

        // This will mock a response that will not have a messageId.
        $this->tester->mockFailingSnsClientSendResponse();

        // Expect
        $this->expectException(TransportException::class);

        // Act
        $awsMessageSenderPlugin = new AwsSnsMessageSenderPlugin();
        $awsMessageSenderPlugin->setFacade($this->tester->getFacade());
        $awsMessageSenderPlugin->send($envelope);
    }
}
