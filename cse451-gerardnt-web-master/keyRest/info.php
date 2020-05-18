<?php
/*
   Nicholas Gerard
   Key value interaction with remote and local

   Based off Dr. Campbells starter code and previous code

pasword.php must have:
$user, $pass, $db, $host $addPass $vendorKey

 */

require 'vendor/autoload.php';
use GuzzleHttp\Client;
require_once("password.php");




$mysqli = mysqli_connect($host, $user,$pass,$db);
if (mysqli_connect_errno($mysqli)) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
	die;
}

/*
 * this function will return an array of keys from the table
 * */

function getKeys() {    
	global $mysqli;
	$sql = "select keyName from keyValue";
	$res = $mysqli->query($sql);
	if (!$res) {
		//if there was an error, log it and then return null.
		error_log("Error on getKeys select " . $mysqli->error);
		return null;
	}

	$keys = array();
	while( $row = mysqli_fetch_assoc($res)) {
		array_push($keys,$row['keyName']);
	}
	return $keys;
}

//given a key, find the associate value
function getValue($k) {
	global $mysqli;
	$stmt = $mysqli->prepare("select value from keyValue where keyName=?");
	if (!$stmt) {
		error_log("Error on getValue " . $mysqli->error);
		return null;
	}

	$stmt->bind_param("s",$k);
	$stmt->execute();
	$stmt->bind_result($value);
	$stmt->fetch();
	return $value;
}


function add($k,$v,$pass) { 

	global $addPass;
	global $mysqli;

	if ($pass != $addPass) {
		return "invalid password";
	}

	//check for duplicate key

	$check = getValue($k);
	if ($check != null) {
		return "Error - duplicate key";
	}

	$stmt = $mysqli->prepare("insert into keyValue (keyName,value) values (?,?)");
	if (!$stmt) {
		error_log("error on add " . $mysqli->error);
		return "error";
	}

	$stmt->bind_param("ss",$k,$v);
	$stmt->execute();

	return "OK";
}


function delete($k,$pass) {
	global $addPass;
	global $mysqli;

	if ($pass != $addPass) {
		return "invalid password";
	}


	$stmt = $mysqli->prepare("delete from keyValue where keyName=?");
	if (!$stmt) {
		error_log("error on delete " . $mysqli->error);
		return "error";
	}


	$stmt->bind_param("s",$k);
	$stmt->execute();


	return "OK";
}




function createInfoTable() {
	global $mysqli;
	$mysqli->query("drop table if exists keyValue");
	print $mysqli->error;
	print "creating db\n";
	$r = $mysqli->query("CREATE TABLE `keyValue` (
		`pk` int(11) NOT NULL AUTO_INCREMENT,
		`keyName` text NOT NULL,
		`value` text NOT NULL,
		`timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY (`pk`)
	)");
	print_r($r);


	print $mysqli->error;
}



function getWeather(){

	global $vendorKey;
	$client = new Client([
		'base_uri' => 'https://api.darksky.net/forecast/' . $vendorKey . '/39.3024,-84.4443',
		'timeout' => 2.0,

	]);

	$response = $client->request('GET','');	

	if ($response->getStatusCode() == 200) {

		$s=$response->getBody();
		$jsonData=json_decode($s);
		if ($jsonData == null) {
			print "Error decoding json";
			return;
		}
		if (isset($jsonData->currently)) {
			$currently = $jsonData->currently;
			$temperature = $currently->temperature;
			return $temperature;
		}
		else {
			return "Invalid response - no current data returned";
		}
	}else{
		return null ;
	}		

}


function getRestKeys(){


try{
	$client = new Client([
		// Base URI is used with relative requests
		'base_uri' => 'http://campbest.451.csi.miamioh.edu/cse451-campbest-web-public-2020/week2/week2-rest.php/api/v1/info',
		// You can set any number of default request options.
		'timeout'  => 2.0,
	]);


	$response =$client->request('GET','');

	if($response->getStatusCode() == 200){


		$s=$response->getBody();
		$jsonData=json_decode($s);
		if ($jsonData == null) {
			print "Error decoding json";
			return;
		}
		
		$jsonFormatted = get_object_vars($jsonData);
		$restkeys = array();

		foreach ($jsonFormatted["keys"] as $k) {
			array_push($restkeys, $k);
		}

		return $restkeys;


	}
	else{
		return null;
	}
}catch (Exception $e) {
		 print "******************************Error ". $url;
		 die;
	 }


}


function getRestValues($key){
	
	
	try {
		$url = 'http://campbest.451.csi.miamioh.edu/cse451-campbest-web-public-2020/week2/week2-rest.php/api/v1/info';



		$send = array();
		$send['key'] =$key;
		
	
		$client = new Client([
			// Base URI is used with relative requests
			'base_uri' => $url,
			// You can set any number of default request options.
			'timeout'  => 2.0,
		]);

		$response =$client->request('POST','',[
			'json' => $send
		]);

		if($response->getStatusCode() == 200){


			$s=$response->getBody();
			$jsonData=json_decode($s);
			if ($jsonData == null) {
				print "Error decoding json";
				return null;
			}

			$value = $jsonData ->value;

			return $value; 	


		}
		else{
			print "error";
			die;
			return null;
		}


	}
	 catch (Exception $e) {
		 print "******************************Error ". $url;
		 die;
	 }
}


	function updateRemote($k, $v){
	try{

		$client = new client([
			'base_uri' => 'http://campbest.451.csi.miamioh.edu/cse451-campbest-web-public-2020/week2/week2-rest.php/api/v1/info',
			'timeout' => 2.0,
		]);



		$send = array();
		$send['password']="password";
		$send['key'] =$k;
		$send['value'] = $v;


		$response = $client->request('PUT','',[
			'json' => $send
		]);

		if($response->getStatusCode() == 200){

			$s=$response->getBody();
			$jsonData=json_decode($s);
			if ($jsonData == null) {
				print "Error decoding json";
				return;
			}

			return 'OK'; 

		}


	}catch (Exception $e) {
		 print "******************************Error ". $url;
		 die;
	 }
}

?>