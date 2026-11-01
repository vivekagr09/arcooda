<div class="card">
    <div class="card-header bg-secondary text-white">
            <h5><?= __('admin.edit_countries_and_states') ?></h5>
        </div>
    <div class="card-body">
        <div class="row">
            <div class="col-sm-12">
                <ul class="nav nav-pills flex-column flex-sm-row" id="TabsNav">
                    <li class="nav-item flex-sm-fill text-sm-center">
                        <a class="nav-link bg-secondary active" data-bs-toggle="tab" href="#countries" role="tab">
                        <?= __('admin.countries') ?></a>
                    </li>
                    <li class="nav-item flex-sm-fill text-sm-center">
                        <a class="nav-link" data-bs-toggle="tab" href="#states" role="tab">
                        <?= __('admin.states') ?></a>
                    </li>
                </ul>
                <hr/>
            </div>
            <div class="col-sm-12">
                <div class="tab-content">
                    <div class="tab-pane active" id="countries" role="tabpanel">
                        <div class="row">
                            <div class="col">
                                <button type="button" class="btn btn-secondary createCountryFormModal">
                                <?= __("admin.create_new_country"); ?>
                                </button>
                                <?php if(empty($countries)) { ?>
                                <div class="alert alert-info p-4 mt-4"><?php echo __("admin.no_new_countries"); ?></div>
                                <?php } else { ?>
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th><?= __('admin.th_iso_code'); ?></th>
                                            <th><?= __('admin.th_name'); ?></th>
                                            <th><?= __('admin.th_phone_code'); ?></th>
                                            <th><?= __('admin.th_latitude'); ?></th>
                                            <th><?= __('admin.th_longitude'); ?></th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($countries as $country) { ?>
                                        <tr>
                                            <td class="sortname"><?= $country['sortname']; ?></td>
                                            <td class="name"><?= $country['name']; ?></td>
                                            <td class="phonecode"><?= $country['phonecode']; ?></td>
                                            <td class="lat"><?= $country['lat']; ?></td>
                                            <td class="lng"><?= $country['lng']; ?></td>
                                            <td>
                                                <?php if((int)$country['created_by'] >= 0) { ?>
                                                <button data-id="<?= $country['id']; ?>" class="btn btn-sm btn-warning updateCountryFormModal"><i class="fa fa-edit"></i></button>
                                            <button data-id="<?= $country['id']; ?>" class="btn btn-sm btn-danger daleteCountry"><i class="fa fa-trash"></i></a>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="states" role="tabpanel">
                    <div class="row">
                        <div class="col">
                            <button type="button" class="btn btn-secondary createStateFormModal">
                            <?= __("admin.create_new_state"); ?>
                            </button>
                            <select class="form-control ml-2 filter-state-by-country" style="width: auto; display: inline-block;">
                                <option value=""><?= __("admin.show_all"); ?></option>
                                <?php foreach($countries as $country) { ?>
                                <option value="<?= $country['id']; ?>"><?= $country['name']; ?></option>
                                <?php } ?>
                            </select>
                            <?php if(empty($states)) { ?>
                            <div class="alert alert-info"><?php echo __("admin.no_new_states"); ?></div>
                            <?php } else { ?>
                            <table class="table table-striped table-states">
                                <thead>
                                    <tr>
                                        <th><?= __('admin.th_name'); ?></th>
                                        <th><?= __('admin.th_country_name'); ?></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($states as $state) { ?>
                                    <tr data-country_id="<?= $state['country_id']; ?>">
                                        <td class="name"><?= $state['name']; ?></td>
                                        <td  class="country_id" data-country_id="<?= $state['country_id']; ?>"><?= $state['country_name']; ?></td>
                                        <td>
                                            <?php if((int)$state['created_by'] >= 0) { ?>
                                            <button data-id="<?= $state['id']; ?>" class="btn btn-sm btn-warning updateStateFormModal"><i class="fa fa-edit"></i></button>
                                            
                                            <button data-id="<?= $state['id']; ?>" class="btn btn-sm btn-danger deleteState"><i class="fa fa-trash"></i></a>

                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<!-- Country Form Modal -->
<div class="modal" id="countryFormModal" tabindex="-1" role="dialog" aria-labelledby="countryModal" aria-hidden="true">
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <form id="countryForm">
            <div class="modal-header">
                <h5 class="modal-title" id=""></h5>
                <button type="button" class="btn-close" aria-label="Close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-lable"><?= __('admin.th_iso_code'); ?></label>
                    <input type="text=" class="form-control" name="sortname" required />
                </div>
                <div class="form-group">
                    <label class="form-lable"><?= __('admin.th_name'); ?></label>
                    <input type="text=" class="form-control" name="name" required />
                </div>
                <div class="form-group">
                    <label class="form-lable"><?= __('admin.th_phone_code'); ?></label>
                    <input type="text=" class="form-control" name="phonecode" required />
                </div>
                <div class="form-group">
                    <label class="form-lable"><?= __('admin.th_latitude'); ?></label>
                    <input type="text=" class="form-control" name="lat" required />
                </div>
                <div class="form-group">
                    <label class="form-lable"><?= __('admin.th_longitude'); ?></label>
                    <input type="text=" class="form-control" name="lng" required />
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="id" value=""/>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= __('admin.close'); ?></button>
                <button type="button" class="btn btn-primary countryFormSubmit"><?= __('admin.save_changes'); ?></button>
            </div>
        </form>
    </div>
</div>
</div>
<!-- State Form Modal -->
<div class="modal" id="stateFormModal" tabindex="-1" role="dialog" aria-labelledby="stateFormModal" aria-hidden="true">
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <form id="stateForm">
            <div class="modal-header">
                <h5 class="modal-title" id=""></h5>
                <button type="button" class="btn-close" aria-label="Close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-lable"><?= __('admin.country'); ?></label>
                    <select class="form-control" name="country_id">
                        <option value="" disabled><?= __("admin.select_country"); ?></option>
                        <?php foreach($countries as $country) { ?>
                        <option value="<?= $country['id']; ?>"><?= $country['name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-lable"><?= __('admin.name'); ?></label>
                    <input type="text=" class="form-control" name="name" required />
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="id" value=""/>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= __('admin.close'); ?></button>
                <button type="button" class="btn btn-primary stateFormSubmit"><?= __('admin.save_changes'); ?></button>
            </div>
        </form>
    </div>
</div>
</div>
<script type="text/javascript">
    $( document ).ready(function() {
        $('.filter-state-by-country').on('change', function() {
            if($(this).val() == "") {
                $('.table-states tbody tr').show();
            } else {
                $('.table-states tbody tr').hide();
                $('.table-states tbody tr[data-country_id="'+$(this).val()+'"]').show();
            }
        });

        $('.daleteCountry').on('click', function(e){
            let c_id = $(this).data('id');
            Swal.fire({
              title: '<?= __('admin.are_you_sure'); ?>',
              text: "<?= __('admin.delete_country_warning'); ?>",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: '<?= __('admin.confirm'); ?>',
              cancelButtonText: `<?= __('admin.cancel'); ?>`,
            }).then((result) => {
              if (result.value) {
                window.location.href="<?= base_url('admincontrol/deleteCountry/'); ?>"+c_id;
              }
            });
        });

         $('.deleteState').on('click', function(e){
            let s_id = $(this).data('id');
            Swal.fire({
              title: '<?= __('admin.are_you_sure'); ?>',
              text: "<?= __('admin.delete_state_warning'); ?>",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: '<?= __('admin.confirm'); ?>',
              cancelButtonText: `<?= __('admin.cancel'); ?>`,
            }).then((result) => {
              if (result.value) {
                window.location.href="<?= base_url('admincontrol/deleteState/'); ?>"+s_id;
              }
            });
        });

        $('.createCountryFormModal').on('click', function(e){
            $('#countryFormModal .modal-title').text('<?= __("admin.create_new_country"); ?>');
            $('#countryFormModal input').val('');
            $('#countryFormModal').modal('show');
        });

        $('.updateCountryFormModal').on('click', function(e){
            $('#countryFormModal .modal-title').text('<?= __("admin.update_country"); ?>');

            let row = $(this).parent().parent();

            $('#countryFormModal input[name="sortname"]').val(row.find('td.sortname').text());
            $('#countryFormModal input[name="name"]').val(row.find('td.name').text());
            $('#countryFormModal input[name="phonecode"]').val(row.find('td.phonecode').text());
            $('#countryFormModal input[name="lat"]').val(row.find('td.lat').text());
            $('#countryFormModal input[name="lng"]').val(row.find('td.lng').text());
            $('#countryFormModal input[name="id"]').val($(this).data('id'));
            $('#countryFormModal').modal('show');
        });


        $('.createStateFormModal').on('click', function(e){
            $('#stateFormModal .modal-title').text('<?= __("admin.create_new_state"); ?>');
            $('#stateFormModal input').val('');
            $('#stateFormModal select').val('');
            $('#stateFormModal').modal('show');
        });

        $('.updateStateFormModal').on('click', function(e){
            $('#stateFormModal .modal-title').text('<?= __("admin.update_state"); ?>');

            let row = $(this).parent().parent();

            $('#stateFormModal input[name="name"]').val(row.find('td.name').text());
            $('#stateFormModal select[name="country_id"]').val(row.find('td.country_id').data('country_id'));
            $('#stateFormModal input[name="id"]').val($(this).data('id'));
            $('#stateFormModal').modal('show');
        });

        $('.countryFormSubmit').on('click', function(e){
            $('#countryFormModal .text-danger').remove();
            $('#countryFormModal input').css('border', '');
            $.ajax({
                type: "POST",
                url: "<?= base_url('admincontrol/createUpdateCountry') ?>",
                data: $('#countryForm').serialize(),
                dataType: 'JSON',
                success: function(response) {
                    if(response.errors) {
                      showErrors('countryFormModal', response.errors);
                    } else if(response.reload){
                        window.location.reload()
                    }
                }
            });
        });

        $('.stateFormSubmit').on('click', function(e){
            $('#stateFormModal .text-danger').remove();
            $('#stateFormModal input').css('border', '');
            $('#stateFormModal select').css('border', '');
            $.ajax({
                type: "POST",
                url: "<?= base_url('admincontrol/createUpdateState') ?>",
                data: $('#stateForm').serialize(),
                dataType: 'JSON',
                success: function(response) {
                    if(response.errors) {
                      showErrors('stateFormModal', response.errors);
                    } else if(response.reload){
                       window.location.reload()
                    }
                }
            });
        });

        function showErrors(modal, errors) {
            for(const key in errors) {
                if($('#'+modal+' input[name="'+key+'"]').length) {
                    $('#'+modal+' input[name="'+key+'"]').after('<span class="text-danger">'+errors[key]+'</span>');
                    $('#'+modal+' input[name="'+key+'"]').css('border', '1px solid red');
                } else {
                    $('#'+modal+' select[name="'+key+'"]').after('<span class="text-danger">'+errors[key]+'</span>');
                    $('#'+modal+' select[name="'+key+'"]').css('border', '1px solid red');
                }
            }
        }
    });
</script>