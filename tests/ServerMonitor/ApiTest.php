<?php

namespace ServerMonitor\Tests;

use PHPUnit\Framework\TestCase;
use ServerMonitor\Api;

require_once dirname(dirname(__DIR__)) . '/config/constants.php';

class ApiTest extends TestCase
{
    /**
     * Test the getInstance method of the Api class.
     */
    public function testGetInstance()
    {
        // Call the getInstance method
        $apiInstance = Api::getInstance();

        // Assert that the returned instance is an object of the Api class
        $this->assertInstanceOf(Api::class, $apiInstance);
    }
}
