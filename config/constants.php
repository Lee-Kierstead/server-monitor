<?php

/**
 * Configuration Script
 *
 * This script sets constants and configurations for the Server Monitor application.
 */

 namespace ServerMonitor;

// Set the namespace constant
define('NSPACE', __NAMESPACE__);

// Set the base directory constant
define('BASE_DIR', dirname(__DIR__));

// Set the configuration directory constant
define('CONFIG_DIR', BASE_DIR . '/config');

// Set the log directory constant
define('LOG_DIR', BASE_DIR . '/logs');

// Set the server table constant
define('SERVER_TBL', 'server');

// Set the server status table constant
define('SERVER_STATUS_TBL', 'server_status');

// Set the environment operating system constant
define('ENV_OS', substr(PHP_OS, 0, 3));
