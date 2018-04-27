<div class="modal fade" id="delModal<?php echo $apptID; ?>" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Delete Appointment</h4>
      </div>
      
      <div class="modal-body">
          <p>Are you sure want to Delete this Appointment - <?php echo $apptID; ?></p>
      </div>

      <div class="modal-footer">
          <form action="" method="post">
            <input type="hidden" name="apptId" value="<?php echo $apptID; ?>">
            <button type="submit" class="btn btn-danger" name="delete-appt">Delete</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </form>
      </div>
    </div>
  </div>
</div>