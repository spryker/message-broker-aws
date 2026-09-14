<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

/**
 * @codingStandardsIgnoreFile
 */

namespace Spryker\Zed\MessageBrokerAws\Business\Normalizer;

use ArrayObject;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer as SymfonyObjectNormalizer;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Symfony\Component\Serializer\Normalizer\ObjectNormalizer is final since symfony/serializer 7.0,
 * so this composes an internal instance instead of extending it.
 */
class TransferNormalizer implements NormalizerInterface, DenormalizerInterface, SerializerAwareInterface
{
    protected SymfonyObjectNormalizer $objectNormalizer;

    public function __construct()
    {
        $this->objectNormalizer = new SymfonyObjectNormalizer();
    }

    /**
     * @param array<string, mixed> $context
     */
    public function normalize(mixed $data, ?string $format = null, array $context = []): array|string|int|float|bool|ArrayObject|null
    {
        return $this->objectNormalizer->normalize($data, $format, $context);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $this->objectNormalizer->supportsNormalization($data, $format, $context);
    }

    /**
     * @param array<string, mixed> $context
     *
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        if (is_subclass_of($type, AbstractTransfer::class)) {
            return (new $type())->fromArray($data, true);
        }

        return $this->objectNormalizer->denormalize($data, $type, $format, $context);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return $this->objectNormalizer->supportsDenormalization($data, $type, $format, $context);
    }

    /**
     * @return array<class-string|'*'|'object'|string, bool|null>
     */
    public function getSupportedTypes(?string $format): array
    {
        return $this->objectNormalizer->getSupportedTypes($format);
    }

    public function setSerializer(SerializerInterface $serializer): void
    {
        $this->objectNormalizer->setSerializer($serializer);
    }
}
