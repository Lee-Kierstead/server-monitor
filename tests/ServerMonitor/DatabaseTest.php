<?php

namespace ServerMonitor\Tests;

require_once dirname(dirname(__DIR__)) . '/config/constants.php';

use PHPUnit\Framework\TestCase;
use ServerMonitor\Database;

class DatabaseTest extends TestCase
{
    public function testGetServers()
    {
        // Arrange
        // Mock the PDO instance
        $pdoMock = $this->createMock(\PDO::class);
        $pdoStatementMock = $this->createMock(\PDOStatement::class);

        // Set up the expected method calls and return values
        $pdoStatementMock->expects($this->once())->method('execute')->willReturn(true);
        $pdoStatementMock->expects($this->once())->method('fetchAll')->with(\PDO::FETCH_OBJ)->willReturn(['server1', 'server2']);

        $pdoMock->expects($this->once())->method('prepare')->willReturn($pdoStatementMock);

        // Set up the Database class with the mock PDO
        $database = new Database();
        $databaseReflection = new \ReflectionClass(Database::class);
        $pdoProperty = $databaseReflection->getProperty('pdo');
        $pdoProperty->setAccessible(true);
        $pdoProperty->setValue($database, $pdoMock);

        // Act
        $result = $database->getServers();

        // Assert
        $this->assertEquals(['server1', 'server2'], $result);
    }
}
