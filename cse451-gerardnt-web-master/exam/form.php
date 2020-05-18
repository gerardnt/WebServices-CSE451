<!Doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Form Submission</title><script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <!--
Nicholas Gerard
Form Submission Exam question 
cse451
2/26/2020
-->


</head>

<body>
    <div class="container">
            <div class="jumbotron text-center"><h2>Nicholas Gerard Form Submission</h2></div><!--End of Jumbotron-->
	<?php

if(isset($_REQUEST['A']) && isset($_REQUEST['B'])){
	
 print '<div class="alert alert-primary" role="alert">Submitted Value for Name is : '. htmlspecialchars($_REQUEST['A']).'</div>';
 print '<div class="alert alert-primary" role="alert">Submitted Value for Some Text is : '. htmlspecialchars($_REQUEST['B']).'</div>';
 
	
}

?>

</div>
    <footer class="container-fluid text-center">
        <p>Nicholas Gerard 2/26/2020</p>
    </footer>

</body>

</html>