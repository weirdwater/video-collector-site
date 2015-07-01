<?php
require 'settings.php';
require 'Classes/YoutubeHandler.php';

// Setup output array that will be changed into JSON
$reply = [
    'errors' => [],
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
	array_push( $reply['errors'],[
		'message' => 'Database Connection Failed '. $e->getMessage() .' '. $e->getFile() .' on line '. $e->getLine(),
		'code'    => $e->getCode()
	]);
	echo json_encode($reply);
	exit;
}

$youtube = new \imp\YoutubeHandler();
