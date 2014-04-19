#!/usr/local/bin/php
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BumpIt</title>
    
    <!-- Bootstrap -->
    <link href="css/bootstrap.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="css/custom.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>


    
    <!-- Login PHP -->
    <?php
    
    $conn = pg_connect("host=postgres.cise.ufl.edu dbname=bumpit user=ec1 password = UFProgE1");
    if (!$conn) { 
      echo "Connection failed";
    exit;
  }
    
    // User is registering
    $isRegistering = $_POST['isRegistering'];
    $reg_user = $_POST['reg_user'];
    $reg_pass = $_POST['reg_pass'];
    
    if ($isRegistering)
    {
      //Create an account 
      echo "REGISTERING  \n";
      $countQuery = sprintf("SELECT count('user_id') AS numUsers FROM users;");  
      $countResult = pg_fetch_row(pg_query($conn, $countQuery)); 
      echo "Current number of users: ".$countResult[0];
      // user_ID (the result is an array silly!)
      $count = $countResult[0];
      
      // Add new account to the database
      $registerQuery = sprintf("INSERT INTO users VALUES ($count, '$reg_user', '$reg_pass', 'false', '0');");
      $registerResult = pg_query($conn, $registerQuery);
      
    }
    // User is just signing in
    $signin_user = $_POST['signin_user'];
    $signin_pass = $_POST['signin_pass'];
    // Sign in to database
    $query = sprintf("SELECT real_login('$signin_user','$signin_pass');");
    $result = pg_query($conn, $query);



    ?>














    <img class="header-logo" src="img/bumpit.png"></img>

    <!-- Navigation Bar -->
        <nav class="navbar navbar-default" role="navigation">
        <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
          <ul class="nav navbar-nav">
            <li class="active"><a href="#">Home</a></li>
            <li><a href="splash.html">Logout</a></li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Dropdown <b class="caret"></b></a>
            
              <ul class="dropdown-menu">
                <li><a href="#">Action</a></li>
                <li><a href="#">Another action</a></li>
                <li><a href="#">Something else here</a></li>
                <li class="divider"></li>
                <li><a href="#">Separated link</a></li>
                <li class="divider"></li>
                <li><a href="#">One more separated link</a></li>
              </ul>
            </li>
          </ul>
          <!-- Search bar -->
          <form class="navbar-form navbar-right" role="search">
            <div class="form-group">
              <input type="text" class="form-control" placeholder="Search">
            </div>
            <button type="submit" class="btn btn-default">Submit</button>
          </form>
         
          
        </div><!-- /.navbar-collapse -->
      </div><!-- /.container-fluid -->
</nav>











    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>
