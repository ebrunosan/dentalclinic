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
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
</head>

<?php
// ***** INSERT ADDRESS ***** 
function insertAddress($pdo, $pID, $pAddress, $pCity, $pPostalCode, $pProvince) {
  try {
    $sql="INSERT INTO PatientAddress (pID, pAddress, pCity, pPostalCode, pProvince) VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($pID, $pAddress, $pCity, $pPostalCode, $pProvince));

    return true;
  } catch (PDOException $ex) {
    echo  $ex->getMessage();
    return false;
  }
}

// ***** UPDATE ADDRESS ***** 
function updateAddress($pdo, $pID, $pAddress, $pCity, $pPostalCode, $pProvince) {
  try {
    $sql="UPDATE PatientAddress SET pAddress=:pAddress, pCity=:pCity, pPostalCode=:pPostalCode, pProvince=:pProvince WHERE pid=:pID";
    $stmt = $pdo->prepare($sql);

    $stmt->bindValue(':pID'         , $pID,         PDO::PARAM_INT);
    $stmt->bindValue(':pAddress'    , $pAddress,    PDO::PARAM_STR);
    $stmt->bindValue(':pCity'       , $pCity,       PDO::PARAM_STR);
    $stmt->bindValue(':pPostalCode' , $pPostalCode, PDO::PARAM_STR);
    $stmt->bindValue(':pProvince'   , $pProvince,   PDO::PARAM_STR);
    
    $stmt->execute();
    return true;
  } catch (PDOException $ex) {
    echo  $ex->getMessage();
    return false;
  }
}

// ***** SELECT ADDRESS ***** 
function selectAddress($pdo) {
  try {
    $sql="SELECT name, pAddress, pCity, pPostalCode, pProvince FROM Patient p, PatientAddress pa WHERE p.pID=pa.PID AND pa.pID = :pID";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':pID'    , $GLOBALS["pID"], PDO::PARAM_INT);  
    
    $stmt->execute();
    if($stmt === false) {
       return "";
    }

    $rowCount = $stmt->rowCount();
    if ( $rowCount == 0 ) {
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

// Select ADDRESS IF is the first visit from different page and pID is set!
if (isset($GLOBALS["pID"]) and basename($_SERVER["HTTP_REFERER"]) != basename($_SERVER["SCRIPT_FILENAME"])) {
   $row= selectAddress($pdo);
   if ( !empty($row) ) {
      $pName       = $row['name'];
      $pAddress    = $row['pAddress'];
      $pCity       = $row['pCity'];
      $pProvince   = $row['pProvince'];
      $pPostalCode = $row['pPostalCode'];
      $GLOBALS["AddressExist"] = true;
   } else {
      $GLOBALS["AddressExist"] = false;
   }
// Do an Insert OR Update AFTER a POST METHOD
} else if ($_SERVER["REQUEST_METHOD"] == "POST") {
   $pID        = trim($_POST['pID']);
   $pName      = trim($_POST['pName']);
   $pAddress   = trim($_POST['pAddress']);
   $pCity      = trim($_POST['pCity']);
   $pProvince  = trim($_POST['pProvince']);
   $pPostalCode= trim($_POST['pPostalCode']);

   if (!empty($_POST['insert-submit'])) {
      if ( insertAddress($pdo, $pID, $pAddress, $pCity, $pPostalCode, $pProvince) ) {
          $GLOBALS["AddressExist"] = true;
          echo "Insert success!";
      }
   
   } else if (!empty($_POST['update-submit'])) {
      if ( updateAddress($pdo, $pID, $pAddress, $pCity, $pPostalCode, $pProvince) ) {
         $GLOBALS["AddressExist"] = true;
         echo "Update success!";
      }
   }
}
?>
  
<body>
    <div class="container">
      <div class="row">
          <p>
          <fieldset class="fsStyle"><legend class="legendStyle">Patient's Address</legend>
             <form class="form-horizontal" action="customer-address.php" method="post">
              <input type="hidden" class="form-control" name="pID" readonly value="<?php echo !empty($pID)?$pID:'';?>">
              <div class="form-group">
                <label class="control-label col-sm-2" for="pName">Patient:</label>
                <div class="col-sm-5"> 
                  <input type="text" class="form-control" name="pName" readonly value="<?php echo !empty($pName)?$pName:'';?>">
                </div>
              </div>
              <div class="form-group">
                <label class="control-label col-sm-2" for="pAddress">Address:</label>
                <div class="col-sm-5"> 
                  <input type="text" class="form-control" name="pAddress" required value="<?php echo !empty($pAddress)?$pAddress:'';?>">
                </div>
              </div>
              <div class="form-group">
                <label class="control-label col-sm-2" for="pCity">City:</label>
                <div class="col-sm-5">
                  <input type="text" class="form-control" name="pCity" required value="<?php echo !empty($pCity)?$pCity:'';?>">
                </div>
              </div>
              <div class="form-group">
                <label class="control-label col-sm-2" for="pProvince">Province:</label>
                <div class="col-sm-1"> 
                  <input type="text" class="form-control" name="pProvince" required value="<?php echo !empty($pProvince)?$pProvince:'';?>">
                </div>
              </div>
              <div class="form-group">
                <label class="control-label col-sm-2" for="pPostalCode">Postal Code:</label>
                <div class="col-sm-3"> 
                  <input type="text" class="form-control" name="pPostalCode" required value="<?php echo !empty($pPostalCode)?$pPostalCode:'';?>">
                </div>
              </div>
              <div class="form-group"> 
                <div class="col-sm-offset-2 col-sm-5">
                  <?php
                    if ( !$GLOBALS["AddressExist"] ) {
                       echo "<button type='submit' value='insert-submit' name='insert-submit' class='btn btn-default'>Create</button>";
                    } else {
                       echo "<button type='submit' value='update-submit' name='update-submit' class='btn btn-default'>Update</button>";
                    }

                    echo"<a href='customer.php' class='btn btn-default' role='button'>Return Patient's Info</a>";
                    
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
    </div> <!-- /container -->
  </body>
</html>
