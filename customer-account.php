<?php
  // Get SESSION pID if is set after user log in
  session_start();

  if ( isset($_SESSION["pID"]) ) { 
     $GLOBALS["pID"] = $_SESSION["pID"];
  }

  // Connect DATABASE
  require 'database.php';
  $pdo = Database::connect();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script defer src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="styles/style.css">
</head>

<?php
// ***** INSERT PATIENT ***** 
function insertPatient($pdo, $name, $dob, $gender, $phone, $email) {
  try {
    $sql="INSERT INTO Patient (name, dob, gender, phone_no, email) VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($name, $dob, $gender, $phone, $email));

    $GLOBALS["pID"] = $pdo->lastInsertId();
    $_SESSION["pID"] = $GLOBALS["pID"];
    
    return true;
  } catch (PDOException $ex) {
    echo  $ex->getMessage();
    return false;
  }
}

// ***** UPDATE PATIENT ***** 
function updatePatient($pdo, $name, $dob, $gender, $phone, $email) {
  try {
    $sql="UPDATE Patient SET name=:name, dob=:dob, gender=:gender, phone_no=:phone, email=:email WHERE pid=:pID";
    $stmt = $pdo->prepare($sql);

    $stmt->bindValue(':name'  , $name,           PDO::PARAM_STR);
    $stmt->bindValue(':dob'   , $dob,            PDO::PARAM_STR);
    $stmt->bindValue(':gender', $gender,         PDO::PARAM_STR);
    $stmt->bindValue(':phone' , $phone,          PDO::PARAM_STR);
    $stmt->bindValue(':email' , $email,          PDO::PARAM_STR);
    $stmt->bindValue(':pID'   , $GLOBALS["pID"], PDO::PARAM_INT);
    
    $stmt->execute();
    return true;
  } catch (PDOException $ex) {
    echo  $ex->getMessage();
    return false;
  }
}

// ***** SELECT PATIENT ***** 
function selectPatient($pdo) {
  try {
    $sql="SELECT name, dob, gender, phone_no, email FROM Patient WHERE pID = :pID";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':pID'    , $GLOBALS["pID"], PDO::PARAM_INT);  
    
    $stmt->execute();
    if($stmt === false) {
       return "";
    }

    $rowCount = $stmt->rowCount();
    if ( $rowCount == 0 ) {
       echo "Argment pID not found!";
       return "";
    } else {
       $row = $stmt->fetch(PDO::FETCH_ASSOC);
       return $row;
    }
  } catch (PDOException $ex) {
    echo  $ex->getMessage();
    return "";
  }
}

// ***** Main *****

// Select PATIENT IF it is the first visit from different page and pID is set!
if (isset($GLOBALS["pID"]) and basename($_SERVER["HTTP_REFERER"]) != basename($_SERVER["SCRIPT_FILENAME"])) {
   $row= selectPatient($pdo);
   if ( !empty($row) ) {
      $name    = $row['name'];
      $dob     = $row['dob'];
      $gender  = $row['gender'];
      $phone   = $row['phone_no'];
      $email   = $row['email'];
   }
// Do an Insert OR Update AFTER a POST METHOD
} else if ($_SERVER["REQUEST_METHOD"] == "POST") {
   $pID     = trim($_POST['pID']);
   $name    = trim($_POST['name']);
   $dob     = trim($_POST['dob']);
   $gender  = trim($_POST['gender']);
   $phone   = trim($_POST['phone']);
   $email   = trim($_POST['email']);

   if (!empty($_POST['insert-submit'])) {
      if ( insertPatient($pdo, $name, $dob, $gender, $phone, $email) ) {
          $pID  = $GLOBALS["pID"];
      }
   
   } else if (!empty($_POST['update-submit'])) {
      if ( updatePatient($pdo, $name, $dob, $gender, $phone, $email) ) {
         //echo "Update success!";
      }
   }
}
?>

<body>
    <div class="container">
      <div class="row">
          <p>
          <fieldset class="fsStyle"><legend class="legendStyle">Patient's Data</legend>
             <form class="form-horizontal" action="customer-account.php" method="post">
              <input type="hidden" class="form-control" name="pID" readonly value="<?php echo !empty($pID)?$pID:'';?>">
              <div class="form-group">
                <label class="control-label col-sm-2" for="name">Name:</label>
                <div class="col-sm-5"> 
                  <input type="text" class="form-control" name="name" required value="<?php echo !empty($name)?$name:'';?>">
                </div>
              </div>
              <div class="form-group">
                <label class="control-label col-sm-2" for="email">Email:</label>
                <div class="col-sm-5">
                  <input type="email" class="form-control" name="email" required value="<?php echo !empty($email)?$email:'';?>">
                </div>
              </div>
              <div class="form-group">
                <label class="control-label col-sm-2" for="dob">Date of Birth:</label>
                <div class="col-sm-2"> 
                  <input type="date" class="form-control" name="dob" required value="<?php echo !empty($dob)?$dob:'';?>">
                </div>
              </div>
              <div class="form-group">
                <label class="control-label col-sm-2" for="gender">Gender:</label>
                <div class="col-sm-1"> 
                  <input type="text" class="form-control" name="gender" required value="<?php echo !empty($gender)?$gender:'';?>">
                </div>
              </div>
              <div class="form-group">
                <label class="control-label col-sm-2" for="phone">Phone:</label>
                <div class="col-sm-5"> 
                  <input type="text" class="form-control" name="phone" value="<?php echo !empty($phone)?$phone:'';?>">
                </div>
              </div>
              <div class="form-group"> 
                <div class="col-sm-offset-2 col-sm-5">
                  <?php
                    if ( empty($pID) ) {
                       echo "<button type='submit' value='insert-submit' name='insert-submit' class='btn btn-default'>Create</button>";
                       echo "<a href='login.php' class='btn btn-default' role='button'>Cancel</a>";
                    } else {
                       echo "<button type='submit' value='update-submit' name='update-submit' class='btn btn-default'>Update</button>";

                       echo"<a href='customer.php' class='btn btn-default' role='button'>Return Patient's Info</a>";
                    
                       if ( isset($_SESSION["dentistID"]) ) {
                         echo "<a href='dentist-list.php' class='btn btn-default' role='button'>Return Dentist's List</a>";
                       } else {
                         echo "<a href='login.php' class='btn btn-default' role='button'>Logout</a>";
                       }
                    }
                  ?>
                </div>
              </div>
             </form>
          </fieldset>
          </p>
      </div>
    </div> <!-- /container -->
  </body>
</html>
