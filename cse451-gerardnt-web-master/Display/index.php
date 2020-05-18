<?php require_once("info.php"); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Week 3 PHP Assignment</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <style>
        /* Set black background color, white text and some padding */
        footer {
            background-color: #555;
            color: white;
            padding: 20px;
        }

        .container-fluid {
            padding-left: 10%;

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
        Javascript Week 3 PHP Assignment
        2/14/2020 
    -->
</head>

<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <div class="col-md-10 text-left">
                <div class="container">
                    <div class="jumbotron" style="text-align: center;">
                        <h1>Nick Gerard Week 3 PHP Assignment</h1>
                        <img src="colorado.jpg" width="120" height="120"
                            alt="Nicholas Gerard's Logo. A picture of a Tent and ATV in the Mountains of Colorado"
                            class="imgLogo">
                    </div>

                    <div>
                        <table class="table table-dark">
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
			                    print "<tr><td>".$keys[$i]."</td><td>" . getValue($keys[$i]) . "</td></tr>";
		                	 }
	    		                ?>
                            </tbody>
                        </table>
                    </div>
                    <!--Table div-->
                </div>
                <!--container Div-->
            </div> <!-- col-sm-8-->
        </div>
        <!--row-->
    </div>
    <!--container fluid-->
    <footer class="container-fluid text-center">
        <p>Nicholas Gerard Week 3 PHP Assignment 2/14/2020</p>
    </footer>
    <!--container-->
</body>

</html>