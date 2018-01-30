<?php
	// output headers so that the file is downloaded rather than displayed
	header('Access-Control-Allow-Origin: *');
	$key = $_GET['key'];
	
	// get the csv
	$spreadsheet_url = "https://docs.google.com/spreadsheet/pub?key=$key&single=true&gid=0&output=csv";
	if(!ini_set('default_socket_timeout',    15)) echo "<!-- unable to change socket timeout -->";

	if (($handle = fopen($spreadsheet_url, "r")) !== FALSE) {
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			fputcsv($output, $data);
		}
		fclose($handle);
	}
	else
		die("Problem reading csv");
	
?>