<div class="modal fade" id="myModal<?php echo $apptID; ?>" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Edit Appointment</h4>
      </div>

      <form action="" method="post">
        <div class="modal-body">
          <input type="hidden" name="apptId" value="<?php echo $apptID; ?>">
          <div class="form-group">
              <label for="dentistID">Dentist:</label>
              <select class="form-control" name="dentistID">
              <?php
                 foreach($arrDent as $key => $value) {
                    echo "<option value=".$key.">".$value."</option>";
                 }
              ?>
              </select>
          </div>
          <div class="form-group">
              <label for="apptDate">When:</label>
              <input type="datetime-local" name="apptDate" class="form-control" required>
          </div>
          <div class="form-group">
              <label for="duration">Duration:</label>
              <select class="form-control" name="duration">
                <option value="30" selected>30 minutes</option>
                <option value="60">60 minutes</option>
                <option value="90">90 minutes</option>
              </select>
          </div>
          <div class="modal-footer">
              <button type="submit" class="btn btn-primary" name="update-appt">Submit</button>
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
      </form>   
  </div>
</div>