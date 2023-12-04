<?php

namespace ServerMonitor;

/**
 * Server Monitor Bootstrap Script
 *
 * This script initializes the Server Monitor application, sets up configuration constants,
 * and starts the monitoring process based on command-line options.
 */

require_once dirname(__DIR__) . '/config/constants.php';
require_once BASE_DIR . '/vendor/autoload.php';

// Disable display errors to rely on class error reporting
ini_set('display_errors', 0);

// Initialize the Server Monitor application - aka "Lets get this party started!"
Api::getInstance();

// Get command-line options
$options = getopt("di:n:w:");

// Default values for options
$defaultInterval = 5;       // Default interval is 5 seconds
$defaultInstanceCount = 1;  // Default instance count is 1
$defaultMaxCpuLoad = 100;   // Default max CPU load is 100%

// Extract values from options or use default values
$interval = isset($options['i']) ? (int)$options['i'] : $defaultInterval;
$instanceCount = isset($options['n']) ? (int)$options['n'] : $defaultInstanceCount;
$maxCpuLoad = isset($options['w']) ? (int)$options['w'] : $defaultMaxCpuLoad;

// Start a background daemon process if '-d' option is present
if (isset($options['d'])) {
    $daemon = new Daemon($interval, $instanceCount, $maxCpuLoad);
    $daemon->startInBackground();
}
