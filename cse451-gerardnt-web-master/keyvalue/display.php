<?php 
 require_once("info.php"); 
 session_start();
 if(isset($_REQUEST["cmd"]) && $_REQUEST["cmd"] =='delete'){
    $_SESSION["message"] = $_REQUEST["key"];
    delete($_REQUEST["key"],$addPass);
    header("Location: display.php");
    exit();

}
 if(!isset($_SESSION["uid"])){
    header("Location: index.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Week 3 PHP Assignment</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
        integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
        crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
        integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6"
        crossorigin="anonymous"></script>
    <style>
        /* Set black background color, white text and some padding */
        footer {
            background-color: #555;
            color: white;
            padding: 20px;
        }

        .imgLogo {
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
    <!--
       Nicholas Gerard 
        CSE 451
        Week 3 PHP Session Assignment
        2/18/2020 

	Sources Used:
        	https://getbootstrap.com/docs/4.0/components/forms/  : for forms and validation 
        	https://getbootstrap.com/docs/4.0/components/alerts/ : for alerts
        	Provided slides and documents 

    -->

</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-10">
                <div class="jumbotron" style="text-align: center;" id="jumbotron">
                    <h1>Nick Gerard Week 3 PHP Assignment</h1>
                    <img src="colorado.jpg" width="120" height="120"
                        alt="Nicholas Gerard's Logo. A picture of a Tent and ATV in the Mountains of Colorado"
                        class="imgLogo">
                    <?php
                        print "<p> <h4> Welcome ".$_SESSION["uid"]." </h3></p>" ; 
           
                        if(isset($_SESSION["message"] ) && !empty($_SESSION['message'])){
                             print "<div class='alert alert-primary' role='alert'>".$_SESSION["message"] ." deleted.</div>";
                             error_log($_SESSION['message']." has been successfully deleted.");
                             unset($_SESSION["message"]);
                         }
              
                    ?>
                </div> <!-- Jumbotron -->
            </div> <!-- jumbotron containter -->
        </div> <!--row for the jumbotron -->

        <div class="col-10">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">Key</th>
                        <th scope="col">Value</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
			                 $keys = getKeys();
                             $count = count($keys);
                             
		                	 for ($i=0 ; $i<$count; $i++){
                                
                                if(is_null($keys[$i])){
                                  $_SESSION['message'] =  "An error has occurred when grabbing the keys. Please contact your Admin."  ; 
                                  header("Location: display.php"); 
                                  exit();
                                }
                                else{
                                    print "<tr><td>".$keys[$i]."</td><td>" . getValue($keys[$i]) . " </td> <td> <a href='display.php?cmd=delete&key=$keys[$i]'><button class ='btn btn-primary'>Delete</button> </a></td></tr>";
                                }
		                	 }
	    		    ?>
                </tbody>
            </table>
        </div>
        <!--Table div-->
        <a href="add.php"><button class="btn btn-primary">Add</button></a>
        <a href="index.php?cmd=logout"><button class="btn btn-primary">Logout</button></a>
    </div>
    <!--container Div-->
    <footer class="container-fluid text-center">
        <p>Nicholas Gerard Week 3 PHP Assignment 2/18/2020</p>
    </footer>
</body>
</html>