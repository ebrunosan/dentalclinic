<?php
session_start();
// Get SESSION pID if is set after user log in
if ( isset($_GET['pID']) and isset($_SESSION["dentistID"]) ) {
   $_SESSION["pID"] = $_GET["pID"];
   $GLOBALS["pID"] = $_SESSION["pID"];
} else {
  echo "You should be logged as a Dentist!";
  die;
}

require 'database.php';
$pdo = Database::connect();

// ***** SELECT Patient's AND Dependent's Appointments W/O invoice ***** 
function selectApptNoInvoice($pdo) {
  try {
    $sql="SELECT p.name as patName, dep.name as depName, a.apptID, a.apptDate, a.duration, d.name denName"
     ." FROM Dentist d, Appointment2 a, Patient p"
     ." LEFT OUTER JOIN Patient dep ON p.principal_pID = dep.pID"
     ." WHERE d.dentistID=a.dentistID AND a.invoiceID IS NULL AND a.pID=:pID"
     ." AND a.PID=p.pID ORDER BY apptDate";
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

// ***** SELECT Invoice to be PAID ***** 
function selectInvoiceTobePaid($pdo) {
  try {
    $sql="SELECT i.invoiceID, i.amount, count(a.apptID) as qtAppt"
      ." FROM Appointment2 a, Invoice2 i"
      ." WHERE a.invoiceID=i.invoiceID AND a.pID=:pID AND date_paid IS NULL"
      ." GROUP BY i.invoiceID";
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

// ***** SELECT PAID Invoice  ***** 
function selectPaidInvoice($pdo) {
  try {
    $sql="SELECT i.invoiceID, i.amount, i.date_paid, i.payment_method"
      ." FROM Appointment2 a, Invoice2 i"
      ." WHERE a.invoiceID=i.invoiceID AND a.pID=:pID AND date_paid IS NOT NULL"
      ." GROUP BY i.invoiceID";
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
// ***** INSERT APPOINTMENT for patient ***** 
if (isset($_POST['create-invoice'])) {
  if (count($_POST["arrNapptID"]) == 0 ) {
     echo "You must select at least ONE appointment to create an invoice!";
  } else {
    try {
      $sql="INSERT INTO Invoice2 (amount) VALUES (?)";
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array($_POST['amount']));
    
      $invoiceID = $pdo->lastInsertId();
      $allAppt = implode(",", $_POST["arrNapptID"]);
    
      $sql="UPDATE Appointment2 SET invoiceID=$invoiceID WHERE apptID IN ($allAppt)";
      $stmt = $pdo->prepare($sql);
      $stmt->execute();
    } catch (PDOException $ex) {
      echo  $ex->getMessage();
    }
  }
}

// ***** UPDATE INVOICE selected (MODAL) ***** 
if (isset($_POST['update-invoice'])) {
  try {
    $sql="UPDATE Invoice2 SET date_paid=:date_paid, payment_method=:payment_method WHERE invoiceID=:invoiceID";
    $stmt = $pdo->prepare($sql);
    
    $stmt->bindValue(':date_paid'     , $_POST['date_paid']      , PDO::PARAM_STR);  
    $stmt->bindValue(':payment_method', $_POST['payment_method'] , PDO::PARAM_STR);  
    $stmt->bindValue(':invoiceID'     , $_POST['invoiceID']      , PDO::PARAM_INT);  
    
    $stmt->execute();
  } catch (PDOException $ex) {
    echo  $ex->getMessage();
    return false;
  }
}


$pId = $GLOBALS["pID"];

$stmtApptNoInvoice = selectApptNoInvoice($pdo);
$rowApptNoInvoice = $stmtApptNoInvoice ->rowCount();

$stmtInvToPay = selectInvoiceTobePaid($pdo);
$rowInvToPay = $stmtInvToPay->rowCount();

$stmtPaidInv = selectPaidInvoice($pdo);
$rowPaidInv = $stmtPaidInv->rowCount();
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
      <!-- View Appointments W/O Invoice-->
      <div class="row"><p>
      <fieldset class="fsStyle"><legend class="legendStyle">Patient's and Dependent's Appointments Without Invoice</legend>
      
      <!-- CREATE INVOICE BEGIN -->
      <div class="row"><p>
      <?php
      if ($rowApptNoInvoice > 0) {
      ?>
        <button data-toggle="collapse" data-target="#demo" class="btn btn-default">+ New Invoice</button>
        <form class="form-horizontal" action="customer-invoice.php?pID=<?php echo $pID; ?>" method="post">
          <div id="demo" class="collapse" style="margin-top: 10px;">
            <div class="form-group">
              <label class="control-label col-sm-2">Amount:</label>
              <div class="col-sm-3">
                <input type="number" name="amount" class="form-control" required>
              </div>
            </div>
            <div class="form-group">
              <div class="col-sm-offset-2 col-sm-2">
                <input type="submit" name="create-invoice" value="Create" class="btn btn-primary">
              </div>
            </div>
          </div>
      <?php
      }
      ?>
          <div class='row'>
          <?php
            if ($rowApptNoInvoice > 0) {
               echo "<table class='table table-striped table-bordered'>
                     <thead><tr>
                     <th>When</th>
                     <th>Duration</th>
                     <th>Patient</th>
                     <th>Dependent</th>
                     <th>Dentist</th>
                     <th>Select appt to create new Invoice</thead>";
              
               // output data of each row
               echo '<tbody>';
               foreach($stmtApptNoInvoice ->fetchAll() as $rowNoInv) {
                  $napptID   = $rowNoInv["apptID"];
                  $napptDate = $rowNoInv["apptDate"];
                  $nduration = $rowNoInv["duration"];
                  $npatName  = $rowNoInv["patName"];
                  $ndepName  = $rowNoInv["depName"];
                  $ndenName  = $rowNoInv["denName"];
                  echo "<tr>
                        <td class='border-class'>$napptDate</td>
                        <td class='border-class'>$nduration</td>
                        <td class='border-class'>$npatName</td>
                        <td class='border-class'>$ndepName</td>
                        <td class='border-class'>$ndenName</td>
                        <td><div class='checkbox'>";
                  echo "<label><input type='checkbox' name='arrNapptID[]' class='checkbox' value='$napptID'/>Select me</label></div></td>";
                  echo "</tr>";
               }
               echo '</tbody></table>';
            } else {
               echo "<br>No Appointments found!";
            }
          ?>
          </div>
      </form>
      </p></div></fieldset></p></div>


      <!-- View Invoice to be payed -->
      <div class="row"><p>
      <fieldset class="fsStyle"><legend class="legendStyle">Patient's Invoices to be payed</legend>
      
      <div class='row'>
          <?php
            if ($rowInvToPay > 0) {
               echo '<table class="table table-striped table-bordered">
                     <thead><tr>
                     <th>Invoice #</th>
                     <th>Amount</th>
                     <th>Qt Appointments</th>
                     <th>Action</th></tr></thead>';
              
               // output data of each row
               echo '<tbody>';
               foreach($stmtInvToPay ->fetchAll() as $row) {
                  $invoiceID = $row["invoiceID"];
                  $amount = $row["amount"];
                  $qtAppt = $row["qtAppt"];
                  echo "<tr>
                        <td class='border-class'>$invoiceID</td>
                        <td class='border-class'>$amount</td>
                        <td class='border-class'>$qtAppt</td>
                        <td><a class='btn btn-info btn-sm' data-toggle='modal' data-target='#myModal$invoiceID'>Pay off</a></td>";
                  include "invoice-update.php";
                  echo "</tr>";
               }
               echo '</tbody></table>';
            } else {
               echo "<br>No invoices to be payed!";
            }
          ?>
      </div></fieldset></p></div>


      <!-- View Invoice to be payed -->
      <div class="row"><p>
      <fieldset class="fsStyle"><legend class="legendStyle">Patient's Paid Invoices</legend>
      
      <div class='row'>
          <?php
            if ($rowPaidInv > 0) {
               echo '<table class="table table-striped table-bordered">
                     <thead><tr>
                     <th>Invoice #</th>
                     <th>Amount</th>
                     <th>Date</th>
                     <th>Method</th></tr></thead>';
              
               // output data of each row
               echo '<tbody>';
               foreach($stmtPaidInv ->fetchAll() as $rowPaid) {
                  $paidInvoiceID = $rowPaid["invoiceID"];
                  $paidAmount    = $rowPaid["amount"];
                  $paidDate      = $rowPaid["date_paid"];
                  $paidMethod    = $rowPaid["payment_method"];
                  echo "<tr>
                        <td class='border-class'>$paidInvoiceID</td>
                        <td class='border-class'>$paidAmount</td>
                        <td class='border-class'>$paidDate</td>
                        <td class='border-class'>$paidMethod</td>";
                  echo "</tr>";
               }
               echo '</tbody></table>';
            } else {
               echo "<br>No invoices payed found!";
            }
            Database::disconnect();
          ?>
      </div></fieldset></p></div>
 
      <a href='dentist-list.php' class='btn btn-default' role='button'>Return Dentist's List</a>
    </div> <!-- /container -->
  </body>
</html>