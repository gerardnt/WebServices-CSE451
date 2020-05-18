<?php
/*
  Nicholas Gerard
  Exam 01 question php db


 */

$mysqli = mysqli_connect("ceclnx01.csi.miamioh.edu","exam","password","exam");
if (mysqli_connect_errno($mysqli)) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
	die;
}

/*
 * this function will return an array of keys from the table
 * */

function getKeys() {    
	global $mysqli;
	$sql = "select name from test";
	$res = $mysqli->query($sql);
	if (!$res) {
		//if there was an error, log it and then return null.
		error_log("Error on getKeys select " . $mysqli->error);
		return null;
	}

	$keys = array();
	while( $row = mysqli_fetch_assoc($res)) {
		array_push($keys,$row['name']);
	}
	return $keys;
}


?>
