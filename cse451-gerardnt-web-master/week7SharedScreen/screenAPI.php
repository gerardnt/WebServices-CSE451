<?php
/*
   Nicholas Gerard 
   Rest Api for shared screen
   
   
		Code used:
			Dr. Campbells starter code
 */


//the following is used to allow any site to access our rest api

header("Access-Control-Allow-Origin: *"); header("Access-Control-Allow-Methods: GET,POST,PUSH,OPTIONS"); header("content-type: application/json");
header("Access-Control-Allow-Headers: Content-Type");

require_once("db.php");

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

array_shift($parts);	//get rid of first part of url which is bogus
//get method type

$method = strtolower($_SERVER['REQUEST_METHOD']);

if ($method == "options") {
	exit;
}

if ($method=="get" &&  sizeof($parts) == 0) {
	$points = getPoints();
	sendJson("OK","",$points);
}

if ($method=="delete" &&  sizeof($parts) == 0) {
	clearPoints();
	sendJson("OK","",array());
}

//returns the current users who are requesting the image 
if ($method=="get" &&  sizeof($parts) == 2 && $parts[0] == 'v1' && $parts[1] == 'users') {
	$users = getUsers();
	$data = array();
	foreach($users as $k => $v){
		$data[$k] = $v;

	}
	$return = array('users' => $data);
	sendJson("OK","",$return);
}


else if ($method=="get" &&  sizeof($parts) == 4) {
	addPoint($parts[0],$parts[1],$parts[2],$parts[3],0,0,0);
	sendJSON("ok","",$retData);
}
else if ($method=="get" &&  sizeof($parts) == 7) {
	addPoint($parts[0],$parts[1],$parts[2],$parts[3],$parts[4],$parts[5],$parts[6]);
	sendJSON("ok","",$retData);
}

else if ($method=="post") {

		$json_str = file_get_contents('php://input');
		$json = json_decode($json_str);
	addPoint($json->x,$json->y,$json->x1,$json->y1,$json->r,$json->g,$json->b);
		$retData=[];
	sendJSON("ok","",$retData);
}






header($_SERVER['SERVER_PROTOCOL'] . ' 404 Invalid URL' , true, 400);
?>
