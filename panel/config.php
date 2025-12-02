<?php

$debug_config = [
    'debug_mode' => false,                    // Set to true to enable debug mode
    'display_errors' => false,                // Show PHP errors on screen
    'error_reporting' => E_ALL,              // Report all PHP errors
    'log_errors' => true,                    // Log errors to file
    'error_log' => dirname(__FILE__) . '/logs/php_errors.log'  // Path to error log file
];

if ($debug_config['debug_mode']) {
    ini_set('display_errors', $debug_config['display_errors']);
    ini_set('display_startup_errors', $debug_config['display_errors']);
    error_reporting($debug_config['error_reporting']);
    ini_set('log_errors', $debug_config['log_errors']);
    ini_set('error_log', $debug_config['error_log']);
}

$download_config = [
    'base_path' => dirname(__FILE__) . '/api/download/[Anticheat]',         // Base directory path for anticheat files
    'config_path' => dirname(__FILE__) . '/api/download/[Anticheat]/FiveSecurity/config/Config.lua', // Path to the configuration file
    'zip_filename' => 'fivesecurity.zip',                                           // Name of the zip file for downloads
    'temp_prefix' => 'fivesecurity_',                                          // Prefix for temporary files
    'config_file_path' => 'FiveSecurity/config/Config.lua',       // Relative path to the configuration file
    'license_key_name' => 'FS.LicenseKey',                          // Variable name for the license key in config
    'headers' => [                                                          // HTTP headers for file downloads
        'content_type' => 'application/zip',                                // Content type for zip files
        'cache_control' => 'no-store, no-cache, must-revalidate, max-age=0', // Cache control settings
        'pragma' => 'no-cache',                                            // Additional cache control
        'expires' => '0'                                                    // Expiration time for cache
    ]
];

// Database configuration section
// Contains all database connection settings and database names
$database_config = [
    'host' => 'localhost',                    // Database server hostname
    'username' => 'andarale',                     // Database username
    'password' => 'IimeiphiH8uphookiec3zohph8zo2zee',                 // Database password
    'databases' => [                          // List of different databases used by the system
        'panel' => 'panel',                   // Database for panel functionality
        'counter' => 'counter',               // Database for counting/statistics
        'logs' => 'logs',                     // Database for storing logs
        'serverbans' => 'serverbans',         // Database for server bans
        'auth' => 'auth'                      // Database for authentication
    ]
];

// Website configuration section
// Contains all website-related settings and integrations
$website_config = [
    // Main website settings
    'site_name' => 'FiveSecurity',              // Name of the website
    'site_logo' => 'https://i.postimg.cc/W1vxkRj1/Chat-GPT-Image-31-Mai-2025-21-55-15.png', // URL to the site logo
    'site_domain' => 'panel.fivesecurity.de',           // Main domain of the website
    'site_cdn_domain' => 'cdn.fivesecurity.de', // CDN domain for static content
    'site_description' => 'FiveSecurity FiveM Anticheat', // Meta description for SEO
    'site_keywords' => 'fivem, anticheat, anticheat leaked, free anticheat, FiveSecurity, fivem ac', // Meta keywords for SEO
    'site_author' => 'Auth.',                // Author of the website
    'site_favicon' => '../../../assets/images/favicon.png', // Path to the favicon
    'site_copyright' => '©FiveSecurity',    // Copyright information

    // Cloudflare Turnstile settings for bot protection - https://dash.cloudflare.com/
    'turnstile_site_key' => '0x4AAAAAABfTWWjQ4oppWHn1',     // Public key for Turnstile integration
    'turnstile_secret_key' => '0x4AAAAAABfTWa1YpHckgsrZleDVvJGGLOU', // Secret key for Turnstile integration

    // Discord integration settings - https://discord.com/developers/applications
    'discord_client_id' => '1377752999526404199',               // Discord application client ID
    'discord_client_secret' => 't9pjmNpGBW16jW-X06ra-Pu_B01tMyGJ',           // Discord application client secret
    'discord_redirect_uri' => 'https://panel.fivesecurity.de/discord/callback', // OAuth2 redirect URI
    'discord_bot_token' => 'MTM3Nzc1Mjk5OTUyNjQwNDE5OQ.GbdN8F.QNrgFhFoTf3UlRBE7JQbrLujxNXQ7bv29-vy_U',               // Discord bot token for bot functionality
    'discord_webhook_url' => 'https://canary.discord.com/api/webhooks/1392487861919682702/MXV8K1bcXH0qO_-QPvKekVCsLiOZgV8bKGewgtcv1egWKCRchRQLinR3v4CRRStq6G6F',             // Discord webhook URL for notifications

    // Additional URLs
    'docs_url' => 'https://docs.fivesecurity.de/',  // Documentation URL
    'discord_url' => 'https://discord.gg/fivesecurity', // Discord server invite URL
];