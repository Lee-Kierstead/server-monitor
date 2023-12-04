<?php

namespace ServerMonitor;

/**
 * Class Database
 *
 * This class handles database operations and interactions.
 */
class Database
{
    /**
     * @var \PDO|null The PDO instance for database connection.
     */
    private $pdo;

    /**
     * @var Database|null An instance of the Database class (singleton pattern).
     */
    private static $instance;

    /**
     * Database constructor.
     *
     * Initializes the Database class by calling the init method.
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     * Destructor method to explicitly close the database connection.
     */
    public function __destruct()
    {
        if ($this->pdo instanceof \PDO) {
            $this->pdo = null;
        }
    }

    /**
     * Initializes the Database class by setting up the PDO instance.
     */
    protected function init()
    {
        $this->getPDO();
    }

    /**
     * Gets a singleton instance of the Database class.
     *
     * @return Database The singleton instance.
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Retrieves or creates a PDO instance for database connection.
     */
    private function getPDO()
    {
        $config = $this->parseConfigFile();
        try {
            $this->pdo = new \PDO($config->dsn, $config->username, $config->password);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            Log::logException($e, true);
        }
    }

    /**
     * Parses the configuration file to obtain database connection details.
     *
     * @param string $configFile The path to the configuration file.
     *
     * @return object An object containing database connection details.
     */
    private function parseConfigFile($configFile = CONFIG_DIR . '/config.ini')
    {
        $config = parse_ini_file($configFile, true);

        if ($config === false) {
            Log::logError(E_ERROR, "Cannot read configuration file.", __FILE__, __LINE__);
        }

        if (!isset($config['database']['dsn']) || !isset($config['database']['username']) || !isset($config['database']['password'])) {
            Log::logError(E_ERROR, "Incomplete configuration. Make sure 'dsn', 'username', and 'password' are set properly.", __FILE__, __LINE__);
        }

        return (object)[
            'dsn' => $config['database']['dsn'],
            'username' => $config['database']['username'],
            'password' => $config['database']['password'],
        ];
    }

    /**
     * Inserts server statistics into the database.
     *
     * @param object $response An object containing server statistics.
     *
     * @return bool Returns true on success, false otherwise.
     */
    public function insertStats($response)
    {
        try {
            $currentUnixTime = time();

            $stmt = $this->pdo->prepare("INSERT INTO " . SERVER_STATUS_TBL . " (time, server_id, total_requests, total_kbytes, active_connections) VALUES (:time, :server_id, :total_requests, :total_kbytes, :active_connections)");
            $stmt->bindParam(':time', $currentUnixTime, \PDO::PARAM_INT);
            $stmt->bindParam(':server_id', $response->ServerID, \PDO::PARAM_INT);
            $stmt->bindParam(':total_requests', $response->{'Total Accesses'}, \PDO::PARAM_INT);
            $stmt->bindParam(':total_kbytes', $response->{'Total kBytes'}, \PDO::PARAM_INT);
            $stmt->bindParam(':active_connections', $response->BusyWorkers, \PDO::PARAM_INT);

            // Execute the query
            $stmt->execute();

            return true; // Return success if no exceptions are thrown
        } catch (\PDOException $e) {
            Log::logException($e, true);
        }

        return false;
    }

    /**
     * Retrieves server information from the database.
     *
     * @return array|false An array of server information or false on failure.
     */
    public function getServers()
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM " . SERVER_TBL);
            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_OBJ);
        } catch (\PDOException $e) {
            Log::logException($e, true);
        }

        return $result;
    }
}
// End Class Database
