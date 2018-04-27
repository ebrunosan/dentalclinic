<?php
session_start();
// Get SESSION pID if is set after user log in
if ( isset($_SESSION["pID"]) ) { 
   $GLOBALS["pID"] = $_SESSION["pID"];
}

require 'database.php';
$pdo = Database::connect();
  
// ***** SELECT PATIENT-PRINCIPAL name ***** 
function selectPatientById($pdo, $pID) {
  try {
    $sql="SELECT name, dob, gender, phone_no, email FROM Patient WHERE pID = :pID";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':pID', $pID, PDO::PARAM_INT);  
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

// ***** SELECT ALL DEPENDENTS ***** 
function selectDependentsPatient($pdo) {
  try {
    $sql="SELECT pID as dependent_pID, name, dob, gender, email, phone_no FROM Patient where principal_pID=:pID";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':pID', $GLOBALS["pID"], PDO::PARAM_INT);  
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    return $stmt;
  } catch (PDOException $ex) {
    echo  $ex->getMessage();
    return false;
  }
}

// ***** Main *****
  
// ***** INSERT DEPENDENT for patient ***** 
if (isset($_POST['create-dep'])) {
  try {
    $sql="INSERT INTO Patient (name, dob, gender, phone_no, email, principal_pID) VALUES (:name, :dob, :gender, :phone, :email, :principal_pID)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':name'         , $_POST['name']  , PDO::PARAM_STR);
    $stmt->bindValue(':dob'          , $_POST['dob']   , PDO::PARAM_STR);
    $stmt->bindValue(':gender'       , $_POST['gender'], PDO::PARAM_STR);
    $stmt->bindValue(':phone'        , $_POST['phone'] , PDO::PARAM_STR);
    $stmt->bindValue(':email'        , $_POST['email'] , PDO::PARAM_STR);
    $stmt->bindValue(':principal_pID', $GLOBALS["pID"] , PDO::PARAM_INT);
    $stmt->execute();
  } catch (PDOException $ex) {
    echo  $ex->getMessage();
  }
}

// ***** UPDATE DEPENDENT PATIENT ***** 
if (isset($_POST['update-dep'])) {
  try {
    $sql="UPDATE Patient SET name=:name, dob=:dob, gender=:gender, phone_no=:phone, email=:email WHERE pid=:pID";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':name'   , $_POST['name']         , PDO::PARAM_STR);
    $stmt->bindValue(':dob'    , $_POST['dob']          , PDO::PARAM_STR);
    $stmt->bindValue(':gender' , $_POST['gender']       , PDO::PARAM_STR);
    $stmt->bindValue(':phone'  , $_POST['phone']        , PDO::PARAM_STR);
    $stmt->bindValue(':email'  , $_POST['email']        , PDO::PARAM_STR);
    $stmt->bindValue(':pID'    , $_POST['dependent_pID'], PDO::PARAM_INT);
    $stmt->execute();
  } catch (PDOException $ex) {
    echo  $ex->getMessage();
  }
}

// ***** DELETE DEPENDENT - PATIENT ***** 
if (isset($_POST['delete-dep'])) {
  try {
    $sql="DELETE FROM Patient WHERE pID=".$_POST['dependent_pID'];
    $stmt = $pdo->prepare($sql);    
    $stmt->execute();
  } catch (PDOException $ex) {
    echo  $ex->getMessage();
    return false;
  }
}

$row = selectPatientById($pdo, $GLOBALS["pID"]);
if ( !empty($row) ) {
   $namePrincipal = $row['name'];
} else {
   echo "Argment pID AND treatmentID not found!";
   die;
}
  
$stmt = selectDependentsPatient($pdo);
$rowCount = $stmt->rowCount();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
</head>

<body>
    <div class="container">
      <div class="row">
          <p>
          <fieldset class="fsStyle"><legend class="legendStyle">Patient's Info</legend>
            <form class="form-horizontal" action="" method="post">
              <input type="hidden" class="form-control" name="pID" readonly value="<?php echo !empty($pID)?$pID:'';?>">
              <div class="form-group">
                <label class="control-label col-sm-2" for="namePrincipal">Patient:</label>
                <div class="col-sm-5"> 
                  <input type="text" class="form-control" name="namePrincipal" readonly value="<?php echo !empty($namePrincipal)?$namePrincipal:'';?>">
                </div>
              </div>

              <div class="form-group"> 
                <div class="col-sm-offset-2 col-sm-5">
                  <a href="customer.php" class="btn btn-default" role="button">Patient's Info</a>
                  <?php 
                     if ( isset($_SESSION["dentistID"]) ) {
                       echo "<a href='dentist-list.php' class='btn btn-default' role='button'>Return Dentist's List</a>";
                     } else {
                       echo "<a href='login.php' class='btn btn-default' role='button'>Logout</a>";
                     }
                  ?>
                </div>
              </div>
            </form>
          </fieldset>
          </p>
      </div>
      
      <!-- CRUD DEPENDENT BEGIN -->
      <div class="row"><p>
      <fieldset class="fsStyle"><legend class="legendStyle">Dependents</legend>
      <button data-toggle="collapse" data-target="#demo" class="btn btn-default">+ New Dependent</button>
      <div id="demo" class="collapse" style="margin-top: 10px;">
      
      <form class="form-horizontal" action="" method="post">
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
              <div class="col-sm-offset-2 col-sm-2">
              <input type="submit" name="create-dep" value="Create" class="btn btn-primary">
          </div>
      </form>
      </div></fieldset></p></div>

      <div class="row">
          <?php
            if ($rowCount > 0) {
               echo '<table class="table table-striped table-bordered">
                     <thead><tr>
                     <th>Name</th>
                     <th>DOB</th>
                     <th>Gender</th>
                     <th>Email</th>
                     <th>Phone</th>
                     <th>Action</th></tr></thead>';
              
               // output data of each row
               echo '<tbody>';
               foreach($stmt->fetchAll() as $row) {
                  $dependent_pID = $row["dependent_pID"];
                  $name   =$row["name"];
                  $dob    =$row["name"];
                  $gender =$row["gender"];
                  $email  =$row["email"];
                  $phone  =$row["phone_no"];
                  echo "<tr>
                        <td class='border-class'>".$row["name"]."</td>
                        <td class='border-class'>".$row["dob"]."</td>
                        <td class='border-class'>".$row["gender"]."</td>
                        <td class='border-class'>".$row["email"]."</td>
                        <td class='border-class'>".$row["phone_no"]."</td>
                        <td><a class='btn btn-info btn-sm' data-toggle='modal' data-target='#myModal$dependent_pID'>Edit</a> 
                            <a class='btn btn-danger btn-sm' data-toggle='modal' data-target='#delModal$dependent_pID'>Delete</a></td>";
                  include 'dependent-delete.php';
                  include 'dependent-edit.php';
                  echo "</tr>";
               }
               echo '</tbody></table>';
            } else {
               echo "<br>No dependents found!";
            }
            Database::disconnect();
          ?>
      </div>
    </div> <!-- /container -->
  </body>
</html>