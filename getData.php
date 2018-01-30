<?php

require 'functions.php';
define('DEBUG', false);

if (defined('DEBUG')) {
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
}

if( isset($_GET['con']) ){
	$con = filter_input(INPUT_GET,'con');
	echo json_encode( ["content" => getContent($con)] );
}

if ( isset($_GET['media']) ){
	$media = substr( urldecode( filter_input(INPUT_GET,'media') ), 1 );
	$exists = file_exists($media)? "true" : "false";
	echo json_encode(  ["url" 	 => $media,
						"exists" => $exists]);
}
