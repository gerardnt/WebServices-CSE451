<?php
/*
   Scott Campbell
   data model for Wikipedia articles

   //pk
   //title
   //rating (1->5)
   //reason
   //numAccess


pasword.php must have:
$user, $pass, $db, $host  and $addPass

 */

require_once("password.php");

$mysqli = mysqli_connect($host, $user,$pass,$db);
if (mysqli_connect_errno($mysqli)) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
	die;
}

/*
 * this function will return an array of keys from the table
 * */

function getArticles() {    
	global $mysqli;
	$sql = "select pk,title from wikiTable";
	$res = $mysqli->query($sql);
	if (!$res) {
		//if there was an error, log it and then return null.
		error_log("Error on getArticles select " . $mysqli->error);
		return null;
	}

	$keys = array();
	while( $row = mysqli_fetch_assoc($res)) {
		array_push($keys,$row);
	}
	return $keys;
}

//given a key, find the associate value
function getFullArticle($pk) {
	global $mysqli;
	//update numAccess
	$stmt = $mysqli->prepare("update wikiTable set numAccess=numAccess+1 where pk=?");
	$stmt->bind_param("s",$pk);
	$stmt->execute();
	$stmt->close();

	$stmt = $mysqli->prepare("select title,rating,reason,numAccess from wikiTable where pk=?");
	if (!$stmt) {
		error_log("Error on getFullArticle" . $mysqli->error);
		return null;
	}

	$stmt->bind_param("s",$pk);
	$stmt->execute();
	$stmt->bind_result($title,$rating,$reason,$num);
	$stmt->fetch();
	$ret = array('title'=>$title,'rating'=>$rating,'reason'=>$reason,'numAccess'=>$num);
	return $ret;
}


/*
 * add record
 * */
function add($record,$pass) { 

	error_log("Add called");
	global $addPass;
	global $mysqli;

	if ($pass != $addPass) {
		return "invalid password";
	}

	//check that all fields present
	$fields = array('title','rating','reason');

	foreach ($fields as $f) {
		if (!isset($record[$f]))
		{
			error_log("add->invalid record -missing $f");
			return "Missing field $f";
		}
	}

	$stmt = $mysqli->prepare("insert into wikiTable (title,rating,reason,numAccess) values (?,?,?,0)");
	if (!$stmt) {
		error_log("error on add " . $mysqli->error);
		return "error";
	}

	$stmt->bind_param("sis",$record['title'],$record['rating'],$record['reason']);
	$stmt->execute();

	return "OK";
}


function delete($pk,$pass) {
	global $addPass;
	global $mysqli;

	if ($pass != $addPass) {
		return "invalid password";
	}

	$stmt = $mysqli->prepare("delete from wikiTable where pk=?");
	if (!$stmt) {
		error_log("error on delete" . $mysqli->error);
		return "error";
	}

	$stmt->bind_param("s",$pk);
	$stmt->execute();


	//finish
	return "OK";
}




function createWikiTable() {
	global $mysqli;
	$mysqli->query("drop table if exists wikiTable");
	print $mysqli->error;
	print "creating db\n";
	$r = $mysqli->query("CREATE TABLE `wikiTable` (
		`pk` int(11) NOT NULL AUTO_INCREMENT,
		`title` text NOT NULL,
		`rating` int,
		`reason` text,
		`numAccess` int,
		`timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY (`pk`)
	)");
	print $mysqli->error;
}



?>
