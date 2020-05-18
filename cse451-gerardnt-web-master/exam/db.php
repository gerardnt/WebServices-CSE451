<?php
require_once('info.php');
$_SESSION['error']='';

?>

<!Doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>DB PHP Access</title>
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <!--
Nicholas Gerard
Database PHP access Exam question 
cse451
2/26/2020
-->


</head>

<body>
    <div class="container">
            <div class="jumbotron text-center"><h2>Nicholas Gerard Accessing DB with PHP</h2></div><!--End of Jumbotron-->
		<table class="table table-dark">
		<thead>
		<tr>
		<th>Names</th>
		</tr>
		</thead>
		<tbody>
		<?php
		
		$keys = getKeys();
		
		if(is_null($keys)){
		 $_SESSION['error']= 'fetching the keys';
		}
		
		$count = count($keys);
		
		
		for($key =0; $key<$count ; $key++){
			
			
			
			print '<tr><td>'.$keys[$key].'</td></tr>';
			
		}
		
		
		?>
		</tbody>
		</table>
		<?php
		
		if($_SESSION['error']!=''){
		print '<div class="alert alert-danger alert-dismissible " role="alert">  Error with '.$_SESSION['error'].' Please look at the logs. </div>';
		$_SESSION['error'] = '';
		}
		?>
		
    </div>
    <!--End of container Div -->

    <footer class="container-fluid text-center">
        <p>Nicholas Gerard 2/26/2020</p>
    </footer>

</body>

</html>