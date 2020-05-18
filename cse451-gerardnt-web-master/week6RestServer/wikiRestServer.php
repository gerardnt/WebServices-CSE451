<?php
/*

   Nicholas Gerard
   week 6 wiki rest server 
   3/11/20
   
   used:
		Dr. Campbell's Week2-rest.php as a referenece 
		https://stackoverflow.com/questions/34519747/how-to-acess-nested-json-object-with-json-decode for how to access the information from a guzzle response
   

   api:

   Provide all available titles from the wiki database along with their keys 

   url: /api/v1/wiki
   method: get
   json_in: N/A
   json_out: {"keys":[{"pk":"value", "title":"value"}]}


   Obtains the title, rating, reason, and the number of times this wiki entry has been accessed from the wiki database for a given primary key in the path.

   url: /api/v1/wiki/{pk}
   method: get
   json_in: N/A
   json_out: { "value":{"title":"value","rating":"value","reason":"value","numAccess":"value"}}


   Returns the current temperature for Oxford OH, provided by the DarkSky API
    
   url: /api/v1/temp
   method: get
   json_in: N/A
   json_out: {"temp":"value"}


   Delete all the information from an entry based on the inputed primary key 
   
   url: /api/v1/wiki
   method: delete
   json_in: {"pk":"value"}
   json_out: {"status":"value", "msg":"value"}
   

   Inserts an entry into the wiki DB with provided inputs of title, rating, and reason of a wiki article.

   url: /api/v1/wiki
   method: put
   json_in: { "title":"value", "rating":"value","reason":"value"}
   json_out: {"status":"value", "msg":"value"}
   
   
 */
 
session_start();
 
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,PUT,DELETE,OPTIONS");
header("content-type: application/json");
header("Access-Control-Allow-Headers: Content-Type");


require_once("wikiModel.php");
require 'vendor/autoload.php';
use GuzzleHttp\Client;



function sendJson($status,$msg,$result) {
	$returnData = array();
	$returnData['status'] = $status;
	$returnData['msg'] = $msg;
	foreach ($result as $k=>$v) {
		$returnData[$k] = $v;
	}

	print json_encode($returnData);
	exit;
}

//parse parts
if (isset($_SERVER['PATH_INFO'])) {
	$parts = explode("/",$_SERVER['PATH_INFO']);
	//sanitize
	for ($i=0;$i<count($parts);$i++) {
		$parts[$i] = htmlspecialchars($parts[$i]);
	}
} else {
	$parts = array();
}

array_shift($parts);	//get rid of first part of the url


$method = strtolower($_SERVER['REQUEST_METHOD']);

if ($method == "options") {
	exit;
}


if ($method=="get" &&  sizeof($parts) == 3 && $parts[0] == "api"  && $parts[1] == "v1" && $parts[2] == "wiki") {
	
	$articleKeys = getArticles();
	$data = array();
	foreach ($articleKeys as $k => $v){
		$data[$k] = $v;
	}	
	$dataToReturn = array('keys' => $data);
	$returned = print_r($dataToReturn,true);
	error_log($returned);
	sendJson("ok","",$dataToReturn);
	
}
else if ($method=="get" &&  sizeof($parts) == 4 && $parts[0] == "api"  && $parts[1] == "v1" && $parts[2] == "wiki"){

	$data = getFullArticle(htmlspecialchars($parts[3]));
	$dataToReturn = array('value'=>$data);
	$returned = print_r($dataToReturn,true);
	error_log($returned);
	sendJson("ok","",$dataToReturn);
	
}
else if($method=="get" &&  sizeof($parts) == 3 && $parts[0] == "api"  && $parts[1] == "v1" && $parts[2] == "temp"){
	
	global $vendorKey;
	
	 //check to see if the Darksky has been called in the last 5 minutes. If not we call it and set the variable.	
	if(!isset($_SESSION['temperature']) || (time() - $_SESSION['tempTime'] ) > (5*60) ){
		
		$client = new Client([
			'base_uri' => 'https://api.darksky.net/forecast/' . $vendorKey . '/39.3024,-84.4443',
			'timeout' => 2.0,

		]);

		$response = $client->request('GET','');	
		
		$statusCode = $response->getStatusCode();
		
		if ($statusCode == 200){
			$s=$response->getBody();
			$jsonData=json_decode($s);
			if ($jsonData == null) {
				sendJson("FAIL", "Error decoding JSON from DarkSky","");
			}
			if (isset($jsonData->currently)) {
				$currently = $jsonData->currently;
				$temperature = $currently->temperature;
				$_SESSION['temperature'] = $temperature;
				$_SESSION['tempTime'] = time();
				sendJson('ok',"",array('temp'=>$temperature));
			}
		}else{
			
			sendJson('FAIL','Error retrieving information from DarkSky. Status Code : $statusCode','');
			}
	}else{
		
		sendJson('ok',"",array('temp'=>$_SESSION['temperature']));
	}
	
}


if($method=="delete" &&  sizeof($parts) == 3 && $parts[0] == "api"  && $parts[1] == "v1" && $parts[2] == "wiki"){
	
	global $addPass;
	
	$jsonBody = array();
	$errormsg = "none";
	try {
		# Get JSON as a string
		$json_str = file_get_contents('php://input');

		# Get as an object
		$jsonBody = json_decode($json_str,true);
	} catch (Exception $e) {
		$errormsg = $e->getMessage();
		sendJson("FAIL","JSON DECODE ERROR " . $errormsg,"");
	}

	if (!isset($jsonBody['pk'])) {
		sendJson("FAIL","JSON DECODE ERROR no primary key (pk)","");
	}
	$pk = htmlspecialchars($jsonBody['pk']);
	$msg = delete($pk,$addPass);
	sendJson('ok','',$msg);

}

if($method=="put" &&  sizeof($parts) == 3 && $parts[0] == "api"  && $parts[1] == "v1" && $parts[2] == "wiki"){
	
	global $addPass;
	global $vendorKey;
	
	$jsonBody = array();
	$errormsg = "none";
	
	try {
		# Get JSON as a string
		$json_str = file_get_contents('php://input');

		# Get as an object
		$jsonBody = json_decode($json_str,true);
	} catch (Exception $e) {
		$errormsg = $e->getMessage();
		sendJson("FAIL","JSON DECODE ERROR " . $errormsg,"");
	}
	if (!isset($jsonBody['title']) || !isset($jsonBody['rating']) || !isset($jsonBody['reason'])) {
		sendJson("FAIL","JSON DECODE ERROR","");
	}
	
	$title = htmlspecialchars($jsonBody['title']);
	$raiting = htmlspecialchars($jsonBody['rating']);
	$reason = htmlspecialchars($jsonBody['reason']);
	
	//prepare the record to be inserted after checking the titles validity
	$record = array('title' => $title, 'rating' => $raiting, 'reason' =>$reason);
	
	
	$client = new Client([
		'base_uri' => 'https://en.wikipedia.org/w/api.php?format=json&action=query&prop=extracts&exintro&explaintext&redirects=1&titles='.$title,
		'timeout' => 2.0,

	]);
	
	$response = $client->request('GET','');	
	
	$statusCode = $response->getStatusCode();
	
	if ($statusCode == 200){
		$s=$response->getBody();
		
		$jsonData=json_decode($s);
		if ($jsonData == null) {
			sendJson("FAIL", "Error decoding JSON from Wikipedia","");
		}
		if (isset($jsonData->query)) {
			
			//checks to see if the title is valid
			if(!isset($jsonData->query->pages->{-1})){
			 $msg = add($record,$addPass);
			 sendJson('ok','',$msg);
			}else{
				sendJson('FAIL','Invalid Title','');	
			}
		}
	}
	else{
		sendJson('FAIL','Error retrieving information from wikipedia. Status Code :'. $statusCode,'');
	}
	
}

header($_SERVER['SERVER_PROTOCOL'] . ' 404 Invalid URL' , true, 400);
?>