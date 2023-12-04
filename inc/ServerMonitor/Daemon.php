<?php

namespace ServerMonitor;

/**
 * Class Daemon
 *
 * Description: Daemon class for monitoring server performance.
 */
class Daemon
{
    /**
     * @var int The current number of instances of the daemon.
     */
    private static $currentInstances = 0;

    /**
     * @var array An array to store CPU loads for each instance of the daemon.
     */
    private static $cpuLoads = [];

    /**
     * @var int The interval, in seconds, at which the daemon runs.
     */
    private $interval;

    /**
     * @var int The maximum allowed CPU load (percentage).
     */
    private $maxCpuLoad;

    /**
     * @var bool Debug mode flag. Set to true to output daemon log.
     */
    private $debug = false;

    /**
     * @var int The total number of instances the daemon should run.
     */
    private $instanceCount;

    /**
     * Daemon constructor.
     *
     * @param int $interval       The interval, in seconds, at which the daemon runs.
     * @param int $instanceCount  The total number of instances the daemon should run.
     * @param int $maxCpuLoad     The maximum allowed CPU load (percentage).
     */
    public function __construct($interval = 5, $instanceCount = 1, $maxCpuLoad = 100)
    {
        $this->interval = $interval;
        $this->instanceCount = $instanceCount;
        $this->maxCpuLoad = $maxCpuLoad;
    }

    /**
     * Run the daemon.
     */
    public function run()
    {
        while (true) {
            self::$currentInstances++;
            $api = new Api();
            $cpuLoad = $this->getCPULoad();
            var_dump($cpuLoad);
            self::$cpuLoads[] = $cpuLoad;

            // Calculate the aggregate CPU load
            $aggregateCpuLoad = array_sum(self::$cpuLoads) / self::$currentInstances;

            // Check if the aggregate load exceeds the maximum
            if ($aggregateCpuLoad > $this->maxCpuLoad) {
                // Log a warning if the aggregate CPU load exceeds the maximum allowed
                Log::logMessage('Warning: Aggregate CPU load exceeded the maximum allowed', false);
            }

            self::$currentInstances--;
            sleep($this->interval);
        }
    }

    /**
     * Get the CPU load.
     *
     * @return int The CPU load as a percentage.
     */
    public function getCPULoad()
    {
        if (ENV_OS === 'WIN') {
            // Windows environment
            $command = "wmic cpu get loadpercentage";
            $result = shell_exec($command);
            $matches = [];
            preg_match("/LoadPercentage\s+(\d+)/", $result, $matches);
            $cpuLoad = isset($matches[1]) ? (int)$matches[1] : 0;
        } else {
            // Non-Windows environment
            $command = 'echo $((100 - $(mpstat 1 1 | awk "/all/{print $12}")))';
            $cpuLoad = shell_exec($command);
        }

        // Return the CPU load as a percentage
        return $cpuLoad;
    }

    /**
     * Start the daemon in the background.
     *
     * @return void
     */
    public function startInBackground()
    {
        // Check if the maximum instances limit is reached
        if (self::$currentInstances < $this->instanceCount) {
            // Check OS for proper redirect of output to null
            $outputRedirect = (ENV_OS === 'WIN') ? 'NUL' : '/dev/null';

            // Run the script in the background using exec and nohup with debug option
            ($this->debug === true) ? exec("nohup php " . BASE_DIR . "/bin/startDaemon.php -i {$this->interval} -n {$this->instanceCount} -w {$this->maxCpuLoad} > ./bin/daemonOutput.log 2>&1 &") : exec("nohup php " . BASE_DIR . "/bin/startDaemon.php -i {$this->interval} -n {$this->instanceCount} -w {$this->maxCpuLoad} > $outputRedirect 2>&1 &"); // phpcs:ignore Generic.Files.LineLength.TooLong
        } else {
            Log::logMessage('Warning: Maximum instances limit reached. Cannot start a new instance.', false);
            echo 'Warning: Maximum instances limit reached. Cannot start a new instance.' . PHP_EOL;
        }
    }
}
