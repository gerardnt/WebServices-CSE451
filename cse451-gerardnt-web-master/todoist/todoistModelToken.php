<?php
/*
 * scott campbell
 * example guzzle client to call dark web
 * */


//this calls in all autoload packages installed via composer
require __DIR__ . '/vendor/autoload.php'; 
require "key.php";

//bring guzzle client into code
use GuzzleHttp\Client;

//base uri -> it is important it end in /
$uri = "https://api.todoist.com/rest/v1/";


//create a new client
$client = new Client([
	// Base URI is used with relative requests
	'base_uri' => $uri,
	// You can set any number of default request options.
	'timeout'  => 2.0,
]);

function getProjects() {
	global $client;
	global $token;
	try {
		$header = array("Authorization"=>"Bearer " . $token);

		$response = $client->request('get',"projects",['headers'=>$header]);
	} catch (Exception $e) {
		print "There was an error getting the projects from todoist";
		header("content-type: text/plain",true);
		print_r($e);
		$a=print_r($e,true);
		error_log($a);
		exit;
	}
	$body = (string) $response->getBody();
	$jbody = json_decode($body);
	if (!$jbody) {
		error_log("no json");
		exit;
	}

	return $jbody;
}



function addProject($name) {
	global $client;
	global $token;

	//make get request
	$body = array();
	$body['name'] = $name;

	$response = $client->request('post','projects',['headers' => ['Authorization'=>'Bearer '.$token,'X-Request-Id'=>uniqid('todoist')],'json'=>$body,'debug'=>False]);

	if ($response->getStatusCode() == 200) {

		$s=$response->getBody();
		$jsonData=json_decode($s);
		if ($jsonData == null) {
			print "Error decoding json";
			return null;
		}
		return $jsonData;
	}
	else {
		return null;
	}
}

function deleteProject($projectID) {
	global $token;
	global $client;

	//make get request
	$body = array();
	$body['content'] = $name;

	$response = $client->request('post','tasks',['headers' => ['Authorization'=>'Bearer '.$token,'X-Request-Id'=>uniqid('todoist')],'json'=>$body,'debug'=>False]);

	if ($response->getStatusCode() == 200) {

		$s=$response->getBody();
		$jsonData=json_decode($s);
		if ($jsonData == null) {
			print "Error decoding json";
			return null;
		}
		return $jsonData;
	}
	else {
		return null;
	}
}


function getTasks($projectId) {
	//we can't use filters since not premium, search all tasks
	global $token;
	global $client;

	$response = $client->request('get','tasks',['headers' => ['Authorization'=>'Bearer '.$token],'debug'=>False]);

	if ($response->getStatusCode() == 200) {

		$s=$response->getBody();
		$jsonData=json_decode($s);
		if ($jsonData == null) {
			print "Error decoding json";
			return null;
		}
		//get just requested projects
		$retData = array();
		foreach ($jsonData as $t) 
			if ($t->project_id == $projectId)
				$retData[] = $t;
		return $retData;
	}
	else {
		return null;
	}

}

function addTask($name,$projectID) {
	global $token;
	global $client;

	//make get request
	$body = array();
	$body['content'] = $name;
	if ($projectID != null) 
		$body['project_id'] = $projectID;

	$response = $client->request('post','tasks',['headers' => ['Authorization'=>'Bearer '.$token,'X-Request-Id'=>uniqid('todoist')],'json'=>$body,'debug'=>False]);

	if ($response->getStatusCode() == 200) {

		$s=$response->getBody();
		$jsonData=json_decode($s);
		if ($jsonData == null) {
			print "Error decoding json";
			return null;
		}
		return $jsonData;
	}
	else {
		return null;
	}
}


function closeTask($taskid) {
	
	global $token;
	global $client;

	$response = $client->request('POST','tasks/'.$taskid.'/close',['headers' => ['Authorization'=>'Bearer '.$token],'debug'=>False]);

	if ($response->getStatusCode() == 204) {
		
		return $taskid . "Has been closed \n";
		
	}
	else {
		return "Task code not be closed";
	}
}


