<div class="modal fade" id="delModal<?php echo $dependent_pID; ?>" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Delete Dependent</h4>
      </div>
      
      <div class="modal-body">
          <p>Are you sure want to Delete this Dependent</p>
      </div>

      <div class="modal-footer">
          <form action="" method="post">
            <input type="hidden" name="dependent_pID" value="<?php echo $dependent_pID; ?>">
            <button type="submit" class="btn btn-danger" name="delete-dep">Delete</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </form>
      </div>
    </div>
  </div>
</div>