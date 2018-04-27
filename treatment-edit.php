<div class="modal fade" id="myModal<?php echo $treatmentId; ?>" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Edit Treatment</h4>
      </div>

      <form action="" method="post">
        <div class="modal-body">
            <div class="form-group">
              <label class="control-label">Description:</label>
              <input type="text" name="description" value="<?php echo $description; ?>" class="form-control" required>
            </div>
            <input type="hidden" name="treatmentId" value="<?php echo $treatmentId; ?>">
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary" name="update-treatment">Submit</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </form>
      
    </div>
  </div>
</div>