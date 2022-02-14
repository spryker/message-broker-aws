<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MessageBrokerAws\Business\Queue;

use Aws\Sns\SnsClient;
use Aws\Sqs\SqsClient;
use Spryker\Zed\MessageBrokerAws\MessageBrokerAwsConfig;

class AwsSqsQueuesSubscriber implements AwsSqsQueuesSubscriberInterface
{
    protected const SQS_PROTOCOL = 'sqs';

    /**
     * @var \Aws\Sns\SnsClient
     */
    protected $snsClient;

    /**
     * @var \Spryker\Zed\MessageBrokerAws\MessageBrokerAwsConfig
     */
    protected $messageBrokerAwsConfig;

    /**
     * @param \Aws\Sqs\SqsClient $snsClient
     * @param \Spryker\Zed\MessageBrokerAws\MessageBrokerAwsConfig $messageBrokerAwsConfig
     */
    public function __construct(
        SnsClient $snsClient,
        MessageBrokerAwsConfig $messageBrokerAwsConfig
    ) {
        $this->snsClient = $snsClient;
        $this->messageBrokerAwsConfig = $messageBrokerAwsConfig;
    }

    /**
     * @return void
     */
    public function subscribeQueues(): void
    {
        foreach ($this->messageBrokerAwsConfig->getSqsToSnsSubscriptions() as $sqsSubscription) {
            $sqsSubscription['Protocol'] = static::SQS_PROTOCOL;
            $sqsSubscription['ReturnSubscriptionArn'] = false;

            // Check FilterPolicy attribute
            // Check that messages are filtered based on this policy
            if (isset($sqsSubscription['FilterPolicy'])) {
                $sqsSubscription['Attributes']['FilterPolicy'] = $sqsSubscription['FilterPolicy'];
                unset($sqsSubscription['FilterPolicy']);
            }

            $this->snsClient->subscribe($sqsSubscription);
        }
    }
}
