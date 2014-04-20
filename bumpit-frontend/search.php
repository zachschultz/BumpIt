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
    ini_set("display_errors", true);

    session_start();

    // Store username and password in session variables
    if (isset($_POST['signin_user']))
    {
      $_SESSION['signin_user']=$_POST['signin_user'];
      $_SESSION['signin_pass']=$_POST['signin_pass'];
      
    }


    // Reset username and pass to that of the session variables
    $signin_user = $_SESSION['signin_user'];
    $signin_pass = $_SESSION['signin_pass'];
    $isSigningIn = $_POST['isSigningIn'];
    
 


    // Connect to the database, check if error happens
    $conn = pg_connect("host=postgres.cise.ufl.edu dbname=bumpit user=ec1 password = UFProgE1");
    $_SESSION['conn'] = $conn;
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
      $countQuery = sprintf("SELECT count('user_id') AS numUsers FROM users;");  
      $countResult = pg_fetch_row(pg_query($conn, $countQuery)); 
      // user_ID (the result is an array silly!)
      $count = $countResult[0];
      // Add new account to the database
      $registerQuery = sprintf("INSERT INTO users VALUES ($count, '$reg_user', '$reg_pass', 'false', '0');");
      $registerResult = pg_query($conn, $registerQuery);
      
    }
    
    // User is just signing in
    if ($isSigningIn){
    // Sign in to database
    $query = sprintf("SELECT login("."'".$_SESSION['signin_user']."'".","."'".$_SESSION['signin_pass']."'".");");
    $result = pg_query($conn, $query);
    }

    // Grab userID
    $result = pg_fetch_row(pg_query($conn, "SELECT user_id FROM users WHERE userName = "."'".$_SESSION['signin_user']."'".""));
    $userID = $result[0];
    // Store userID inside a session variable
    $_SESSION['userID']=$userID;


/************************************************************** 
**************************************************************
*****************    FUNCTIONS     ***************************
**************************************************************
***************************************************************/

    // Logout function
    if($_GET['logoutBtn']){logout();}
    
    function logout(){
      $query = sprintf("SELECT logout("."'".$_SESSION['signin_user']."'".");");
      $result = pg_query($_SESSION['conn'], $query);
      header("Location: http://www.cise.ufl.edu/~zschultz");
      exit();

    }

    // Load Friend List function
    //if($_GET['friendList']){friendList();}

    function friendList(){
      $userID = $_SESSION['userID'];
      $query = sprintf("SELECT username FROM (SELECT friend_id FROM friends WHERE user_id = $userID) AS currFriends, users WHERE friend_id = user_id;");
      $result = pg_query($_SESSION['conn'], $query);
        
      echo "<table class='table table-striped table-bordered table-hover'>\n";
      echo "<caption>Friend List</caption>\n";
      while ($line=pg_fetch_array($result, null, PGSQL_ASSOC)) {
        echo "\t<tr>\n";
        foreach ($line as $col_value) {
          echo "\t\t<td>$col_value</td>\n";
        }
        echo "\t</tr>\n";
      }     
      echo "</table>\n";
    }
  

    // LOGIC NOT WORKING RIGHT YET (DISPLAYING MULTIPLES OF SAME NAME)
    // Find Friends function
    //if($_POST['search']){searchForFriends();}

    function searchForFriends() {
      $search = $_POST['search'];
      $userID = $_SESSION['userID'];
      $query = sprintf("SELECT DISTINCT findFriends($userID, "."'".$search."'".");");
      echo "DEBUG PURPOSES-> SEARCHFORFRIENDS QUERY IS: ".$query;
      $result = pg_query($_SESSION['conn'], $query);

      echo "<table class='table table-striped table-bordered table-hover'>\n";
      echo "<caption>Users</caption>\n";
      while ($line=pg_fetch_array($result, null, PGSQL_ASSOC)) {
        echo "\t<tr>\n";
        foreach ($line as $col_value) {
          echo "\t\t<td>$col_value</td>\n";
        }
         echo "\t</tr>\n";
        }
        echo "</table>\n";
  
    }

    // Load friend's posts function
    //if($_GET['friendsPosts']){friendsPosts();}

    function friendsPosts() {
      $userID = $_SESSION['userID'];
      $query = sprintf("SELECT userName, posts FROM 
        (SELECT friend_id FROM friends WHERE user_id = $userID)
        as currFriends, posts WHERE currFriends.user_id = 
        posts.user_id");
      $result = pg_query($_SESSION['conn'], $query);

      echo "<table class='table table-striped table-bordered table-hover'>\n";
      echo "<caption>Users</caption>\n";
      while ($line=pg_fetch_array($result, null, PGSQL_ASSOC)) {
        echo "\t<tr>\n";
        foreach ($line as $col_value) {
          echo "\t\t<td>$col_value</td>\n";
        }
         echo "\t</tr>\n";
        }
        echo "</table>\n";
    }

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
            <li><a href="index.php">Home</a></li>
            <li><a id="friendList" href="friendlist.php?friendList=true">Friend List</a></li>
            <li><a id="friendsPosts" href="friendsposts.php?friendsPosts=true">Friend's Posts</a></li>
            <li>
              <a id="logoutBtn" href="index.php?logoutBtn=true">Logout</a>
            </li>

            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Dropdown <b class="caret"></b></a>
            
              <ul class="dropdown-menu">
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
          <form class="navbar-form navbar-right" role="search" action="search.php?search=true" method="post">
            <div class="form-group">
              <input name="search" type="text" class="form-control" placeholder="Search">
              <input name="isSigningIn" type="hidden" value="1">
            </div>
            <button type="submit" class="btn btn-default">Submit</button>
          </form>
         
          
        </div><!-- /.navbar-collapse -->

        



      </div><!-- /.container-fluid -->
      </nav>

      <!-- Main content -->

      
      <div class="container">
        <div class="starter-template">
        <form action = ""



      <?php 
      //Check if friends posts function was called
      if($_GET['friendsPosts']){friendsPosts();}
      
      // Check if friend list function was called
      if($_GET['friendList']){friendList();}

      // Check if search function was called
      if($_POST['search']){searchForFriends();}
      ?>

         </div>
       </div><!-- /.container -->












    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>