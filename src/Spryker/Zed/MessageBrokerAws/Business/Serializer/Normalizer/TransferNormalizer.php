<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MessageBrokerAws\Business\Serializer\Normalizer;

use Spryker\Shared\Kernel\Transfer\TransferInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class TransferNormalizer implements NormalizerInterface
{
    /**
     * @param \Spryker\Shared\Kernel\Transfer\TransferInterface|mixed $object
     * @param string|null $format
     * @param array<string, mixed> $context
     *
     * @return array<string, mixed>
     */
    public function normalize($object, ?string $format = null, array $context = []): array
    {
        return $object->modifiedToArray();
    }

    /**
     * @param mixed $data
     * @param string|null $format
     *
     * @return bool
     */
    public function supportsNormalization($data, ?string $format = null): bool
    {
        return is_object($data) && $data instanceof TransferInterface;
    }
}
