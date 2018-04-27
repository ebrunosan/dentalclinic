<?php
  session_start();
  // reset SESSION pID
  if ( isset($_SESSION["pID"]) ) { 
     unset($_SESSION["pID"]);
  }

  require 'database.php';
  $pdo = Database::connect();

// ***** SELECT PATIENT ***** 
function selectPatient($pdo, $dob, $email) {
  try {
    $sql="SELECT pID FROM Patient WHERE email = :email AND dob = :dob";
    $stmt = $pdo->prepare($sql);

    $stmt->bindValue(':dob'   , $dob,            PDO::PARAM_STR);
    $stmt->bindValue(':email' , $email,          PDO::PARAM_STR);

    $stmt->execute();
  
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    if ( $stmt->rowCount() == 0 ) {
       echo "Argment not found!";
       return false;
    }
  
    foreach($stmt->fetchAll() as $row) {
      $_SESSION["pID"] = $row['pID'];
    }
    return true;
  } catch (PDOException $ex) {
    echo  $ex->getMessage();
    return false;
  }
}

// ***** Main *****
if ($_SERVER["REQUEST_METHOD"] == "POST") {
   $dob     = trim($_POST['dob']);
   $email   = trim($_POST['email']);

   //echo "main".$dob.$email;
   if ( selectPatient($pdo, $dob, $email) ) {
      echo "<script>window.open('customer.php','_self')</script>";
      exit();
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Dental Clinic - Login</title>
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
            <li><a class="active" href="login.php">Login</a></li>
            <li><a href="login-dentist.php">Staff Login</a></li>
          </ul>
        </nav>
    <div class="container" id="main">
      <div class="row">
          <p>
          <fieldset class="fsStyle"><legend class="legendStyle">Do you already have an account?</legend>
             <form class="form-horizontal" action="login.php" method="post">
              <div class="form-group">
                <label class="control-label col-sm-2" for="email">Email:</label>
                <div class="col-sm-5">
                  <input type="email" class="form-control" name="email" required value="<?php echo !empty($email)?$email:'';?>">
                </div>
              </div>
              <div class="form-group">
                <label class="control-label col-sm-2" for="pwd">Date of Birth:</label>
                <div class="col-sm-5"> 
                  <input type="date" class="form-control" name="dob" required  value="<?php echo !empty($dob)?$dob:'';?>">
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
        
          <p>
          <fieldset class="fsStyle"><legend class="legendStyle">Are you new here?</legend>
              <div class="col-sm-offset-2 col-sm-5">
                <a href="customer-account.php" class="btn btn-default" role="button">Create an account</a>
              </div>
          </fieldset>
          </p>
        
      </div>
    </div> <!-- /container -->
  </body>
</html>