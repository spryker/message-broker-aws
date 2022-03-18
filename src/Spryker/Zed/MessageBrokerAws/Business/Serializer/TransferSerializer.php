<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MessageBrokerAws\Business\Serializer;

use Generated\Shared\Transfer\MessageAttributesTransfer;
use Generated\Shared\Transfer\PublisherTransfer;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\MessageDecodingFailedException;
use Symfony\Component\Messenger\Stamp\NonSendableStampInterface;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface as SymfonySerializerInterface;

class TransferSerializer implements SerializerInterface
{
    /**
     * @var \Symfony\Component\Serializer\SerializerInterface
     */
    protected SymfonySerializerInterface $serializer;

    /**
     * @var string
     */
    protected string $format = 'json';

    /**
     * @var array<bool>
     */
    protected array $context = [];

    /**
     * @param \Symfony\Component\Serializer\SerializerInterface $serializer
     */
    public function __construct(SymfonySerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param array<string, mixed> $encodedEnvelope
     *
     * @throws \Symfony\Component\Messenger\Exception\MessageDecodingFailedException
     *
     * @return \Symfony\Component\Messenger\Envelope
     */
    public function decode(array $encodedEnvelope): Envelope
    {
        if (empty($encodedEnvelope['body'])) {
            throw new MessageDecodingFailedException('Encoded envelope should have a "body".');
        }

        if (empty($encodedEnvelope['headers'])) {
            throw new MessageDecodingFailedException('Encoded envelope should have some "headers".');
        }

        if (empty($encodedEnvelope['headers']['transferName'])) {
            throw new MessageDecodingFailedException('Encoded envelope does not have a "transferName" header. The "transferName" is referring to a Transfer class that is used to unserialize the message data.');
        }

        if (empty($encodedEnvelope['headers']['publisher'])) {
            throw new MessageDecodingFailedException('Encoded envelope does not have a "publisher" header. The "publisher" is referring to a Transfer class that is used to unserialize the message data.');
        }

        $messageAttributesTransfer = new MessageAttributesTransfer();
        $messageAttributesTransfer->setPublisher(
            (new PublisherTransfer())
                ->fromArray(json_decode($encodedEnvelope['headers']['publisher'], true), true),
        );
        unset($encodedEnvelope['headers']['publisher']);

        $messageAttributesTransfer->fromArray($encodedEnvelope['headers'], true);

        // TODO check with security manager if this could be an issue.
        $messageTransferClassName = sprintf('\\Generated\\Shared\\Transfer\\%sTransfer', $messageAttributesTransfer->getTransferNameOrFail());

        if (!class_exists($messageTransferClassName)) {
            throw new MessageDecodingFailedException(sprintf('Could not find the "%s" transfer object to unserialize the data.', $messageTransferClassName));
        }

        try {
            $messageTransfer = $this->serializer->deserialize($encodedEnvelope['body'], $messageTransferClassName, $this->format, $this->context);
            $messageTransfer->setMessageAttributes($messageAttributesTransfer);
        } catch (ExceptionInterface $e) {
            throw new MessageDecodingFailedException('Could not decode message: ' . $e->getMessage(), $e->getCode(), $e);
        }

        return new Envelope($messageTransfer);
    }

    /**
     * @param \Symfony\Component\Messenger\Envelope $envelope
     *
     * @throws \Symfony\Component\Messenger\Exception\MessageDecodingFailedException
     *
     * @return array<string, mixed>
     */
    public function encode(Envelope $envelope): array
    {
        $envelope = $envelope->withoutStampsOfType(NonSendableStampInterface::class);

        $messageTransfer = $envelope->getMessage();

        if (!($messageTransfer instanceof AbstractTransfer)) {
            throw new MessageDecodingFailedException(sprintf('Could not decode message, expected type of "%s" but got "%s".', AbstractTransfer::class, gettype($messageTransfer)));
        }

        if (!method_exists($messageTransfer, 'getMessageAttributes')) {
            throw new MessageDecodingFailedException(sprintf('Could not decode message, expected to have a method "getMessageAttributes()" but it was not found in "%s".', get_class($messageTransfer)));
        }

        /** @var \Generated\Shared\Transfer\MessageAttributesTransfer $messageAttributesTransfer */
        $messageAttributesTransfer = $messageTransfer->getMessageAttributes();

        if (!($messageAttributesTransfer instanceof MessageAttributesTransfer)) {
            throw new MessageDecodingFailedException(sprintf('Could not decode message, expected to have a Transfer object "%s" inside your "%s" message transfer but it is empty.', MessageAttributesTransfer::class, get_class($messageTransfer)));
        }
        $messageAttributesTransfer->getTransferNameOrFail();

        $messageData = $messageTransfer->modifiedToArray(true, true);

        unset($messageData['messageAttributes']);

        $headers = $messageAttributesTransfer->modifiedToArray(true, true);
        $headers += ['Content-Type' => 'application/json'];

        return [
            'body' => $this->serializer->serialize($messageData, $this->format, $this->context),
            'bodyRaw' => $messageData,
            'headers' => $headers,
        ];
    }
}
