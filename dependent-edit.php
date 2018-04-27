<div class="modal fade" id="myModal<?php echo $dependent_pID; ?>" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Edit Dependent</h4>
      </div>

      <form action="" method="post">
        <div class="modal-body">
          <input type="hidden" name="dependent_pID" value="<?php echo $dependent_pID; ?>">
          <div class="form-group">
              <label for="name">Name:</label>
              <input type="text" name="name" class="form-control" required value="<?php echo $name; ?>">
          </div>
          <div class="form-group">
              <label for="email">Email:</label>
              <input type="email" name="email" class="form-control" required value="<?php echo $email; ?>">
          </div>
          <div class="form-group">
              <label for="dob">Date of Birth:</label>
              <input type="date" name="dob" class="form-control" required value="<?php echo $dob; ?>">
          </div>
          <div class="form-group">
              <label for="gender">Gender:</label>
              <input type="text" name="gender" class="form-control" required value="<?php echo $gender; ?>">
          </div>
          <div class="form-group">
              <label for="phone">Phone:</label>
              <input type="text" name="phone" class="form-control" required value="<?php echo $phone; ?>">
          </div>
        </div>
        <div class="modal-footer">
           <button type="submit" class="btn btn-primary" name="update-dep">Submit</button>
           <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </form>
  </div>
</div>