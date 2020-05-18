<?php
/*
 * Nicholas Gerard
 *  3/18/202
 *  Week 7 shared screen db.php
 *  used to store points with the associated users along with whose currently on the page 
 *
 *   Code used:
 *      Dr. Campbells starter code
 *
 */



require_once("password.php");

$mysqli = mysqli_connect("localhost", $user,$pass,$db);
if (mysqli_connect_errno($mysqli)) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
	die;
}

//lock table
function lockTable($type) {
	global $mysqli;
	global $db;

	$result=$mysqli->query("lock tables $db $type");
	if (!$result) {
		error_log("Error on getLock $type");
	}
}

function unlockTable() {
	global $mysqli;
	global $db;

	$result=$mysqli->query("unlock tables");
	if (!$result) {
		error_log("Error on getLock $type");
	}

}

//run a query to get users who have requested the page in the past second 
function getUsers(){
	
	global $db;
	global $mysqli;

	lockTable("read");

	$result = $mysqli->query("select owner from $db where (unix_timestamp() - unix_timestamp(createTime)) <= 1 ");
	
	if (!$result) {
		error_log("Error on getValue " . $mysqli->error);
		unlockTable();
		return array();
	}
	$users = array();
	
	while ($row = $result->fetch_assoc()) {
		array_push($users,$row);
	}
	
	unlockTable();
	return $users;
	
}

//upsert the current user's time accessed for the page
function addCurrentUsers($user){
	global $mysqli;
	global $db;
	
	$username = htmlspecialchars($user);
	
	lockTable("write");
	
	//check to see if the user is already in the db
	$exists = $mysqli->query("select owner from $db where owner = '$username'");
	
	//if they exist update the users time else insert them with the time 
	if(mysqli_num_rows($exists)>=1){
			$result = $mysqli->query("update $db set createTime = CURRENT_TIMESTAMP where owner = '$username'");
	}else{
		$result = $mysqli->query("insert into $db (owner,createTime) values ('$username',CURRENT_TIMESTAMP)");
	}

	if (!$result) {
		
		error_log("Error on Insert current Users " . $mysqli->error);
		unlockTable();
		print $mysqli->error;
		
	}
	
	unlockTable();
	
}

//run a query to delete points after 60 second
function FadeOutPoints() {
	global $db;
	global $mysqli;
	lockTable("write");
	$mysqli->query("delete from $db where (unix_timestamp() - unix_timestamp(createTime)) > 60");
	unlockTable();
}

//return a list of all points indb
function getPoints() {
	global $mysqli;
	global $db;

	FadeOutPoints();	

	lockTable("read");
	$result = $mysqli->query("select `x`,`y`,`x1`,`y1`,`r`,`g`,`b`,unix_timestamp(createTime) as createTime from $db order by createTime");
	if (!$result) {
		error_log("Error on getValue " . $mysqli->error);
		unlockTable();
		return array();
	}
	$points =array();
	while ($row = $result->fetch_assoc()) {
		array_push($points,$row);
	}

	unlockTable();
	return $points;
}

//add point to db
function addPoint($x,$y,$x1,$y1,$r,$g,$b) {
	global $mysqli;
	global $db;
	lockTable("write");
	if (!is_numeric($x) || !is_numeric($y) || !is_numeric($x1) || !is_numeric($y1) || $x >= $x1 || $y >= $y1) {
		return array("status"=>"fail","message"=>"invalid input");
	}
	$mysqli->query("insert into $db (x,x1,y,y1,r,g,b) values ('$x','$x1','$y','$y1','$r','$g','$b')");
	print $mysqli->error;
	unlockTable();
}

//clear all points from db
function clearPoints() {
	global $mysqli;
	global $db;
	lockTable("write");
	$mysqli->query("delete from $db");
	print $mysql->error;
	unlockTable();
}


//helper function to create shared data table
function createScreenTable() {
	global $db;
	global $mysqli;
	print "creating db\n";
	$mysqli->query("drop table `$db`");
	$r = $mysqli->query("CREATE TABLE `$db` (
		`pk` int(11) NOT NULL AUTO_INCREMENT,
		`owner` text,
		`x` int,
		`x1` int,
		`y` int,
		`y1` int,
		`r` int,
		`g` int,
		`b` int,
		`createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY (`pk`)
	)");
	print $mysqli->error;
}

//uncomment and run from command line ONCE to create table
//make sure to recomment before using in rest server!!!!!!!!
//createScreenTable();
//addPoint(0,0,10,10,255,255,255);
//print_r(getPoints());

?>
