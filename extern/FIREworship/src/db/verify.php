<?php
	session_start();
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	if(isset($_POST['code']) ){
		try{
			// try to get an id_token
			$code = $_POST['code'];
			$url = 'https://www.googleapis.com/oauth2/v3/tokeninfo';

			$data = ["id_token" => $code];

			$options = [
				'http' => [
					'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
					'method'  => 'POST',
					'content' => http_build_query($data) 
				]
			];

			$context  = stream_context_create($options);
			$verify = json_decode(file_get_contents($url, false, $context),true);
			//print_r($verify);
			//Verify id_token
			if($verify["aud"] == "74508341761-fmq8neihdmcaka98a0utjv4i0b39ml2m.apps.googleusercontent.com"){
				//check in DB if sub matches email
				$db = new PDO('sqlite:FIREworship.db');
				$gId = $verify['sub'];
				$email = $verify['email'];
				$qry = "SELECT * FROM user WHERE gId = ? AND email = ?";
				$result = $db->prepare($qry);
				$result->execute(array($gId,$email));
				$res=$result->fetchAll();
				// create new user with admin = 0 if not in DB
				if( empty($res) ){
					$lvl = 0;
					$qry = "INSERT INTO user (gId,email,lvl) VALUES(?,?,?)";
					$insert= $db->prepare($qry);
					if(!$insert)
						 print_r($db->errorInfo());
					if(!$insert->execute(array($gId,$email,$lvl) ))
						exit("ERROR: Insert".print_r($db->errorInfo()));
				}
				else{
					//print_r($res);
					$lvl = $res[0]['lvl'];
				}
				$db=0;
				//start session
				$_SESSION['admin'] = $gId;
				$_SESSION['lvl'] = $lvl;
			}
		}
		catch(Exception $e){
			echo $e;
		}
	}
	
	
?>