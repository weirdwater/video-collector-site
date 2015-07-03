<?php
// Database settings
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'imp-her');
define('DB_USER', 'root');
define('DB_PASS', 'root');

// Error reporting
error_reporting(0);
ini_set('display_errors', 0);

// Turns 'normal' errors into Exceptions.
set_error_handler(function ($errno, $errstr, $errfile, $errline)
{
	throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

// Youtube
define('YOUTUBE_KEY', 'AIzaSyDH8FGoHlIHCvPLLLpwhjEVE12tGdOFSgg');
define('YOUTUBE_API', 'https://www.googleapis.com/youtube/v3/');

// Default timezone
date_default_timezone_set('Europe/Amsterdam');
setlocale(LC_TIME, 'nl_NL');
