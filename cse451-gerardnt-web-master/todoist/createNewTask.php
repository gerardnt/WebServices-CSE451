<?php

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

try {

  $line = htmlspecialchars(readline("Task Name: "));	
  
  $header = array();
  $header["Authorization"] = "Bearer ".$testToken;
  $header["Content-Type"] = "application/json";
  $header["X-Request-Id"] =   uniqid();
  $bodya = array();
  $bodya['content'] = $line;
  $body = json_encode($bodya);

  $response = $client->request('POST','tasks',['headers'=>$header, 'body'=>$body]);
  
} catch (Exception $e) {
  print "There was an error creating the tasks from todoist";
  print_r($e);
  exit;
}
$body = (string) $response->getBody();
$jbody = json_decode($body);
if (!$jbody) {
  error_log("no json");
  exit;
}

  print  $jbody->content . "\n";
 


?>
