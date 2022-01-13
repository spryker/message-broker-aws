<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\MessageBrokerAws\Business\Config;

use Codeception\Test\Unit;
use Exception;
use Spryker\Zed\MessageBrokerAws\Business\Config\JsonToArrayConfigFormatter;

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
     * @return void
     */
    public function testFormatFormatsJsonStringToArray(): void
    {
        // Arrange
        $jsonToArrayConfigFormatter = new JsonToArrayConfigFormatter();

        // Act
        $formatted = $jsonToArrayConfigFormatter->format('{"foo": "bar"}');

        // Assert
        $this->assertSame(['foo' => 'bar'], $formatted);
    }

    /**
     * @return void
     */
    public function testFormatThrowsExceptionWhenStringCanNotBeConvertedToArray(): void
    {
        // Arrange
        $jsonToArrayConfigFormatter = new JsonToArrayConfigFormatter();

        // Expect
        $this->expectException(Exception::class);

        // Act
        $jsonToArrayConfigFormatter->format('"foo": "bar"');
    }
}
