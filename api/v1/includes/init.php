<?php
require 'settings.php';
require 'Classes/YoutubeHandler.php';
require 'Classes/DatabaseHandler.php';

// Setup output array that will be changed into JSON
$reply = [
    'status' => [],
    'data' => []
];
header('Content-Type: application/json');


// Connect to database
try
{
	$db = new PDO(
		'mysql:host='.DB_HOST.';dbname='.DB_NAME.';port='.DB_PORT,
		DB_USER,
		DB_PASS
	);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$db->exec('SET NAMES \'utf8\'');
}
catch (Exception $e)
{
	$reply['status']['message'] = 'PDOException: ' . $e->getMessage();
    $reply['status']['file'] = $e->getFile();
    $reply['status']['line'] = $e->getLine();
    $reply['status']['code'] = $e->getCode();
	echo json_encode($reply);
	exit;
}

$youtube = new \imp\YoutubeHandler();
$database = new \imp\DatabaseHandler();
