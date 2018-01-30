<?php
	// output headers so that the file is downloaded rather than displayed
	header('Access-Control-Allow-Origin: *');
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename=data.csv');
	$key = $_GET['key'];
	// create a file pointer connected to the output stream
	$output = fopen('php://output', 'w');
	// get the csv
	$spreadsheet_url = "https://docs.google.com/spreadsheets/d/".$key."/pub?output=csv";
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