<?php
  session_start();
  // Get SESSION pID if is set after user log in
  if ( isset($_SESSION["pID"]) ) { 
     $GLOBALS["pID"] = $_SESSION["pID"];
  }
  if ( isset($_SESSION["treatmentID"]) ) { 
     $GLOBALS["treatmentID"] = $_SESSION["treatmentID"];
  }

  require 'database.php';
  $pdo = Database::connect();
  
// ***** SELECT PATIENT-TREATMENT name, description ***** 
function selectTreatmentPatientById($pdo) {
  try {
    $sql="SELECT p.name, t.description".
         " FROM Patient p, Treatment2 t".
         " WHERE p.pid = t.pid AND t.treatmentID=:treatmentID AND p.pid=:pID";
    $stmt = $pdo->prepare($sql);
    
    $stmt->bindValue(':pID'        , $GLOBALS["pID"]        , PDO::PARAM_INT);  
    $stmt->bindValue(':treatmentID', $GLOBALS["treatmentID"], PDO::PARAM_INT);  
    
    $stmt->execute();
    if($stmt === false) {
       return "";
    }

    $rowCount = $stmt->rowCount();
    if ( $rowCount == 0 ) {
       echo "Argment pID AND treatmentID not found!";
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
  
// ***** SELECT DENTIST ***** 
function selectDentist($pdo) {
  try {
    $sql="SELECT dentistID, name FROM Dentist";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    return $stmt;
  } catch (PDOException $ex) {
    echo  $ex->getMessage();
    return false;
  }
}
  
// ***** SELECT PATIENT-VS-TREATMENT's APPOINTMENTS ***** 
function selectTreatmentPatient($pdo) {
  try {
    $sql="SELECT a.apptID, a.apptDate, a.duration, d.name as Doctor, t.description".
         " FROM Patient p, Appointment2 a, Dentist d, Treatment2 t".
         " WHERE p.pid = a.pid AND d.dentistID=a.dentistID AND a.treatmentID=t.treatmentID".
         " AND t.treatmentID=:treatmentID AND p.pid=:pID";
    $stmt = $pdo->prepare($sql);
    
    $stmt->bindValue(':pID'        , $GLOBALS["pID"]        , PDO::PARAM_INT);  
    $stmt->bindValue(':treatmentID', $GLOBALS["treatmentID"], PDO::PARAM_INT);  
    
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    return $stmt;
  } catch (PDOException $ex) {
    echo  $ex->getMessage();
    return false;
  }
}

// ***** Main *****
  
// ***** INSERT APPOINTMENT for patient ***** 
if (isset($_POST['create-appt'])) {
  try {
    $sql="INSERT INTO Appointment2 (ApptDate, Duration, pID, dentistID, TreatmentID) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
     $stmt->execute(array($_POST['apptDate'], $_POST['duration'], $GLOBALS["pID"], $_POST['dentistID'], $GLOBALS["treatmentID"]));
  } catch (PDOException $ex) {
    echo  $ex->getMessage();
  }
}

// ***** UPDATE APPOINTMENT for patient ***** 
if (isset($_POST['update-appt'])) {
  try {
    $sql="UPDATE Appointment2 SET ApptDate=?, Duration=?, DentistID=? WHERE apptID=?";
    $stmt = $pdo->prepare($sql);
     $stmt->execute(array($_POST['apptDate'], $_POST['duration'], $_POST['dentistID'], $_POST["apptId"]));
  } catch (PDOException $ex) {
    echo  $ex->getMessage();
  }
}

// ***** DELETE APPOINTMENT for patient ***** 
if (isset($_POST['delete-appt'])) {
  try {
    $sql="DELETE FROM Appointment2 WHERE apptID=".$_POST['apptId'];
    $stmt = $pdo->prepare($sql);    
    $stmt->execute();
  } catch (PDOException $ex) {
    echo  $ex->getMessage();
    return false;
  }
}


if ($_SERVER["REQUEST_METHOD"] == "GET") {
   // Receives TREATMENTID from POST IF it is the first visit from different page and pID is set!
   if (basename($_SERVER["HTTP_REFERER"]) != basename($_SERVER["SCRIPT_FILENAME"])) {
      $GLOBALS["treatmentID"] = trim($_GET["treatmentID"]);
      $_SESSION["treatmentID"] = $GLOBALS["treatmentID"];
      $row = selectTreatmentPatientById($pdo);
      if ( !empty($row) ) {
         $name        = $row['name'];
         $description = $row['description'];
      } else {
         echo "Argment pID AND treatmentID not found!";
         die;
      }
   } 
} else {
  if ( !isset($GLOBALS["pID"]) or !isset($GLOBALS["treatmentID"])) {
     echo("You MUST login to create appointment!");
     die;
  }
}
  
$stmtDent = selectDentist($pdo);
$rowCountDent = $stmtDent->rowCount();
  
$stmt = selectTreatmentPatient($pdo);
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
          <fieldset class="fsStyle"><legend class="legendStyle">Treatment Info</legend>
            <form class="form-horizontal" action="customer-account.php" method="post">
              <input type="hidden" class="form-control" name="pID" readonly value="<?php echo !empty($pID)?$pID:'';?>">
              <div class="form-group">
                <label class="control-label col-sm-2" for="name">Patient:</label>
                <div class="col-sm-5"> 
                  <input type="text" class="form-control" name="name" readonly value="<?php echo !empty($name)?$name:'';?>">
                </div>
              </div>
              <div class="form-group">
                <label class="control-label col-sm-2" for="description">Description:</label>
                <div class="col-sm-5"> 
                  <input type="text" class="form-control" name="description" readonly value="<?php echo !empty($description)?$description:'';?>">
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
      
      <!-- CRUD APPOINTMENT BEGIN -->
      <div class="row"><p>
      <fieldset class="fsStyle"><legend class="legendStyle">Patient's Appointments</legend>
      <button data-toggle="collapse" data-target="#demo" class="btn btn-default">+ New Appointment</button>
      <div id="demo" class="collapse" style="margin-top: 10px;">
        <form class="form-horizontal" action="" method="post">
          <div class="form-group">
            <label class="control-label col-sm-2">Dentist:</label>
            <div class="col-sm-5">
              <select class="form-control" name="dentistID">
              <?php
                 unset($arrDent);
                 foreach($stmtDent->fetchAll() as $rowDent) {      
                    echo "<option value=".$rowDent["dentistID"].">".$rowDent["name"]."</option>";
                    $arrDent[$rowDent["dentistID"]] = $rowDent["name"];
                 }
              ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-sm-2">Date:</label>
            <div class="col-sm-3">
              <input type="datetime-local" name="apptDate" class="form-control" required>
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-sm-2">Duration:</label>
            <div class="col-sm-2">
             <select class="form-control" name="duration">
               <option value="30" selected>30 minutes</option>
               <option value="60">60 minutes</option>
               <option value="90">90 minutes</option>
             </select>
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-offset-2 col-sm-2">
              <input type="submit" name="create-appt" value="Create" class="btn btn-primary">
            </div>
          </div>
        </form>
      </div>
      </p></fieldset></div>
        
      <div class="row">
          <?php
            if ($rowCount > 0) {
               echo '<table class="table table-striped table-bordered">
                     <thead><tr>
                     <th>When</th>
                     <th>Duration</th>
                     <th>Dentist</th>
                     <th>Action</th>
                     </tr></thead>';
              
               // output data of each row
               echo '<tbody>';
               foreach($stmt->fetchAll() as $row) {
                  $apptID = $row["apptID"];
                  $apptDate = $row["apptDate"];
                  $duration = $row["duration"];
                  echo "<tr>
                        <td class='border-class'>".$row["apptDate"]."</td>
                        <td class='border-class'>".$row["duration"]."</td>
                        <td class='border-class'>".$row["Doctor"]."</td>
                        <td><a class='btn btn-info btn-sm' data-toggle='modal' data-target='#myModal$apptID'>Edit</a> 
                            <a class='btn btn-danger btn-sm' data-toggle='modal' data-target='#delModal$apptID'>Delete</a></td>";

                  include 'appointment-edit.php';
                  include 'appointment-delete.php';
                  echo "</tr>";
               }
               echo '</tbody></table>';
            } else {
               echo "<br>No appointments found!";
            }
            Database::disconnect();
          ?>
      </div>
    </div> <!-- /container -->
  </body>
</html>