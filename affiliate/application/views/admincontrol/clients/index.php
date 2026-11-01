<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header bg-secondary text-white">
        <div class="d-flex justify-content-between align-items-center">
          <h5><?= __('admin.store_clients') ?></h5>
          <div>
            <a id="toggle-uploader" class="btn btn-light" href="<?php echo base_url("admincontrol/addclients") ?>">
              <?= __('admin.add_client') ?></a>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="row top-panel">
          <div class="col-sm-2">
            <div class="share-store-list">
            </div>
          </div>
        </div>
        <br>
        <div class="table-responsive">
          <section class="empty-div d-none">
            <div class="d-flex justify-content-center align-items-center flex-column mt-5">
                <i class="fas fa-exchange-alt fa-5x text-muted"></i>
                <h3 class="text-muted"><?= __('admin.no_data_found') ?></h3>
            </div>
          </section>
          <table id="clients-table" class="table table-striped">
            <thead>
              <tr>
                <th><?= __('admin.id') ?></th>
                <th><?= __('admin.name') ?></th>
                <th><?= __('admin.refer_user') ?></th>
                <th><?= __('admin.email') ?></th>
                <th><?= __('admin.phone') ?></th>
                <th><?= __('admin.username') ?></th>
                <th><?= __('admin.sales') ?></th>
                <th><?= __('admin.type') ?></th>
                <th><?= __('admin.action') ?></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td colspan="9" class="text-center">
                  <h3 class="text-muted py-4"><?= __("admin.loading_clients_data_text") ?></h3>
                  <h5 class="text-muted py-4"><?= __("admin.not_taking_longer") ?></h5>
                </td>
              </tr>
            </tbody>
          </table>
          <div class="card-footer text-end" style="display: none;">
            <div class="pagination"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="ShipingDetailsModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><?= __('admin.shipping_details') ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="frm_shipping_address">
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="address" class="form-label"><?= __('admin.address') ?></label>
                <input type="text" class="form-control" name="address" id="address" placeholder="<?= __('admin.address') ?>" readonly>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="country_id" class="form-label"><?= __('admin.country') ?></label>
                <input type="text" class="form-control" name="country_id" id="country_id" placeholder="<?= __('admin.country') ?>" readonly>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="state_id" class="form-label"><?= __('store.state') ?></label>
                <input type="text" class="form-control" name="state_id" id="state_id" placeholder="<?= __('admin.state') ?>" readonly>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="city" class="form-label"><?= __('store.city') ?></label>
                <input class="form-control" name="city" type="text" id="city" readonly>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="zip_code" class="form-label"><?= __('store.postal_code') ?></label>
                <input class="form-control" name="zip_code" type="text" id="zip_code" readonly>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="phone" class="form-label"><?= __('store.phone') ?></label>
                <input class="form-control" name="phone" type="text" id="phone" readonly>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
    function shareinsocialmedia(url) {
    window.open(url, 'sharein', 'toolbar=0,status=0,width=648,height=395');
    return true;
    }

    $(document).on('click', '.deleteuser', function(e) {
    var deleteaction = $(this).data('url');
    var message = '<?= __('admin.lost_all_data_are_you_sure_delete') ?>';
    Swal.fire({
    icon: 'warning',
    html: message,
    showCancelButton: true,
    confirmButtonColor: "#DD6B55",
    confirmButtonText: '<?= __('admin.yes') ?>',
    cancelButtonText: '<?= __('admin.no') ?>'
    }).then((result) => {
    if (result.value) window.location.href = deleteaction;
    });
    });

    $(document).on('click', '.viewShipping', function(e) {
    $this = $(this);
    var id = $(this).data('id');
    $.ajax({
    url: '<?= base_url('admincontrol/getShippingDetails') ?>',
    type: 'POST',
    dataType: 'json',
    data: {
      id
    },
    beforeSend: function() {
      $this.prop("disabled", true);
    },
    complete: function() {
      $this.prop("disabled", false);
    },
    success: function(data) {
      if (data.status) {
        $("#frm_shipping_address").trigger('reset');
        $("#address").val(data.data.address);
        $("#country_id").val(data.data.country_name);
        $("#state_id").val(data.data.state_name);
        $("#city").val(data.data.city);
        $("#zip_code").val(data.data.zip_code);
        $("#phone").val(data.data.phone);
        $("#twaddress").val(data.data.twaddress);
        $("#ShipingDetailsModal").modal('show');
      } else {
        Swal.fire({
          icon: 'info',
          text: '<?= __('admin.no_shipping_details_found') ?>',
        })
      }
    },
    });
    });

    function getclientsRows(page, t) {
    $this = $(t);

    $.ajax({
    url: "<?= base_url('admincontrol/listclients'); ?>/" + page,
    type: 'POST',
    dataType: 'json',
    data: {
      listclients: 1,
      page: page
    },
    beforeSend: function() {
      $this.addClass("loading");
    },
    complete: function() {
      $this.removeClass("loading");
    },
    success: function(json) {
      if (json['html']) {
        $("#clients-table tbody").html(json['html']);
        $("#clients-table").show();
      } else {
        $(".empty-div").removeClass("d-none");
        $("#clients-table").hide();
      }

      $(".card-footer").hide();

      if (json['pagination']) {
        $(".card-footer").show();
        $(".card-footer .pagination").html(json['pagination'])
      }
    },
    })
    }

    $(".card-footer .pagination").on("click", "a", function(e) {
    e.preventDefault();
    getclientsRows($(this).attr("data-ci-pagination-page"), $(this));
    });

    $(function() {
    getclientsRows(1, $(this));
    });
</script>