<div class="modal fade" id="delModal<?php echo $treatmentId; ?>" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Delete Treatment</h4>
      </div>
      
      <div class="modal-body">
          <p>Are you sure want to Delete this Treatment</p>
      </div>

      <div class="modal-footer">
          <form action="" method="post">
            <input type="hidden" name="treatmentId" value="<?php echo $treatmentId; ?>">
            <button type="submit" class="btn btn-danger" name="delete-treatment">Delete</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </form>
      </div>
    </div>
  </div>
</div>