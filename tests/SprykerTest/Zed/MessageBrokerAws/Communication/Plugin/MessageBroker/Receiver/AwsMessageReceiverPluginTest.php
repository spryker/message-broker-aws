<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\MessageBrokerAws\Communication\Plugin\MessageBroker\Receiver;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\MessageBrokerTestMessageTransfer;
use Spryker\Zed\MessageBrokerAws\Communication\Plugin\MessageBroker\Receiver\AwsSqsMessageReceiverPlugin;
use SprykerTest\Zed\MessageBrokerAws\MessageBrokerAwsCommunicationTester;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group MessageBrokerAws
 * @group Communication
 * @group Plugin
 * @group Receiver
 * @group AwsMessageReceiverPluginTest
 * Add your own group annotations below this line
 */
class AwsMessageReceiverPluginTest extends Unit
{
    public const CHANNEL_NAME = 'channel';
    /**
     * @var \SprykerTest\Zed\MessageBrokerAws\MessageBrokerAwsCommunicationTester
     */
    protected MessageBrokerAwsCommunicationTester $tester;

    /**
     * @return void
     */
//    public function testGetReturnsEmptyIterableWhenNoMessageFound(): void
//    {
//        $this->tester->setChannelNameReceiverClientMap(static::CHANNEL_NAME, 'sqs');
//        $this->tester->setSqsReceiverClientConfiguration();
//
//        $awsMessageReceiverPlugin = new AwsMessageReceiverPlugin();
//        $awsMessageReceiverPlugin->setFacade($this->tester->getFacade());
//
//        /** @var \Generator $result */
//        $result = $awsMessageReceiverPlugin->getFromQueues([static::CHANNEL_NAME]);
//
//        $this->assertFalse($result->valid());
//    }

    /**
     * @return void
     */
    public function testGetReturnsMessageWhenMessageExist(): void
    {
        $this->tester->haveMessage();

        $this->tester->setChannelNameReceiverClientMap(static::CHANNEL_NAME, 'sqs');
        $this->tester->setSqsReceiverClientConfiguration();

        $awsMessageReceiverPlugin = new AwsSqsMessageReceiverPlugin();
        $awsMessageReceiverPlugin->setFacade($this->tester->getFacade());

        /** @var \Generator $result */
        $result = $awsMessageReceiverPlugin->getFromQueues([static::CHANNEL_NAME]);

        $currentMessage = $result->current();
        $this->assertInstanceOf(MessageBrokerTestMessageTransfer::class, $currentMessage->getMessage());

        $awsMessageReceiverPlugin->ack($currentMessage);
    }

    /**
     * @return void
     */
    public function testRejectReceivedMessage(): void
    {
        $this->tester->haveMessage();

        $this->tester->setChannelNameReceiverClientMap(static::CHANNEL_NAME, 'sqs');
        $this->tester->setSqsReceiverClientConfiguration();

        $awsMessageReceiverPlugin = new AwsSqsMessageReceiverPlugin();
        $awsMessageReceiverPlugin->setFacade($this->tester->getFacade());

        /** @var \Generator $result */
        $result = $awsMessageReceiverPlugin->getFromQueues([static::CHANNEL_NAME]);

        $currentMessage = $result->current();
        $this->assertInstanceOf(MessageBrokerTestMessageTransfer::class, $currentMessage->getMessage());

        $awsMessageReceiverPlugin->reject($currentMessage);
    }

    /**
     * @return void
     */
    public function testGetClientNameReturnNameOfTheSupportedClient(): void
    {
        // Arrange
        $awsMessageReceiverPlugin = new AwsSqsMessageReceiverPlugin();

        // Act
        $clientName = $awsMessageReceiverPlugin->getClientName();

        // Assert
        $this->assertSame('sqs', $clientName, sprintf('Expected to get "sqs" as client name but got "%s"', $clientName));
    }
}
