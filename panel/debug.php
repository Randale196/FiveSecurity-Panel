<?php

require_once __DIR__ . '/config.php';

function initializeDebugMode() {
    global $debug_config;
    
    if ($debug_config['debug_mode']) {
        ini_set('display_errors', $debug_config['display_errors']);
        ini_set('display_startup_errors', $debug_config['display_errors']);
        error_reporting($debug_config['error_reporting']);
        ini_set('log_errors', $debug_config['log_errors']);
        ini_set('error_log', $debug_config['error_log']);
    }
}

$logsDir = __DIR__ . '/logs';
if (!file_exists($logsDir)) {
    mkdir($logsDir, 0755, true);
}

initializeDebugMode(); 