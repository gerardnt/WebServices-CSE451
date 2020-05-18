<?php
    session_start();

    if(isset($_REQUEST["cmd"]) && $_REQUEST["cmd"] =='logout'){
        session_unset();
        header("Location: index.php");
        error_log('User has logged out');
        exit();
    }

    if(isset($_SESSION["uid"])){
        error_log('User is already logged in. Redirecting to display.php');
        header("Location: display.php");
        exit();
    }

    if(isset($_REQUEST["uid"]) && isset($_REQUEST['password']) && $_REQUEST['password'] == 'PASSWORD'){
        $_SESSION['uid'] = htmlspecialchars(trim($_REQUEST["uid"]));
        error_log( $_SESSION['uid'] .' has successfully logged in');
        header("Location: display.php");
        exit();
    }


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Gerardnt Week 3 PHP</title>
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
        <div>
            <h2>Display keys and Values </h2>
        </div>
        <!--End of Header div -->
        <div class="lg-6">
            <form method="POST" action="index.php">
                <div class="form-group">
                    <?php 
        if(isset($_REQUEST["uid"]) && isset($_REQUEST["password"]) && htmlspecialchars($_REQUEST['password']) != 'PASSWORD' ){

            print "<div class='alert alert-danger' role='alert'> The Username or Password is incorrect. Please Try again. </div>"; 
            error_log("The username or Password is incorrect. Please try again.");
            $temporaryUid = htmlspecialchars(trim($_REQUEST['uid']));    
         }
        ?>
                    <label for="uid">User uniqueId</label>
                    <input type="text" class="form-control" id="uid" name="uid" value="<?php print $temporaryUid; ?>"
                        placeholder="Enter username" required>
                </div><!-- End of username in form div-->
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password"
                        required>
                </div> <!-- End of password in form div-->
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
            <div style="margin-top :2% ;">
                <footer class="container-fluid text-center" style="border-top: 5px black solid">
                    <p>Nicholas Gerard Week 3 PHP Session Assignment 2/18/2020</p>
                </footer>
            </div>
            <!--End of footer Div -->
        </div>
        <!--End of Form Div--->
    </div>
    <!--End of container-->
</body>

</html>