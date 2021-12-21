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
     * @param TransferInterface $object
     * @param string|null $format
     * @param array $context
     *
     * @return array
     */
    public function normalize($object, ?string $format = null, array $context = [])
    {
        return $object->modifiedToArray();
    }

    /**
     * @param mixed $data
     * @param string|null $format
     *
     * @return bool
     */
    public function supportsNormalization($data, ?string $format = null)
    {
        return is_object($data) && $data instanceof TransferInterface;
    }
}
