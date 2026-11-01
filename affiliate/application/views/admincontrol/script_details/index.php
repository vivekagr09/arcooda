<?php if (ENVIRONMENT !== 'demo'): ?>
<div class="card">
	<div class="card-header bg-secondary text-white">
		<h5><?= strtolower($licence['license']) == 'company site' ? __('admin.company_site') : 'Codecanyon' ?> <?= __("admin.license_details") ?></h5>
	</div>
	<div class="card-body" >
		<div class="license-details row">
			<div class="col-sm-4">
				<div class='data-row'>
					<label><?= __("admin.license_code") ?></label>
					<span class="code"><?= $licence['code'] ?></span>
				</div>
			</div>
		    <div class="col-sm-4">
		    	<div class='data-row'>
					<label><?= __("admin.purchase_amount") ?></label>
					<span><?= (float)$licence['amount'] ?> USD</span>
				</div>
			</div>
		    <div class="col-sm-4">
		    	<div class='data-row'>
					<label><?= __("admin.support_amount") ?></label>
					<span><?= (float)$licence['support_amount'] ?> USD</span>
				</div>
			</div>
		    <div class="col-sm-4">
		    	<div class='data-row'>
					<label><?= __("admin.sold_at") ?></label>
					<span><?= $licence['sold_at'] ?></span>
				</div>
			</div>
		    <div class="col-sm-4">
		    	<div class='data-row'>
					<label><?= __("admin.license_type") ?></label>
					<span><?= $licence['license'] ?></span>
				</div>
			</div>
		    <div class="col-sm-4">
		    	<div class='data-row'>
					<label><?= __("admin.supported_until") ?></label>
					<span><?= $licence['supported_until'] ? $licence['supported_until'] : '<?= __("admin.free") ?>' ?></span>

				</div>
			</div>
		    <div class="col-sm-4">
		    	<div class='data-row'>
					<label><?= __("admin.buyer_username") ?></label>
					<span><?= $licence['buyer'] ?></span>
				</div>
			</div>

			<div class="col-sm-4">
		    	<div class='data-row'>
					<label><?= __("admin.uninstall_script") ?></label>
					<span>
						<button class="btn uninstall-script btn-danger btn-sm"><?= __("admin.un_install") ?></button>
					</span>
				</div>
			</div>
			
		</div>
	</div>
</div>
<?php endif; ?>


<div class="card mt-4">
	<div class="card-header bg-secondary text-white">
		<h5><?= $product['name'] ?> <?= __("admin.changelog") ?></h5>
	</div>
	<div class="card-body" >
		<div class="change-history">
			<?php foreach ($versions as $key => $value) { ?>
				<div class="<?= $value['show_frame'] == "1" ? 'frame' : '' ?>">
					<h2><b><?= __("admin.version") ?> <?= $value['code'] ?></b> — <?= date('M d, Y',strtotime($value['date'])) ?></h2>
					<div class="b">
						<ul>
							<?php 
								$logs = json_decode($value['change_log'],1);
								foreach ($logs as $key => $log) {
									echo '<li>'. $log .'</li>';
								}
							?>
						</ul>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function () {
	    $(".change-history ul li").each(function(i,el){
	    	$(el).html($(el).html().replace(new RegExp('NEW', 'gi'), "<strong class='text-success'>"+'<?= __('admin.new') ?>'+"</strong>"));
	    	$(el).html($(el).html().replace(new RegExp('ADDED', 'gi'), "<strong class='text-info'>"+'<?= __('admin.added') ?>'+"</strong>"));
	    	$(el).html($(el).html().replace(new RegExp('IMPROVED', 'gi'), "<strong class='text-danger'>"+'<?= __('admin.improved') ?>'+"</strong>"));
	    	$(el).html($(el).html().replace(new RegExp('FIXED', 'gi'), "<strong class='text-primary'>"+'<?= __('admin.fixed') ?>'+"</strong>"));
	    });

	    
	    $(document).on('change','input[name="licence"]', function(){
		    $this = $(this);
		    $.ajax({
		        url:'<?php echo base_url() ?>/install/codecanyon.php',
		        type:'POST',
		        dataType:'json',
		        data:{
		            code: $this.val()
		        },
		        success:function(json){
		            $($this).parent().removeClass("has-error");
		            $($this).parent().find("span.text-danger").remove();                
		            if(json['errors']){
		                $('[name="username"]').val('');
		                $.each(json['errors'], function(i,j){
	                       $($this).parent().addClass("has-error");
	                       $($this).parent().append("<span class='text-danger'>"+ j +"</span>");
		                })
		            }else{
		                if(json.response.buyer){
		                	$('.swal2-confirm').removeAttr('disabled');
		                    $('input[name="username"]').val(json.response.buyer);
		                }
		            }
		        },
		    })

		    return false;
		});
	});

$(".uninstall-script").on("click",function(){
    Swal.fire({
        title: '<h1 class="modal-title text-center"><span class="badge bg-danger text-white"><?= __("admin.uninstall_warning_attention") ?></span></h1>',
        html: `
            <ul class="list-unstyled">
                <li class="text-start mt-3"><strong><?= __("admin.uninstall_warning_attention_info1") ?></strong> <span class="badge bg-warning"><?= __("admin.first_uninstall") ?></span> <?= __("admin.uninstall_warning_attention_info2") ?></li>
                <li class="text-start mt-2"><strong><?= __("admin.uninstall_warning_attention_info3") ?></strong> <span class="badge bg-warning"><?= __("admin.first_uninstall") ?></span> <?= __("admin.uninstall_warning_attention_info4") ?></li>
            </ul>
            <div class="frame"><span class="badge bg-success"><?= __("admin.site_data_is_safe") ?></span> <span class="badge bg-success"></span>
            <br><span class="fs-5"><strong><?= __("admin.uninstall_warning_attention_info5") ?></strong></span></div>
            <br>
            <div class="text-start uninstall-script-form">
                <div class="mb-3">
                    <label class="form-label"><?= __("admin.admin_password") ?></label>
                    <input type="password" name="password" class="form-control" autocomplete="off">
                </div>
                <div class="mb-3">
                    <label class="form-label"><?= __("admin.Enter_license") ?></label>
                    <input type="text" name="licence" class="form-control" autocomplete="off">
                </div>
                <div class="mb-3">
                    <label class="form-label"><?= __("admin.user_name") ?></label>
                    <input type="text" name="username" class="form-control" readonly="true" autocomplete="off">
                </div>
            </div>
        `,
        showCancelButton: true,
        onOpen: function (){
            $('.swal2-confirm').attr('disabled', true);
        },
        confirmButtonText: '<?= __("admin.uninstall") ?>',
        showLoaderOnConfirm: true,
        preConfirm:  (login)  => {
            var data = {
                password: btoa($(".uninstall-script-form input[name=password]").val()),
                licence: btoa($(".uninstall-script-form input[name=licence]").val()),
            };

            if(data.password == "") {
                Swal.showValidationMessage('<?= __("admin.password_should_not_be_empty") ?>');
            } else {
                let response = fetch('<?= base_url("Installversion/uninstall_script") ?>/' + data.password + "/" + (data.licence ? data.licence : "00-00"))
                    .then(async response => {
                        let json = await response.json();

						if(json['status'] === 'error') {
						showPrintMessage(json['message'], 'error');}


                        else if (json["errors"]) {
                            $.each(json["errors"], function(i, j){
                                Swal.showValidationMessage(j);
                            });
                        } else {
                            return json;
                        }
                    })
                    .catch(error => {
                        Swal.showValidationMessage(error);
                    });

                if(response.warning) {
                    Swal.showValidationMessage(response.warning);
                } else if(response.error) {
                    Swal.showValidationMessage(response.error);
                } else if(response.errors) {
                    Swal.showValidationMessage(response.error);
                } else {
                    return response;
                }
            }
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if(result.value && result.value.success){
            window.location.href = '<?= base_url("/install") ?>';
        }
    });
});
</script>