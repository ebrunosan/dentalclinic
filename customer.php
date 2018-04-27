<?php
session_start();
// Get SESSION pID if is set after user log in
if ( isset($_GET['pID']) and isset($_SESSION["dentistID"]) ) {
   $_SESSION["pID"] = $_GET["pID"];
   $GLOBALS["pID"] = $_SESSION["pID"];
} else {
   if ( isset($_SESSION["pID"]) ) { 
      $GLOBALS["pID"] = $_SESSION["pID"];
   }
}


require 'database.php';
$pdo = Database::connect();

// ***** SELECT PATIENT ***** 
function selectPatient($pdo) {
  try {
    $sql="SELECT name FROM Patient WHERE pID = :pID";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':pID', $GLOBALS["pID"], PDO::PARAM_INT);  
    
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

// ***** SELECT TREATMENT for patient ***** 
function selectTreatment($pdo) {
  try {    
    $sql="SELECT treatmentID, description FROM Treatment2 WHERE pID = :pID";
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
  
// ***** INSERT TREATMENT for patient ***** 
if (isset($_POST['create-treatment'])) {
  try {
    $sql="INSERT INTO Treatment2 (description, pID) VALUES (?, ?)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($_POST['description'], $GLOBALS["pID"]));
  } catch (PDOException $ex) {
    echo  $ex->getMessage();
  }
}

// ***** UPDATE TREATMENT for patient ***** 
if (isset($_POST['update-treatment'])) {
  try {
    $sql="UPDATE Treatment2 SET description=:description WHERE pID=:pID AND treatmentID=:treatmentID";
    $stmt = $pdo->prepare($sql);
    
    $stmt->bindValue(':pID'        , $GLOBALS['pID']       , PDO::PARAM_INT);  
    $stmt->bindValue(':treatmentID', $_POST['treatmentId'] , PDO::PARAM_INT);  
    $stmt->bindValue(':description', $_POST['description'] , PDO::PARAM_STR);  
    
    $stmt->execute();
  } catch (PDOException $ex) {
    echo  $ex->getMessage();
    return false;
  }
}

// ***** DELETE TREATMENT for patient ***** 
if (isset($_POST['delete-treatment'])) {
  try {
    $sql="DELETE FROM Treatment2 WHERE pID=".$GLOBALS['pID']." AND treatmentID=".$_POST['treatmentId'];
    $stmt = $pdo->prepare($sql);    
    $stmt->execute();
  } catch (PDOException $ex) {
    echo  $ex->getMessage();
    return false;
  }
}

// ***** Main *****
if (isset($GLOBALS["pID"])) {
   $row = selectPatient($pdo);
   if ( !empty($row) ) {
      $name    = $row['name'];
   }
}

$stmt = selectTreatment($pdo);
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
             <form class="form-horizontal" action="customer-account.php" method="post">
              <input type="hidden" class="form-control" name="pID" readonly value="<?php echo !empty($pID)?$pID:'';?>">
              <div class="form-group">
                <label class="control-label col-sm-2" for="name">Patient:</label>
                <div class="col-sm-7"> 
                  <input type="text" class="form-control" name="name" readonly value="<?php echo !empty($name)?$name:'';?>">
                </div>
              </div>

              <div class="form-group"> 
                <div class="col-sm-offset-2 col-sm-7">
                  <button type="submit" value="my-account" name="my-account" class="btn btn-default">Patient's Data</button>
                  <a href="customer-address.php" class="btn btn-default" role="button">Address</a>
                  <a href="customer-dependent.php" class="btn btn-default" role="button">Dependents</a>
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

      <!-- CRUD TREATMENT BEGIN -->
      <div class="row"><p>
      <fieldset class="fsStyle"><legend class="legendStyle">Patient's Treatments</legend>
      <button data-toggle="collapse" data-target="#demo" class="btn btn-default">+ New Treatment</button>
      <div id="demo" class="collapse" style="margin-top: 10px;">
        <form action="" method="post">
          <div class="form-group">
            <label class="control-label">Description:</label>
            <input type="text" name="description" placeholder="Enter treatment description" class="form-control" required>
          </div>
          <div class="form-group">
            <input type="submit" name="create-treatment" value="Create" class="btn btn-primary">
          </div>
        </form>
      </div>
      </p></fieldset></div>

      <div class='row'>
          <?php
            if ($rowCount > 0) {
               echo '<table class="table table-striped table-bordered">
                     <thead><tr>
                     <th>Treatment Description</th>
                     <th>Action</th></tr></thead>';
              
               // output data of each row
               echo '<tbody>';
               foreach($stmt->fetchAll() as $row) {
                  $treatmentId = $row["treatmentID"];
                  $description = $row["description"];
                  echo "<tr>
                        <td class='border-class'>$description</td>
                        <td><a href='customer-appt-list.php?treatmentID=$treatmentId' class='btn btn-default' role='button'>View Appointments</a>
                            <a class='btn btn-info btn-sm' data-toggle='modal' data-target='#myModal$treatmentId'>Edit</a> 
                            <a data-toggle='modal' data-target='#delModal$treatmentId' class='btn btn-danger btn-sm'>Delete</a></td>";

                  include 'treatment-edit.php';
                  include 'treatment-delete.php';
                  echo "</tr>";
               }
               echo '</tbody></table>';
            } else {
               echo "<br>No treatment found!";
            }
            Database::disconnect();
          ?>
      </div>
      <!-- CRUD TREATMENT END -->
    </div> <!-- /container -->
  </body>
</html>