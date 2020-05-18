<?php

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
scott campbell
html for Wiki List
cse 451
spring 2020
-->
<html lang="en">
<head>
  <meta name="generator" content=
  "HTML Tidy for HTML5 for Linux version 5.2.0">
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content=
  "width=device-width, initial-scale=1">
  <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
  <title>Campbest Wiki</title><!-- Bootstrap -->
  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href=
  "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <!-- jQuery library -->

  <script src=
  "https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js">
  </script><!-- Latest compiled JavaScript -->

  <script src=
  "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js">
  </script>
  <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->

  <script src="script.js">
  </script>
  <script>
  let user = "<?php print $user;?>"
  </script>
  <link rel="stylesheet" href="style.css" type="text/css">
</head>
<body>
  <div class='container-fluid text-center' id='mainpage'>
    <div class='alert alert-info'>
      <h1>Wiki Articles I Like</h1>
      <h2>Welcome 
      <script>
      document.write(user);
      </script></h2>
      <h3>Temperature is <span id='temp'>Loading</span></h3>
    </div>
    <div id='msg'>
<div id='msgText' class='alert alert-warning'></div>
      <a onclick='hideMessage()'>Hide</a>
    </div>
    <div id='info'>
      <table class="table text-center">
        <thead>
          <tr>
            <th class='text-center'>Title</th>
          </tr>
        </thead>
        <tbody id='info-body'></tbody>
      </table>
    </div><!--close info-->
    <div id="details">
      <h2>Details</h2>
      <table class="table">
        <tbody id='detail-body'>
          <tr>
            <td>Title</td>
            <td id='detail-title'></td>
          </tr>
          <tr>
            <td>Rating</td>
            <td id='detail-rating'></td>
          </tr>
          <tr>
            <td>Reason Added</td>
            <td id='detail-reason'></td>
          </tr>
          <tr>
            <td>Number of Accesses</td>
            <td id='detail-numAccess'></td>
          </tr>
          <tr>
            <td>link</td>
            <td id='detail-link'></td>
          </tr>
 
        </tbody>
      </table><span id='deleteItem'></span><br><a id='hideDetails'>Hide</a>
    </div><!-- close details-->
    <div id="addForm">
      <h2>Add</h2>
      <form id='addItemForm' name="addItemForm">
        <div class='form-group'>
          <label for='add-title'>Title</label> <input type='text'
          id='add-title' required="">
        </div>
        <div class='form-group'>
          <label for='add-reason'>Why is this important
          (reason)</label> 
          <textarea cols="40" rows="10" id='add-reason'></textarea>
        </div>
        <div class='form-group'>
          Rating <label><input type='radio' name='rating' value=
          "0" required>0 (worst)</label> <label><input type='radio' name=
          'rating' value="1">1</label> <label><input type='radio'
          name='rating' value="2">2</label> <label><input type=
          'radio' name='rating' value="3">3</label>
          <label><input type='radio' name='rating' value=
          "4">4</label> <label><input type='radio' name='rating'
          value="5">5</label>
        </div><input type='submit' value='Submit'> <input type=
        'submit' value='Cancel' onclick='cancel()'>
      </form>
    </div><!-- close addForm-->
    <a id='showAddForm'>Add Wiki Article</a>
  </div><!-- close 1st container-->
  <div id='footer' class='container-fluid center text-center'>
    Scott Campell - CSE451 - Spring 2020 - WikiTable<br>
  </div><!-- close footer-->
</body>
</html>
