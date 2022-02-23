<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\MessageBrokerAws\Business\Config;

use Codeception\Test\Unit;
use Exception;
use Generated\Shared\Transfer\StoreTransfer;
use Spryker\Zed\MessageBrokerAws\Business\Config\JsonToArrayConfigFormatter;
use Spryker\Zed\MessageBrokerAws\Dependency\MessageBrokerAwsToStoreFacadeInterface;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group MessageBrokerAws
 * @group Business
 * @group Config
 * @group JsonToArrayConfigFormatterTest
 * Add your own group annotations below this line
 */
class JsonToArrayConfigFormatterTest extends Unit
{
    /**
     * @var string
     */
    protected const STORE_NAME = 'foo';

    /**
     * @return void
     */
    public function testFormatFormatsJsonStringToArrayWhenNoDefaultKeyAndNoStoreKey(): void
    {
        // Arrange
        $storeFacade = $this->getMockStoreFacadeDefaultStore();
        $jsonToArrayConfigFormatter = new JsonToArrayConfigFormatter($storeFacade);

        // Act
        $formatted = $jsonToArrayConfigFormatter->format('{"foo": "bar"}');

        // Assert
        $this->assertSame(['foo' => 'bar'], $formatted);
    }

    /**
     * @return void
     */
    public function testFormatFormatsJsonStringToArrayWhenDefaultKeyExistsAndStoreKeyExists(): void
    {
        // Arrange
        $jsonToArrayConfigFormatter = new JsonToArrayConfigFormatter($this->getMockStoreFacadeDefaultStore());

        // Act
        $formatted = $jsonToArrayConfigFormatter->format('{"default": {"foo": "bar"}, "' . static::STORE_NAME . '": {"foo": "bar_' . static::STORE_NAME . '"}}');

        // Assert
        $this->assertSame(['foo' => 'bar_DE'], $formatted);
    }

    /**
     * @return void
     */
    public function testFormatFormatsJsonStringToArrayWhenDefaultKeyExistsAndNoStoreKey(): void
    {
        // Arrange
        $jsonToArrayConfigFormatter = new JsonToArrayConfigFormatter($this->getMockStoreFacadeDefaultStore());

        // Act
        $formatted = $jsonToArrayConfigFormatter->format('{"default": {"foo": "bar"}}');

        // Assert
        $this->assertSame(['foo' => 'bar'], $formatted);
    }

    /**
     * @return void
     */
    public function testFormatFormatsJsonStringToArrayWhenNoDefaultKeyAndStoreKeyExists(): void
    {
        // Arrange
        $jsonToArrayConfigFormatter = new JsonToArrayConfigFormatter($this->getMockStoreFacadeDefaultStore());

        // Act
        $formatted = $jsonToArrayConfigFormatter->format('{"' . static::STORE_NAME . '": {"foo": "bar_' . static::STORE_NAME . '"}}');

        // Assert
        $this->assertSame(['foo' => 'bar_DE'], $formatted);
    }

    /**
     * @return void
     */
    public function testFormatThrowsExceptionWhenStringCanNotBeConvertedToArray(): void
    {
        // Arrange
        $jsonToArrayConfigFormatter = new JsonToArrayConfigFormatter($this->getMockStoreFacadeDefaultStore());

        // Expect
        $this->expectException(Exception::class);

        // Act
        $jsonToArrayConfigFormatter->format('"foo": "bar"');
    }

    /**
     * @return \Spryker\Zed\MessageBrokerAws\Dependency\MessageBrokerAwsToStoreFacadeInterface
     */
    protected function getMockStoreFacadeDefaultStore(): MessageBrokerAwsToStoreFacadeInterface
    {
        $storeFacadeMock = $this->getMockBuilder(MessageBrokerAwsToStoreFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $storeFacadeMock->method('getCurrentStore')->willReturn(
            (new StoreTransfer())->setName(static::STORE_NAME),
        );

        return $storeFacadeMock;
    }
}
