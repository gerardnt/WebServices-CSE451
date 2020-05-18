<?php
/*
 * Nicholas Gerard
 * Program to add more keys and values
 * 2/28/2020
 * Based off Dr. Campbell's starter code 
 */

session_start();
require_once("info.php");

function getVal($name) {
	if (isset($_REQUEST[$name]) && $_REQUEST[$name] != "")
		return htmlspecialchars(trim($_REQUEST[$name]));
	else
		return "";
}

$cmd=getVal("cmd");

if (!isset($_SESSION['uid'])) {
	error_log("invalid access to add.php");
	header("Location: index.php");
	$_SESSION['msg'] = "Please login first";
	exit;
}
$uid = $_SESSION['uid'];

error_log($uid ." called add.php");

$key=getVal("key");
$value = getVal("value");
$location = $_REQUEST['update'];
$msg="";
if ($key != "" && $value != "" && $location == 'Local' ) {
		error_log("$uid added $key with $value");
		$msg = add($key,$value,$addPass);
		if ($msg == "OK") {
		$_SESSION['msg'] = "$key added";
		header("Location: display.php");
		exit;
		}
}
if($key != "" && $value != "" && $location == 'Remote'){
	error_log("$uid added $key with $value to Remote location");
	$msg = updateRemote($key,$value);
	if ($msg == "OK") {
		$_SESSION['msg'] = "$key added";
		header("Location: display.php");
		exit;
		}
}

	

?>
<!DOCTYPE html>

<HTML lang="en">

<HEAD>
    <!--- 
		Nicholas Gerard 
		add.php 
		This is meant to add keys and values to my DB or Dr. Campbell's database
		2/28/2020
		
		This code was provided by Dr. Campbell
		
		https://stackoverflow.com/questions/8605516/default-select-option-as-blank : for select input
		
		
		--->
    <META name="generator" content="HTML Tidy for Linux (vers 25 March 2009), see www.w3.org">
    <META charset="utf-8">
    <META http-equiv="X-UA-Compatible" content="IE=edge">
    <META name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

    <TITLE>Nicholas Gerard Week 4 </TITLE><!-- Bootstrap -->
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
        crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous"></script>

    <LINK rel="stylesheet" href="style.css" type="text/css">
</HEAD>

<BODY>
    <DIV class='container center' id='mainpage'>
        <H1>Display keys and values</H1>

        <?php
if ($msg != "") {
	print "<div class='alert alert-danger'>";
	print $msg;
	print "</DIV>";
}
?>
        <form id="loginForm" action="add.php" method="post" class="was-validated">
            <div class="form-group">
                <label for="key">Enter Key</label>
                <input type="text" class="form-control" placeholder="Enter Key" id="key" name='key' required
                    value='<?php print $key;?>'>
            </div>
            <div class="form-group">
                <label for="value">Value</label>
                <input type="text" class="form-control" placeholder="Value" id="value" name='value' required
                    value='<?php print $value;?>'>
            </div>
            <div class="form-group">
                <label for="update">Where To Update:</label>
                <select class="form-control" id="update" name="update" required value='<?php print $value;?>'>
                    <option disabled selected value> -- select an option -- </option>
                    <option>Local</option>
                    <option>Remote</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
        <br>
        <a href='display.php'><button class="btn btn-primary">Cancel</button></a>

    </DIV><!-- close 1st container-->

    <DIV id="footer" class="mx-auto" style="width: 400px;">
        Nicholas Gerard - CSE451 - Spring 2020 - Week4<BR>
    </DIV><!-- close footer-->
</BODY>

</HTML>