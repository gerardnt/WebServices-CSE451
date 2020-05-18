<?php
/*
 * scott campbell
 * php program to display info
 * cse451
 * sprint 2020
 */

session_start();

require_once 'config.php';
// Load the CAS lib
require_once 'vendor/autoload.php';
require_once 'vendor/apereo/phpcas/CAS.php';

// Enable debugging
phpCAS::setDebug('/tmp/cas');

// Initialize phpCAS
phpCAS::client(CAS_VERSION_2_0, $cas_host, $cas_port, $cas_context);

// For production use set the CA certificate that is the issuer of the cert
// on the CAS server and uncomment the line below
// phpCAS::setCasServerCACert($cas_server_ca_cert_path);

// For quick testing you can disable SSL validation of the CAS server.
// THIS SETTING IS NOT RECOMMENDED FOR PRODUCTION.
// VALIDATING THE CAS SERVER IS CRUCIAL TO THE SECURITY OF THE CAS PROTOCOL!
phpCAS::setNoCasServerValidation();

// force CAS authentication
phpCAS::forceAuthentication();
$login_user=phpCAS::getUser();
$_SESSION['uid'] = $login_user;
error_log("keyCas login $login_user");

// at this step, the user has been authenticated by the CAS server
// and the user's login name can be read with phpCAS::getUser().

// logout if desired
if (isset($_REQUEST['logout'])) {
	phpCAS::logout();
}



require_once("info.php");


function getVal($name) {
	if (isset($_REQUEST[$name]) && $_REQUEST[$name] != "")
		return htmlspecialchars($_REQUEST[$name]);
	else
		return "";
}

$msg = "";
$cmd=getVal("cmd");
if ($cmd=="delete") {
	$key = getVal("key");
	if ($key != "") {
		error_log("$uid deleting $key");
		delete($key,$addPass);
	}
	$_SESSION['msg'] = "$key deleted";
	header("Location: display.php");
	exit;
}

if (isset($_SESSION['msg'])) {
	$msg = $_SESSION['msg'];
	$_SESSION['msg'] = "";
}


?>
<!DOCTYPE html>

<HTML lang="en">
<HEAD>
  <META name="generator" content=
  "HTML Tidy for Linux (vers 25 March 2009), see www.w3.org">
  <META charset="utf-8">
  <META http-equiv="X-UA-Compatible" content="IE=edge">
  <META name="viewport" content=
  "width=device-width, initial-scale=1">
  <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

  <TITLE>Campbest KeyCas</TITLE><!-- Bootstrap -->
  <!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

  <LINK rel="stylesheet" href="style.css" type="text/css">
</HEAD>

<BODY>
  <DIV class='container-fluid center' id='mainpage'>
    <H1>Display keys and values</H1>
<?php
print "Welcome $login_user<br>";
?>
<?php
if ($msg != "") {
	print "<div class='alert alert-info'>$msg</div>";
}
?>

    <DIV id="msg"></DIV>

    <DIV id="info">
      <TABLE class="table">
	<THEAD>
	  <TR>
	    <TH>Key</TH>
	    <TH>Value</TH>
		<th>&nbsp;</th>
	  </TR>
	</THEAD>

	<TBODY id='info-body'>
		<?php
		$keys = getKeys();
		if ($keys == null) {
			print "Error getting keys\n";
		} else {
			foreach ($keys as $k) {
				$value = getValue($k);
				if ($value != null)
					print "<tr><td>$k</td><td>$value</td><td><a href='display.php?cmd=delete&key=$k'><button >delete</button></a></td></tr>";
			}
		}
		?>
	</TBODY>
      </TABLE>
<a href='add.php'><button>Add</button></a>
<a href='index.php?cmd=logout'><button>logout</button></a>
    </DIV><!-- close info-->
  </DIV><!-- close 1st container-->

  <DIV id="footer" class="mx-auto" style="width: 400px;">
    Scott Campell - CSE451 - Spring 2020 - Week3<BR>
  </DIV><!-- close footer-->
</BODY>
</HTML>
