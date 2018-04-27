<?php
session_start();
// Get SESSION pID if is set after user log in
if ( isset($_SESSION["dentistID"]) ) { 
   $GLOBALS["dentistID"] = $_SESSION["dentistID"];
}

require 'database.php';
$pdo = Database::connect();

// ***** SELECT Dentist ***** 
function selectDentist($pdo) {
  try {
    $sql="SELECT name FROM Dentist WHERE dentistID = :dentistID";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':dentistID', $GLOBALS["dentistID"], PDO::PARAM_INT);  
    
    $stmt->execute();
    if($stmt === false) {
       return "";
    }

    $rowCount = $stmt->rowCount();
    if ( $rowCount == 0 ) {
       echo "Argment dentistID not found!";
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

// ***** VIEW Dentist Schedule ***** 
function selectAppointment($pdo) {
  try {
    $sql="SELECT a.apptID, p.pID, a.treatmentID, a.apptDate, a.duration, p.name, t.description"
        ." FROM Appointment2 a, Patient p, Treatment2 t"
        ." WHERE p.PID = a.PID"
        ." AND a.treatmentID = t.treatmentID"
        ." AND a.dentistID = :dentistID"
        ." ORDER BY a.apptDate";
    $stmt = $pdo->prepare($sql);
    
    $stmt->bindValue(':dentistID', $GLOBALS["dentistID"], PDO::PARAM_INT);  
    
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    return $stmt;
  } catch (PDOException $ex) {
    echo  $ex->getMessage();
    return false;
  }
}

// ***** VIEW ALL Patients ***** 
function selectAllPatients($pdo) {
  try {
    $sql="SELECT pID, name, dob, phone_no, principal_pID FROM Patient";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    return $stmt;
  } catch (PDOException $ex) {
    echo  $ex->getMessage();
    return false;
  }
}

// ***** Main *****
if (isset($GLOBALS["dentistID"])) {
   $row = selectDentist($pdo);
   if ( !empty($row) ) {
      $name    = $row['name'];
   }
}

$stmtAllPat = selectAllPatients($pdo);
$rowCountAllPat = $stmtAllPat ->rowCount();

$stmt = selectAppointment($pdo);
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
          <fieldset class="fsStyle"><legend class="legendStyle">Welcome</legend>
             <form class="form-horizontal" action="" method="post">
              <input type="hidden" class="form-control" name="dentistID" readonly value="<?php echo !empty($dentistID)?$dentistID:'';?>">
              <div class="form-group">
                <label class="control-label col-sm-2" for="name">Dentist:</label>
                <div class="col-sm-5"> 
                  <input type="text" class="form-control" name="name" readonly value="<?php echo !empty($name)?$name:'';?>">
                </div>
              </div>

              <div class="form-group"> 
                <div class="col-sm-offset-2 col-sm-5">
                  <a href="login-dentist.php" class="btn btn-default" role="button">Logout</a>
                </div>
              </div>
             </form>
          </fieldset>
          </p>
      </div>


      <!-- View Appointments -->
      <div class="row"><p>
      <fieldset class="fsStyle"><legend class="legendStyle">All Patients List</legend>
      
      <div class='row'>
          <?php
            if ($rowCountAllPat > 0) {
               echo '<table class="table table-striped table-bordered">
                     <thead><tr>
                     <th>Name</th>
                     <th>DOB</th>
                     <th>Phone</th>
                     <th>Action</th></tr></thead>';
              
               // output data of each row
               echo '<tbody>';
               foreach($stmtAllPat ->fetchAll() as $rowPat) {
                  $ppId = $rowPat["pID"];
                  $pname = $rowPat["name"];
                  $pdob = $rowPat["dob"];
                  $pphone = $rowPat["phone_no"];
                  echo "<tr>
                        <td class='border-class'>$pname</td>
                        <td class='border-class'>$pdob</td>
                        <td class='border-class'>$pphone</td>
                        <td><a href='customer.php?pID=$ppId' class='btn btn-default' role='button'>View Patient</a>
                            <a href='customer-invoice.php?pID=$ppId' class='btn btn-default' role='button'>Patient's invoice</a></td>";
                  echo "</tr>";
               }
               echo '</tbody></table>';
            } else {
               echo "<br>No Patients found!";
            }
          ?>
      </div>

      <!-- View Appointments -->
      <div class="row"><p>
      <fieldset class="fsStyle"><legend class="legendStyle">Dentist's Appointments</legend>
      
      <div class='row'>
          <?php
            if ($rowCount > 0) {
               echo '<table class="table table-striped table-bordered">
                     <thead><tr>
                     <th>When</th>
                     <th>Duration</th>
                     <th>Patient</th>
                     <th>Treatment</th>
                     <th>Action</th></tr></thead>';
              
               // output data of each row
               echo '<tbody>';
               foreach($stmt->fetchAll() as $row) {
                  $pId = $row["pID"];
                  $apptId = $row["apptID"];
                  $treatmentId = $row["treatmentID"];
                  $apptDate = $row["apptDate"];
                  $duration = $row["duration"];
                  $patName = $row["name"];
                  $description = $row["description"];
                  echo "<tr>
                        <td class='border-class'>$apptDate</td>
                        <td class='border-class'>$duration</td>
                        <td class='border-class'>$patName</td>
                        <td class='border-class'>$description</td>
                        <td><a href='customer.php?pID=$pId' class='btn btn-default' role='button'>View Patient</a>
                            <a href='customer-invoice.php?pID=$pId' class='btn btn-default' role='button'>Patient's invoice</a></td>";
                  echo "</tr>";
               }
               echo '</tbody></table>';
            } else {
               echo "<br>No Appointments found!";
            }
            Database::disconnect();
          ?>
      </div>
    </div> <!-- /container -->
  </body>
</html>