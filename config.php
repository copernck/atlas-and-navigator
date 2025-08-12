<?php
// basic configuration settings

// error reporting (dev vs production)
error_reporting(E_ALL); // show all errors during development
ini_set('display_errors', 1);
// ini_set('display_errors', 0); // hide errors in production
// ini_set('log_errors', 1); // log errors in production
// ini_set('error_log', '/path/outside/webroot/php-error.log'); // set a secure path on dreamhost

// site settings
define('SITE_NAME', 'Atlas & Navigator'); // Or your final site name
define('BASE_URL', ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST']));

// ** IMPORTANT: REPLACE WITH YOUR ACTUAL DREAMHOST MYSQL DETAILS !! **
define('DB_HOST', 'dicksonatuserat.atlasandnavigator.site'); // Check DreamHost Panel (e.g., 'mysql.yourdomain.com' or 'localhost')
define('DB_NAME', 'atlas_giveme_quest'); // The DB name you created
define('DB_USER', 'dicksonat_userat'); // The DB user you created
define('DB_PASS', 'N93i^@*vwhy7@^hdhy38##$'); // The password for that DB user
define('DB_CHARSET', 'utf8mb4'); // Use utf8mb4 for broader character support

// start session if needed globally - must be before any HTML output
// ensure this file has NO whitespace or text before <?php
session_start();

// ** NO closing  tag here AND ABSOLUTELY NO OTHER TEXT OR BLANK LINES BEFORE OR AFTER THE PHP CODE **
// This prevents "headers already sent" errors caused by stray output.
