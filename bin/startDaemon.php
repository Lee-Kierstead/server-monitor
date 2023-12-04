<?php

/**
 * Daemon Execution Script
 *
 * This script initializes the Daemon class, sets up configuration constants,
 * and runs the daemon process based on command-line options.
 */

require_once dirname(__DIR__) . '/config/constants.php';
require_once BASE_DIR . '/vendor/autoload.php';

use ServerMonitor\Daemon;

// Get command-line options
$options = getopt("i:n:w:");

// Default values for options
$defaultInterval = 60;      // Default interval is 60 seconds
$defaultInstanceCount = 1;  // Default instance count is 1
$defaultMaxCpuLoad = 100;   // Default max CPU load is 100%

// Extract values from options or use default values
$interval = isset($options['i']) ? (int)$options['i'] : $defaultInterval;
$instanceCount = isset($options['n']) ? (int)$options['n'] : $defaultInstanceCount;
$maxCpuLoad = isset($options['w']) ? (int)$options['w'] : $defaultMaxCpuLoad;

// Create and run the Daemon with specified options
$daemon = new Daemon($interval, $instanceCount, $maxCpuLoad);
$daemon->run();
