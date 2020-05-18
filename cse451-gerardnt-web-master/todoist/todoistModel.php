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
try {
  $header = array("Authorization"=>"Bearer " . $_SESSION['token']);

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

