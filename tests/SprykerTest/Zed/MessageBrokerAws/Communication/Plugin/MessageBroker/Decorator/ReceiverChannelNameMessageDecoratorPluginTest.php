<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\MessageBrokerAws\Communication\Plugin\MessageBroker\Decorator;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\MessageBrokerTestMessageTransfer;
use Spryker\Zed\MessageBrokerAws\Business\Sender\Stamp\ReceiverChannelNameStamp;
use Spryker\Zed\MessageBrokerAws\Communication\Plugin\MessageBroker\Decorator\ReceiverChannelNameMessageDecoratorPlugin;
use Symfony\Component\Messenger\Envelope;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group MessageBrokerAws
 * @group Communication
 * @group Plugin
 * @group MessageBroker
 * @group Decorator
 * @group ReceiverChannelNameMessageDecoratorPluginTest
 * Add your own group annotations below this line
 */
class ReceiverChannelNameMessageDecoratorPluginTest extends Unit
{
    /**
     * @var \SprykerTest\Zed\MessageBrokerAws\MessageBrokerAwsCommunicationTester
     */
    protected $tester;

    /**
     * @return void
     */
    public function testDecorateMessageAddsReceiverChannelNameStampWithChannelForTheGivenMessage(): void
    {
        // Arrange
        $messageBrokerTestMessageTransfer = new MessageBrokerTestMessageTransfer();
        $this->tester->setMessageToReceiverSenderChannelNameMap(MessageBrokerTestMessageTransfer::class, 'channel');

        $envelope = Envelope::wrap($messageBrokerTestMessageTransfer);
        $channelNameMessageDecoratorPlugin = new ReceiverChannelNameMessageDecoratorPlugin();

        // Act
        $envelope = $channelNameMessageDecoratorPlugin->decorateMessage($envelope);

        // Assert
        $this->tester->assertMessageHasStamp($envelope, ReceiverChannelNameStamp::class);
    }
}
