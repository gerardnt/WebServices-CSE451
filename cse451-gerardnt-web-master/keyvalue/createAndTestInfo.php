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

print "connected\n";
print "droping db\n";
$r = $mysqli->query("drop database if exists " . $db);
print_r($r);
print $mysqli->error;
$mysqli->query("create database " . $db);
print $mysqli->error;
$mysqli->close();


require_once("info.php");
createInfoTable();
print "\n\nadd - should work\n";
print_r(add("test1","hello","password"));
print_r(add("test2","hello2","password"));
print "\n\ngetKeys - should return 2 keys\n";
print_r(getKeys());
print ("\n\nget test1 - should return 'hello'\n");
print_r(getValue("test1"));
print ("\n\nget invalid - should return null, nothing\n");
print_r(getvalue("hello"));
print "\n";
add("Miami","Computer Science","password");
add("Quadratic","ax^2+b^x=c","password");
add("c45443","3ff3ffde","password");
?>
