<?php

namespace ServerMonitor\Tests;

use PHPUnit\Framework\TestCase;
use ServerMonitor\Log;

require_once dirname(dirname(__DIR__)) . '/config/constants.php';

class LogTest extends TestCase
{
    public function testLogMessageEchoesMessage()
    {
    // Arrange: Create a Log instance
        $log = new Log();

    // Act: Call the logMessage method
        $log->logMessage('Test message');

    // Assert: Check if the message was echoed
        $this->expectOutputString('Test message' . PHP_EOL);
    }
}
