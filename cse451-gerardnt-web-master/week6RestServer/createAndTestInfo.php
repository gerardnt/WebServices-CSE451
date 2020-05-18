<?php
/*
Scott Campbell
cse451
spring 2020
Week2 

this file create the database and tables and create some basic data.

Run this first

*/

ini_set('register_argc_argv', 0);  

if (!isset($argc) || is_null($argc))
{ 
    echo 'Not CLI mode';
	die;
}


//create database -connect directly to mysql

require_once("password.php");
$mysqli = mysqli_connect("localhost", $user,$pass,$db);
if (mysqli_connect_errno($mysqli)) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
	die;
}


require_once("wikiModel.php");
createWikiTable();

$r = array('title'=>'test','rating'=>1,'reason'=>'some reason that is really long and interesting. This is related to something I am doing');
print add($r,$addPass);

$r=getArticles();
var_dump($r);


$r = array('title'=>'test1','rating'=>1,'reason'=>'some description');
print add($r,$addPass);
$r=getArticles();
var_dump($r);

$r= getFullArticle(2);
var_dump($r);
?>
