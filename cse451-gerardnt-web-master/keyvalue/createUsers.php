<?php
/*
 * Scott Campbell
 * program to create user accounts in mysql
 * cse451
 * spring 2020
 *
 */

require "password.php";

//check that all required variables are filled out
$required = array("user","pass","host","addPass","db");
$ok = true;
foreach ($required as $r) {
	if (!isset($$r) || $$r == "") {
		print "variable $r not configured\n";
		$ok = false;
	}
}
if ($ok===false)
{
	print "\n\nFailed to create users since password.php is not configured right\n\n";
	die;
}



//create a file holding the mysql commands to create users
// we can't directly call mysql since there is no user setup for connecting
// so we have to create this file and then run the mysql client from the 
// "command line"
$f=fopen("createUsers.sh","w");
fwrite($f,"create database if not exists $db;\n");
fwrite($f,"create user IF NOT EXISTS '$user'@'localhost' identified by '$pass';\n");
fwrite($f,"grant all privileges on $db.* to '$user'@'localhost';\n");
fwrite($f,"create user IF NOT EXISTS '$user' identified by '$pass';\n");
fwrite($f,"grant all privileges on $db.* to '$user'@'%';\n");
fclose($f);

//invoke mysql and pass in this script
echo exec("mysql <createUsers.sh");

print "\nCreated users, now testing \n";

//now check to see we can connect
$mysqli = mysqli_connect($host, $user,$pass,$db);
if (mysqli_connect_errno($mysqli)) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
	die;
}

echo "Looks like your users are setup\n";
//remove script
unlink("createUsers.sh");


