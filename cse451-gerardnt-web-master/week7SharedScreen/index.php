<?php
/*
 * Nick Gerard
 * cse451
 * shared screen
 * March 18, 2020
 *
 *  Sources Used:
 *
 * 		Dr. Campbell's starter code 
 *		Bootstrap for better error messages
 * */

// Load the settings from the central config file
require_once 'config.php';
// Load the CAS lib
require_once 'vendor/autoload.php';
require_once 'vendor/apereo/phpcas/CAS.php';

phpCAS::client(CAS_VERSION_2_0, $cas_host, $cas_port, $cas_context);

// For production use set the CA certificate that is the issuer of the cert
// on the CAS server and uncomment the line below
// phpCAS::setCasServerCACert($cas_server_ca_cert_path);

// For quick testing you can disable SSL validation of the CAS server.
// THIS SETTING IS NOT RECOMMENDED FOR PRODUCTION.
// VALIDATING THE CAS SERVER IS CRUCIAL TO THE SECURITY OF THE CAS PROTOCOL!
phpCAS::setNoCasServerValidation();

session_start();
if (!isset($_SESSION['user']) || !isset($_SESSION['session_time']) ||  (time()-$_SESSION['session_time']) >10) {

        // Initialize phpCAS
        // force CAS authentication
        phpCAS::forceAuthentication();

        $user = phpCAS::getUser();
        $_SESSION['user'] = $user;
        $_SESSION['session_time'] = time();
		
}
else
        $user =$_SESSION['user'] . " (session)";


// logout if desired
if (isset($_REQUEST['logout'])) {
        phpCAS::logout();
}
?>
<!DOCTYPE html>
<!--
	Nicholas Gerard
	CSE 451
	3/18/20
	Week 7 shared screen assignment 
-->

<HTML lang="en">
<HEAD>
  <META name="generator" content=
  "HTML Tidy for Linux (vers 25 March 2009), see www.w3.org">

  <TITLE>screen</TITLE>
  <meta charset="UTF-8">
  <SCRIPT src=
  "https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"
  > </SCRIPT>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

<script> var user = '<?php echo $_SESSION['user'] ?>';</script>
<SCRIPT src="render.js"></script>
<style>
#buttons {
clear:both;
}
#users {
padding-left: 20px;
}

#pic {
float: left;
padding-right: 20px;
}

</style>

</HEAD>

<BODY>
 <div class="container">
  <DIV id='pic'><IMG src='' alt='shared screen display'></DIV>
<div id='users'><h2>Users</h2><ul></ul></div>

  <DIV id='buttons'>
    <BUTTON id='clear'>Clear</BUTTON>

    <select id="size" name="size">
      <option value="point">Point</option>
      <option valu="block">4x4 Block </option>
    </select>

    <DIV id="msg"></DIV>
  </DIV>
  </diV>
</BODY>
</HTML>
