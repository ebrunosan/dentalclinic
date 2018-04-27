<?php
  require 'database.php';
  $pdo = Database::connect();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script defer src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>

<?php
// ***** SELECT PATIENT W/ NO ARGS ***** 
function selectPatient($pdo) {
  $sql="SELECT * FROM Patient";
  $stmt = $pdo->prepare($sql);
  $stmt->execute();
  if($stmt === false) {
     trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $pdo->errno . ' ' . $pdo->error, E_USER_ERROR);
  }
  $stmt->setFetchMode(PDO::FETCH_ASSOC);
  return $stmt;
}

$stmt = selectPatient($pdo);
$rowCount = $stmt->rowCount();
?>

<body>
    <div class="container">
        <div class="row">
            <h3>Patients</h3>
        </div> <!-- /row -->
 
        <div class="row">
          <?php
            if ($rowCount > 0) {
               echo '<table class="table table-striped table-bordered">';
               echo '<thead>';
               echo '<tr>';
               echo '<th>PID</th>';
               echo '<th>Name</th>';
               echo '<th>Address</th>';
               echo '</tr>';
               echo '</thead>';
              
               // output data of each row
               echo '<tbody>';
               $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
               $rowCount = $stmt->rowCount();
               foreach($stmt->fetchAll() as $row) {      
                  echo "<tr>
                        <td class='border-class'>".$row["pID"]."</td>
                        <td class='border-class'>".$row["name"]."</td>
                        <td class='border-class'>".$row["pAddress"]."</td>
                        </tr>";
               }
               echo '</tbody>';
               echo '</table>';
            } else {
               echo "0 results";
            }
            Database::disconnect();
          ?>
        </div> <!-- /row -->
    </div> <!-- /container -->  
  </body>
</html>