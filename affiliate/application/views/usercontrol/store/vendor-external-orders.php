<!-- Card Component -->
<div class="card">
  
  <!-- Card Header -->
  <div class="card-header">
    <div class="row gx-3">
      <!-- Status Filter Column -->
      <div class="col-sm-4">
        <div class="form-group mb-3">
          <label class="form-label" for="filterStatus"><?= __('user.status') ?></label>
          <select id="filterStatus" class="form-select filter_status">
            <option value=""><?= __('user.all'); ?></option>
            <?php foreach ($status as $key => $value) { ?>
              <option value="<?= $key ?>">
                <?= 
                  match($value) {
                    'Received' => __('user.received'),
                    'Complete' => __('user.complete'),
                    'Total not match' => __('user.total_not_match'),
                    'Denied' => __('user.denied'),
                    'Expired' => __('user.expired'),
                    'Failed' => __('user.failed'),
                    'Processed' => __('user.processed'),
                    'Refunded' => __('user.refunded'),
                    'Reversed' => __('user.reversed'),
                    'Voided' => __('user.voided'),
                    'Canceled Reversal' => __('user.cancel_reversal'),
                    'Waiting For Payment' => __('user.waiting_for_payment'),
                    'Pending' => __('user.pending'),
                    default => $value
                  }
                ?>
              </option>
            <?php } ?>
          </select>
        </div>
      </div>

      <!-- Button Column -->
      <div class="col-sm-4">
        <div class="form-group mb-3">
          <label class="form-label d-block">&nbsp;</label>
          <button class="btn btn-primary" onclick="getPage(1, this)"><?= __('user.search') ?></button>
        </div>
      </div>
    </div>
  </div>

  <!-- Card Body -->
  <div class="card-body">
    <div class="table-responsive">
      <!-- Empty Placeholder -->
      <section class="empty-div d-none">
				<div class="text-center mt-5">
					<div class="d-flex justify-content-center align-items-center flex-column mt-5">
						 <i class="fas fa-exchange-alt fa-5x text-muted"></i>
						 <h3 class="text-muted"><?= __('admin.no_data_found') ?></h3>
					</div>
				</div>
      </section>
      
      <!-- Orders Table -->
      <table class="table table-striped table-hover orders-table">
        <thead>
          <tr>
            <th scope="col" style="width: 80px;">#</th>
            <th scope="col" style="width: 80px;"><?= __('user.order_id') ?></th>
            <th scope="col"><?= __('user.total') ?></th>
            <th scope="col"><?= __('user.country') ?></th>
            <th scope="col"><?= __('user.store') ?></th>
            <th scope="col"><?= __('user.status') ?></th>
            <th scope="col"><?= __('user.commission') ?></th>
            <th scope="col"><?= __('user.commission_status') ?></th>
            <th scope="col"><?= __('user.date') ?></th>
          </tr>
        </thead>
        <tbody>
          <!-- Data will be populated here -->
        </tbody>
      </table>
    </div>
  </div>

  <!-- Card Footer -->
  <div class="card-footer d-none justify-content-end">
    <div class="pagination"></div>
  </div>

</div>
<!-- End of Card Component -->

<script type="text/javascript">
	$(document).on("click", ".toggle-child-tr", function() {
	    // Find the parent row
	    const $tr = $(this).closest("tr");
	    
	    // Find the next detail row
	    const $ntr = $tr.next("tr.detail-tr");

	    // Define icon classes
	    const plusIconClass = "bi bi-plus-circle";
	    const minusIconClass = "bi bi-dash-circle";

	    // Toggle the detail row and icons
	    if ($ntr.is(":visible")) {
	        $ntr.hide();
	        $(this).find("i").attr("class", plusIconClass);
	    } else {
	        $(this).find("i").attr("class", minusIconClass);
	        $ntr.show();
	    }
	});

	function getPage(page,t) {
		$this = $(t);
		var data ={
			page:page, 
			filter_status:$(".filter_status").val()
		}
		$.ajax({
		    url: '<?= base_url("usercontrol/external_vendor_orders") ?>/' + page,
		    type: 'POST',
		    dataType: 'json',
		    data: data,
		    beforeSend: function() {
		        $this.btn("loading");
		    },
		    complete: function() {
		        $this.btn("reset");
		    },
		    success: function(json) {
		        // Reset to hide both sections first
		        $(".empty-div").addClass("d-none");
		        $(".orders-table").hide();
		        
		        // Show either the orders-table or the empty-div based on whether data exists
		        if (json['html']) {
		            $(".orders-table tbody").html(json['html']);
		            $(".orders-table").show();
		        } else {
		            $(".empty-div").removeClass("d-none");
		        }
		        
		        // Handle pagination
		        $(".card-footer").hide();
		        if (json['pagination']) {
		            $(".card-footer").show();
		            $(".card-footer .pagination").html(json['pagination'])
		        }
		    },
		});
	}

	// Attach a click event listener to <a> tags in .pagination inside .card-footer
	$(".card-footer .pagination").delegate("a", "click", function(e) {
	    e.preventDefault();  // Prevent the default behavior of <a> (navigation)
	    
	    // Get the 'data-ci-pagination-page' attribute of the clicked <a> tag
	    const paginationPage = $(this).attr("data-ci-pagination-page");
	    
	    // Call the 'getPage()' function with the page number and jQuery object of clicked <a>
	    getPage(paginationPage, $(this));
	});


	getPage(1)
</script>