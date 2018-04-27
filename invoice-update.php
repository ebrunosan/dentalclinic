<div class="modal fade" id="myModal<?php echo $invoiceID; ?>" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Pay off Invoice #<?php echo $invoiceID." - CAD$ ".$amount; ?></h4>
      </div>

      <form action="" method="post">
        <div class="modal-body">
          <div class="form-group">
            <label for="date_paid">Date:</label>
            <input type="date" class="form-control" name="date_paid">
          </div>
          <div class="form-group">
            <label for="payment_method">Method:</label>
             <select class="form-control" name="payment_method">
               <option value="Cash" selected>Cash</option>
               <option value="Mastercard">Mastercard</option>
               <option value="Visa">Visa</option>
               <option value="Insurance">Insurance</option>
             </select>
          </div>
          <input type="hidden" name="invoiceID" value="<?php echo $invoiceID; ?>">
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary" name="update-invoice">Submit</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>