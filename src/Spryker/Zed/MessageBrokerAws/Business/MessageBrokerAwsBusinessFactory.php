<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MessageBrokerAws\Business;

use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Spryker\Zed\MessageBrokerAws\Business\Config\ConfigFormatterInterface;
use Spryker\Zed\MessageBrokerAws\Business\Config\StringToArrayConfigFormatter;
use Spryker\Zed\MessageBrokerAws\Business\Receiver\Client\Locator\ReceiverClientLocator;
use Spryker\Zed\MessageBrokerAws\Business\Receiver\Client\Locator\ReceiverClientLocatorInterface;
use Spryker\Zed\MessageBrokerAws\Business\Receiver\Client\ReceiverClientInterface;
use Spryker\Zed\MessageBrokerAws\Business\Receiver\Client\SqsReceiverClient;
use Spryker\Zed\MessageBrokerAws\Business\Receiver\Receiver;
use Spryker\Zed\MessageBrokerAws\Business\Receiver\ReceiverInterface;
use Spryker\Zed\MessageBrokerAws\Business\Sender\Client\Locator\SenderClientLocator;
use Spryker\Zed\MessageBrokerAws\Business\Sender\Client\Locator\SenderClientLocatorInterface;
use Spryker\Zed\MessageBrokerAws\Business\Sender\Client\SenderClientInterface;
use Spryker\Zed\MessageBrokerAws\Business\Sender\Client\SnsSenderClient;
use Spryker\Zed\MessageBrokerAws\Business\Sender\Sender;
use Spryker\Zed\MessageBrokerAws\Business\Sender\SenderInterface;
use Symfony\Component\Messenger\Transport\Serialization\Serializer;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

/**
 * @method \Spryker\Zed\MessageBrokerAws\MessageBrokerAwsConfig getConfig()
 */
class MessageBrokerAwsBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \Spryker\Zed\MessageBrokerAws\Business\Sender\SenderInterface
     */
    public function createSender(): SenderInterface
    {
        return new Sender(
            $this->getConfig(),
            $this->createSenderClientLocator(),
            $this->createConfigFormatter(),
        );
    }

    public function createSenderClientLocator(): SenderClientLocatorInterface
    {
        return new SenderClientLocator(
            $this->getConfig(),
            $this->getSenderClients(),
            $this->createConfigFormatter(),
        );
    }

    /**
     * @return array<string, \Spryker\Zed\MessageBrokerAws\Business\Sender\Client\SenderClientInterface>
     */
    public function getSenderClients(): array
    {
        return [
            'sns' => $this->createSnsSenderClient(),
        ];
    }

    /**
     * @return \Spryker\Zed\MessageBrokerAws\Business\Sender\Client\SenderClientInterface
     */
    public function createSnsSenderClient(): SenderClientInterface
    {
        return new SnsSenderClient(
            $this->getConfig(),
            $this->createSerializer(),
            $this->createConfigFormatter(),
        );
    }

    /**
     * @return \Spryker\Zed\MessageBrokerAws\Business\Receiver\ReceiverInterface
     */
    public function createReceiver(): ReceiverInterface
    {
        return new Receiver(
            $this->getConfig(),
            $this->createReceiverClientLocator(),
        );
    }

    public function createReceiverClientLocator(): ReceiverClientLocatorInterface
    {
        return new ReceiverClientLocator(
            $this->getConfig(),
            $this->getReceiverClients(),
            $this->createConfigFormatter(),
        );
    }

    /**
     * @return array<string, \Spryker\Zed\MessageBrokerAws\Business\Receiver\Client\ReceiverClientInterface>
     */
    public function getReceiverClients(): array
    {
        return [
            'sqs' => $this->createSqsReceiverClient(),
        ];
    }

    /**
     * @return \Spryker\Zed\MessageBrokerAws\Business\Receiver\Client\ReceiverClientInterface
     */
    public function createSqsReceiverClient(): ReceiverClientInterface
    {
        return new SqsReceiverClient(
            $this->getConfig(),
            $this->createSerializer(),
            $this->createConfigFormatter(),
        );
    }

    /**
     * @return \Symfony\Component\Messenger\Transport\Serialization\SerializerInterface
     */
    public function createSerializer(): SerializerInterface
    {
        return new Serializer(); // TODO this one does not work Transfer objects

        // return new PhpSerializer(); // TODO this one does not create the required header
    }

    /**
     * @return \Spryker\Zed\MessageBrokerAws\Business\Config\ConfigFormatterInterface
     */
    public function createConfigFormatter(): ConfigFormatterInterface
    {
        return new StringToArrayConfigFormatter();
    }
}
