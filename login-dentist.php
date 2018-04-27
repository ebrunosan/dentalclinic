<?php
  session_start();
  // reset SESSION dentistID
  if ( isset($_SESSION["dentistID"]) ) { 
     unset($_SESSION["dentistID"]);
  }

  require 'database.php';
  $pdo = Database::connect();

// ***** SELECT Dentist ***** 
function selectDentist($pdo, $dentistEmail) {
  try {
    $sql="SELECT dentistID FROM Dentist WHERE dentistEmail='".$dentistEmail."'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    if ( $stmt->rowCount() == 0 ) {
       return false;
    }
  
    foreach($stmt->fetchAll() as $row) {
      $_SESSION["dentistID"] = $row['dentistID'];
    }
    return true;
  } catch (PDOException $ex) {
    echo  $ex->getMessage();
    return false;
  }
}

// ***** Main *****
if ($_SERVER["REQUEST_METHOD"] == "POST") {
   $dentistEmail   = trim($_POST['dentistEmail']);
   if ( selectDentist($pdo, $dentistEmail) ) {
      echo "<script>window.open('dentist-list.php','_self')</script>";
      exit();
  }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Dental Clinic - Staff Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script defer src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="styles/style.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>

<body>
  <nav id="navBar">
          <ul>
            <li><a href="index.html">Home</a></li>
            <li><a href="contact.html">Contact Us</a></li>
            <li><a href="login.php">Login</a></li>
            <li><a class="active" href="login-dentist.php">Staff Login</a></li>
          </ul>
        </nav>
    <div class="container" id="main">
      <div class="row">
          <p>
          <fieldset class="fsStyle"><legend class="legendStyle">Please enter your staff email ID</legend>
             <form class="form-horizontal" action="login-dentist.php" method="post">
              <div class="form-group">
                <label class="control-label col-sm-2" for="dentistEmail">Email:</label>
                <div class="col-sm-5">
                  <input type="email" class="form-control" name="dentistEmail" required value="<?php echo !empty($dentistEmail)?$dentistEmail:'';?>">
                </div>
              </div>
              <div class="form-group"> 
                <div class="col-sm-offset-2 col-sm-2">
                  <button type="submit" class="btn btn-default">Sign in</button>
                </div>
              </div>
             </form>
          </fieldset>
          </p>
        
      </div>
    </div> <!-- /container -->
  </body>
</html>