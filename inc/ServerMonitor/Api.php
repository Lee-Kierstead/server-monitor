<?php

namespace ServerMonitor;

/**
 * Class Api
 *
 * This class handles communication with servers to fetch and parse status data.
 */
class Api
{
    /**
     * @var Api|null An instance of the Api class (singleton pattern).
     */
    private static $instance;

    /**
     * @var Database The Database instance for interacting with the database.
     */
    private $db;

    /**
     * @var array An array of server parameters fetched from the database to make HTTP requests.
     */
    private $servers;

    /**
     * @var array An array to store responses from server status requests.
     */
    private $responses = [];

    /**
     * @var bool The overall status of the server statistics update process.
     */
    private $status;

    /**
     * Api constructor.
     *
     * Initializes the Api class by calling the init method.
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     * Initializes the Api class by setting up the database and fetching server parameters.
     *
     * @return void
     */
    protected function init()
    {
        $this->db = Database::getInstance(); // instantiate the db object.
        $this->servers = $this->db->getServers(); // get server params from db to make HTTP requests.
        $this->fetchStatus(); // query the webservers for status data.
        $this->setStats(); // insert the parsed responses into the db.
        $this->finalNotice(); // if we've made it this far all is good. Close PDO and log success.
    }

    /**
     * Sets statistics by inserting parsed responses into the database.
     *
     * @return void
     */
    private function setStats()
    {
        foreach ($this->responses as $response) {
            $status = $this->db->insertStats($response);
            $this->status = ($status === true);
        }
    }

    /**
     * Fetches server status by making HTTP requests to each server in the configuration.
     *
     * @return void
     */
    private function fetchStatus()
    {
        foreach ($this->servers as $server) {
            $url = self::buildURL($server);

            try {
                // Open separate process for each request.
                // Add SSL cert bypass for HTTPS requests. !!Development/Demo purposes only!!

                $command = 'php -r "$url = \'' . addslashes($url) . '\'; $context = stream_context_create([\'ssl\' => [\'verify_peer\' => false, \'verify_peer_name\' => false,],]); echo \'PID : \' .getmypid() . \':\' . file_get_contents($url, false, $context);"'; // phpcs:ignore Generic.Files.LineLength.TooLong
                $process = proc_open($command, [
                    1 => ['pipe', 'w'], // Capture stdout
                ], $pipes);

                // Check if the process was successfully created
                if (is_resource($process)) {
                    // Read the response from the process
                    $response = stream_get_contents($pipes[1]);

                    fclose($pipes[1]);

                    // Wait for the process to finish
                    $exitCode = proc_close($process);

                    if ($exitCode === 0) {
                        // Process completed successfully

                        // Don't include garbage (filter out warnings, etc., indicated by the position of the echoed PID)
                        if (substr($response, 0, 3) !== "PID") {
                            unset($response);
                        }

                        if ($response || $response !== null) {
                            $parsed_response = self::parseResponse($response);
                            // Append server id to $parsed_response object for DB insertion
                            $parsed_response->ServerID = $server->server_id;
                            $this->responses[$server->name]  = $parsed_response;
                        }
                    } else {
                        // Process exited with a warning
                        Log::logMessage('Warning: Unable to fetch server status for ' . $server->name . '.', false);
                    }
                } else {
                    Log::logMessage('Warning: Unable to create process for ' . $server->name . '.', false);
                }
            } catch (\Exception $e) {
                // Log the error to a file or any other logging mechanism
                Log::logException($e, false);
                $this->db->__destruct();
            }
        }
    }

    /**
     * Parses the server response into an object containing relevant statistics.
     *
     * @param string $response The raw response from the server.
     *
     * @return object The parsed response as an object.
     */
    public static function parseResponse($response)
    {
        // Split lines and filter out empty lines
        $responseLines = array_filter(explode("\n", $response));

        // Transform lines into key-value pairs
        $responseArray = array_map(function ($line) {
            list($key, $value) = explode(':', $line, 2);
            return [trim($key) => trim($value)];
        }, $responseLines);

        // Merge the array of key-value pairs into a single associative array
        $responseArray = call_user_func_array('array_merge', $responseArray);

        $preppedArray = [];
        $searchKey = ['Total Accesses', 'Total kBytes', 'BusyWorkers'];

        foreach ($responseArray as $key => $value) {
            if (in_array($key, $searchKey)) {
                $preppedArray[$key] = $value;
            }
        }

        return (object) $preppedArray;
    }

    /**
     * Builds the URL for making HTTP requests to the server's status endpoint.
     *
     * @param object $server An object containing server configuration parameters.
     *
     * @return string The constructed URL.
     */
    private static function buildURL($server)
    {
        // TODO: Add support for **Lighttpd** and **NGINX** web servers as well
        $url = ($server->port == 443) ? 'https://' . $server->address . '/server-status?auto' : 'http://' . $server->address . '/server-status?auto'; // phpcs:ignore Generic.Files.LineLength.TooLong
        return $url;
    }

    /**
     * Gets a singleton instance of the Api class.
     *
     * @return Api The singleton instance.
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Outputs a final notice based on the success or failure of the server statistics update process.
     *
     * @return void
     */
    private function finalNotice()
    {
        try {
            if ($this->status === true) {
                Log::logMessage('Success: Server statistics have been updated.');
            } else {
                throw new \Exception('Server statistics failed to update.');
            }
        } catch (\Exception $e) {
            Log::logException($e, true);
            $this->db->__destruct();
        }
    }
}
// end class Api
