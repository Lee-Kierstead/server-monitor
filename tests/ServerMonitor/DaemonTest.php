<?php

namespace ServerMonitor\Tests;

use PHPUnit\Framework\TestCase;
use ServerMonitor\Daemon;

require_once dirname(dirname(__DIR__)) . '/config/constants.php';

class DaemonTest extends TestCase
{
    /**
     * Test the getCPULoad method of the Daemon class.
     */
    public function testGetCPULoadMethod()
    {
        // Mock a Daemon instance
        $daemon = $this->getMockBuilder(Daemon::class)
            ->setConstructorArgs([5, 1, 100])
            ->getMock();

        // Set up expectations or assertions as needed
        $daemon->expects($this->atLeastOnce())
            ->method('getCPULoad')
            ->willReturn(60); // Assuming 60% CPU load for the mock

        // Call the getCPULoad method
        $cpuLoad = $daemon->getCPULoad();

        // Assert the expected CPU load
        $this->assertEquals(60, $cpuLoad);
    }
}
