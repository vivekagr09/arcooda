<style type="text/css">
	#myTable tr td {
		vertical-align: middle;
	}
	#progressbar123 {
	  background-color: lightgrey;
	  padding: 3px;
	}
	#progressbar123>div {
	  background-color: #007BFF;
	  width: 0%;
	  height: 15px;
	}
</style>

<div class="col-12 mb-4">
	<div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
		<div class="col">
			<div class="card bg-info text-white h-100">
				<div class="card-body text-center">
					<h3><?php echo __('admin.banner_campaign') ?></h3>
					<a href="<?= base_url('integration/integration_tools_form/banner') ?>" class="btn btn-light mt-3"><i class="fa fa-plus"></i> <?php echo __('admin.create_new') ?></a>
				</div>
			</div>
		</div>
		<div class="col">
			<div class="card bg-secondary text-white h-100">
				<div class="card-body text-center">
					<h3><?= __('admin.text_campaign') ?></h3>
					<a href="<?= base_url('integration/integration_tools_form/text_ads') ?>" class="btn btn-light mt-3"><i class="fa fa-plus"></i> <?php echo __('admin.create_new') ?></a>
				</div>
			</div>
		</div>
		<div class="col">
			<div class="card bg-primary text-white h-100">
				<div class="card-body text-center">
					<h3><?php echo __('admin.link_campaign') ?></h3>
					<a href="<?= base_url('integration/integration_tools_form/link_ads') ?>" class="btn btn-light mt-3"><i class="fa fa-plus"></i> <?php echo __('admin.create_new') ?></a>
				</div>
			</div>
		</div>
		<div class="col">
			<div class="card bg-dark text-white h-100">
				<div class="card-body text-center">
					<h3><?php echo __('admin.video_campaign') ?></h3>
					<a href="<?= base_url('integration/integration_tools_form/video_ads') ?>" class="btn btn-light mt-3"><i class="fa fa-plus"></i> <?php echo __('admin.create_new') ?></a>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="container-fluid mt-3">
    <div class="row">
        <div class="card">
<div class="card-body">
    <div class="row">
        <div class="col-12">
            <div class="row mb-3 g-3">
                <div class="col-sm-3">
                    <div class="form-group">
                        <select class="form-select category_id">
                            <option value=""><?php echo __('admin.search_by_all_categories') ?></option>
                            <?php 
                            if(count($categories)>0) {
                                $parentcategoyrid=0;
                                foreach ($categories as $key => $value) {
                                    if($parentcategoyrid!=0 && $parentcategoyrid!=$value['pid']) { 
                                    }
                                    if($parentcategoyrid!=$value['pid']) {
                                        ?>
                                        <option value="<?= $value['value'] ?>"><?= $value['label'] ?></option>  
                                        <?php
                                    } else {
                                        ?>
                                        <option value="<?= $value['value'] ?>">--<?= $value['label'] ?></option>
                                        <?php 
                                    }
                                    $parentcategoyrid=$value['pid'];
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <input class="form-control ads_name" placeholder="<?php echo __('admin.search_enter_ads_name') ?>" type="search">
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <select name="vendor_id" class="form-select vendor_id">
                            <?php $selected = isset($_GET['vendor_id']) ? $_GET['vendor_id'] : ''; ?>
                            <option value=""><?php echo __('admin.all_campaigns') ?></option>
                            <option value="only_admins"><?php echo __('admin.all_admin_campigns') ?></option>
                            <option value="only_vendors"><?php echo __('admin.all_vendors_campigs') ?></option>
                            <?php foreach ($vendors as $key => $value) { ?>
                                <option <?= $selected == $value['id'] ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <select name="groups[]" class="form-select select2 groups" multiple="multiple">
                            <?php foreach ($groups as $key => $value) { ?>
                                <option value="<?= $key ?>"><?= $value ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label class="form-check-label">
                            <input type="checkbox" class="form-check-input show_only" name="show_only" value="admin">
                            <span><?php echo __('admin.show_only_admin') ?></span>
                        </label>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <select class="form-select" name="status">
                            <option value=""><?php echo __('admin.search_by_all_status') ?></option>
                            <option value="1"><?php echo __('admin.public'); ?></option>
                            <option value="2"><?php echo __('admin.in_review'); ?></option>
                            <option value="0"><?php echo __('admin.draft'); ?></option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#cron-job-info-modal"><?php echo __('admin.cron_job_setting') ?></button>
                    </div>  
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#perform-security-check-modal"><?php echo __('admin.perform_security_check') ?></button>
                    </div>  
                </div>
            </div>
        </div>
        <div class="text-center col-12 empty-div d-none">
            <div class="d-flex justify-content-center align-items-center flex-column mt-5">
                <i class="fas fa-exchange-alt fa-5x text-muted"></i>
                <h3 class="text-muted"><?php echo __('admin.no_data_found') ?></h3>
            </div>
        </div>
        <div class="table-responsive b-0" data-pattern="priority-columns">
            <table id="myTable" class="table table-striped table-white-space-normal">
                <thead class="bg-secondary text-white">
                    <tr>
                        <th><?php echo __('admin.image') ?></th>
                        <th><?php echo __('admin.user') ?></th>
                        <th><?php echo __('admin.campaign_name') ?></th>
                        <th><?php echo __('admin.integration_plugin_name') ?></th>
                        <th><?php echo __('admin.created_at') ?></th>
                        <th><?php echo __('admin.view') ?></th>
                        <th><?php echo __('admin.ratio') ?></th>
                        <th><?php echo __('admin.security_status') ?></th>
                        <th><?php echo __('admin.code') ?></th>
                        <th><?php echo __('admin.campaign_status') ?></th>
                        <th><?php echo __('admin.info') ?></th>
                        <th><?php echo __('admin.action') ?></th>
                    </tr>
                </thead>
                <tbody class="integration-product"></tbody>
                <tfoot>
                    <tr>
                        <td colspan="12" class="text-end">
                            <ul class="pagination pagination-td"></ul>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

        </div>
    </div>
</div>

<div class="modal fade" id="integration-mlm-info"></div>

<div class="modal fade" id="integration-code">
	<div class="modal-dialog">
		<div class="modal-content"></div>
	</div>
</div>

<div class="modal fade" id="showcode-code"></div>

<div id="cron-job-info-modal" class="modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><?= __('admin.cron_job_setting') ?></h5>
      </div>
      <div class="modal-body">
    		<div class="row">
					<div class="col-sm-12">
						<h5><?= __('admin.what_is_cron_job') ?></h5>
						<p><?= __('admin.what_is_cron_job_answer') ?></p>

						<h6><?= __('admin.to_add_cron_job_steps') ?>:</h6>

						<ol>
							<li><?= __('admin.to_add_cron_job_step1') ?></li>
							<li><?= __('admin.to_add_cron_job_step2') ?></li>
							<li><?= __('admin.to_add_cron_job_step3') ?></li>
							<li><?= __('admin.to_add_cron_job_step4') ?>  <b><?= __('admin.once_per_minute') ?>(* * * * *)</b>.</li>
							<li><?= __('admin.to_add_cron_job_step5') ?> <div> <code>curl <?= base_url('/cronJob/check_campaign_security') ?></code></div> </li>
							<li><?= __('admin.to_add_cron_job_step6') ?></li>
						</ol>
					</div>
				</div>
      </div>
    	<div class="modal-footer">
	      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= __('admin.close') ?></button>
    	</div> 
    </div>
  </div>  	 	
</div>

<div id="perform-security-check-modal" class="modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><?= __('admin.perform_security_check') ?></h5>
      </div>
      <div class="modal-body">
      	<div class="step-1">
					<h5><?= __('admin.are_you_sure_perform_security_check') ?></h5>
      		<h6><?= __('admin.take_longer_depending_campaigns_available') ?></h6>
      	</div>
      	<div class="step-2" style="display:none;">
      		<h5><?= __('admin.wait_while_performing_security') ?></h5>
      		<div id="progressbar123">
					  <div></div>
					</div>
      		<h6 class="text-success approved" data-count="0" style="display:none;">0 <?= __('admin.campaigns_verified_successfully') ?></h6>
      		<h6 class="text-info pending" data-count="0" style="display:none;">0 <?= __('admin.campaigns_in_pending_integration') ?></h6>
      		<h6 class="text-warning warning" style="display:none;font-size: 21px;">
      			<?= __('admin.no_campagins_available') ?>
      		</h6>
      		<h6 class="text-warning pending" style="display:none;">
      			<?= __('admin.no_campagins_available') ?>
      		</h6>
      	</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary allow_to_perform_security_check"><?= __('admin.yes_continue') ?></button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= __('admin.close') ?></button>
      </div>
    </div>
  </div>
</div>

<?= $social_share_modal; ?>

<script type="text/javascript">
	
	$(document).on('click','.check-campaign-with-id',function(){
		var el = $(this);
		var id = el.data('id');
		$.ajax({
      type:"POST",
      url: '<?= base_url('Integration/check_campaign_security_with_id/') ?>' + id,
      dataType:"json",
      success: function(data){
      	if(data.statusClass){
      		el.parents('td').siblings('.security-status').find('button.badge').remove();

      		el.parents('td').siblings('.security-status').find('span.badge').removeClass().addClass(data.statusClass).text(data.message);

      		if(data.security_status == 0)
      			el.parents('td').siblings('.security-status').prepend(data.integration_code_button);
      	}
	    }
    });
	})

	$(document).on('click', '.allow_to_perform_security_check', function(){
		$(this).hide();
		$('#perform-security-check-modal .step-1').hide();
		$('#perform-security-check-modal .step-2').show();
		$('#perform-security-check-modal .modal-footer').hide();
		recursive_security_check();
	});

	function recursive_security_check(index = 1) {
		$.ajax({
      type:"POST",
      url: '<?= base_url('integration/check_campaign_security')?>',
      dataType:"json",
      data:{index:index},
      success: function(data){
      	if(data.progress_percentage){
      		$('#progressbar123').show();
	      	$('#progressbar123 > div').css('width',data.progress_percentage);
      	} else {
      		$('#progressbar123').hide();
      	}

      	if(data.warning){
      		$('#perform-security-check-modal .step-2 h5').hide();
          $('#perform-security-check-modal .step-2 h6.text-warning').show();
      	} else {
      		let existing_count = $('#perform-security-check-modal .step-2 .'+data.security_status).data('count');
          $('#perform-security-check-modal .step-2 .'+data.security_status).data('count',(existing_count+1));
          $('#perform-security-check-modal .step-2 .'+data.security_status).text((existing_count+1)+' '+data.message);
          $('#perform-security-check-modal .step-2 .'+data.security_status).show();
      	}

        if(data.index){
          recursive_security_check(data.index);
        } else {
    			$('#perform-security-check-modal .modal-footer').show();
    			$('#perform-security-check-modal .modal-footer').html('<button type="button" class="btn btn-secondary" onclick="window.location.reload()">'+'<?= __('admin.close') ?>'+'</button>');
        } 
	    }
    });
	}


	$(document).on('click', ".btn_lang_toggle", function(){
		let skip_change = false;
		let id = $(this).data('lang_id');
		let column = $(this).data('column');
		let status = $(this).hasClass('fa-toggle-off') ? 1 : 0;
		$(this).addClass('fa-toggle-off').removeClass('fa-toggle-on');
		$(this).css("color", "red");


		if(status) {
			$(this).addClass('fa-toggle-on').removeClass('fa-toggle-off');
			$(this).css("color", "green");
		}

	});	


	$('.select2').select2({
		placeholder: '<?= __('admin.search_by_groups') ?>'
	});

	var xhr;
	function getPage(url){
		$this = $(this);
		if(xhr && xhr.readyState != 4) xhr.abort();

		xhr = $.ajax({
			url:url,
			type:'POST',
			dataType:'html',
			data:{
				category_id: $(".category_id").val(),
				ads_name: $(".ads_name").val(),
				vendor_id: $(".vendor_id").val(),
				groups : $('.select2').val(),
				show_only: $(".show_only").prop("checked"),
				status: $("select[name='status']").val(),
			},
			beforeSend:function(){$(".btn-search").btn("loading");},
			complete:function(){$(".btn-search").btn("reset");},
			success:function(json){
				if(json){
					$("#myTable tbody").html(json);
					$("#myTable").show();
					$(".empty-div").addClass("d-none");
				} else {
					$(".empty-div").removeClass("d-none");
					$("#myTable").hide();
				}

				$('[data-toggle="tooltip"]').tooltip();
			},
		})

		xhr = $.ajax({
			url:url,
			type:'POST',
			dataType:'html',
			data:{
				category_id: $(".category_id").val(),
				ads_name: $(".ads_name").val(),
				vendor_id: $(".vendor_id").val(),
				groups : $('.select2').val(),
				show_only: $(".show_only").prop("checked"),
				status: $("select[name='status']").val(),
				paginate:true,
			},
			beforeSend:function(){$(".btn-search").btn("loading");},
			complete:function(){$(".btn-search").btn("reset");},
			success:function(json){
				if(json)
					$("#myTable .pagination-td").html(json);
			},
		})

		
	}

	$(".category_id,.vendor_id,.select2,.show_only,select[name='status']").on("change",function(){
		getPage('<?= base_url("integration/integration_tools/1") ?>');
	});

	$(".ads_name").on("keyup",function(){
		getPage('<?= base_url("integration/integration_tools/1") ?>');
	});

	$(".btn-search").on("click",function(){
		getPage('<?= base_url("integration/integration_tools/1") ?>');
		return false;
	})

	getPage('<?= base_url("integration/integration_tools") ?>/1');

	$("#myTable .pagination-td").delegate("a","click",function(e){
		e.preventDefault();
		getPage($(this).attr("href"));
		return false;
	})

	$("#myTable").delegate(".btn-show-integration-mlm-info",'click',function(){
		$this = $(this);
		$.ajax({
			url:'<?= base_url("integration/getIntegrationMlmInfo") ?>',
			type:'POST',
			dataType:'html',
			data:{
				id: $this.attr("data-id"),
			},
			beforeSend:function(){
				$this.btn("loading");
			},
			complete:function(){
				$this.btn("reset");
			},
			success:function(html){
				$("#integration-mlm-info").html(html);
				$("#integration-mlm-info").modal("show");
			},
		})
	});

	$("#myTable").delegate(".btn-show-code",'click',function(){
		$this = $(this);
		$.ajax({
			url:'<?= base_url("integration/integration_code_modal") ?>',
			type:'POST',
			dataType:'html',
			data:{
				id: $this.attr("data-id"),
			},
			beforeSend:function(){
				$this.btn("loading");
			},
			complete:function(){
				$this.btn("reset");
			},
			success:function(html){
				$("#showcode-code").html(html);
				$("#showcode-code").modal("show");
			},
		})
	});

	$("#myTable").delegate(".btn-show-terms",'click',function(){
		$this = $(this);
		$.ajax({
			url:'<?= base_url("integration/integration_terms_modal") ?>',
			type:'POST',
			dataType:'json',
			data:{
				id: $this.attr("data-id"),
			},
			beforeSend:function(){
				$this.btn("loading");
			},
			complete:function(){
				$this.btn("reset");
			},
			success:function(json){
				if(json['html']){
					$("#showcode-code").html(json['html']);
					$("#showcode-code").modal("show");
				}
			},
		})
	})

	$("#myTable").delegate(".btn-campaign-detail",'click',function(){
		var id = $(this).attr("data-id");
		$("#campaign-details-"+id).modal("show");
	});

	$("#myTable").delegate(".wallet-toggle .tog",'click',function(){
		$(this).parents(".wallet-toggle").find("> div").toggleClass("hide");
	})
	$("#myTable").delegate(".tool-remove-link",'click',function(){
		if(!confirm("<?php echo __('admin.are_you_sure') ?>")) return false;
		return true;
	})

	$("#myTable").delegate(".get-code",'click',function(){
		$this = $(this);
		$.ajax({
			url:'<?= base_url("integration/tool_get_code") ?>',
			type:'POST',
			dataType:'json',
			data:{id:$this.attr("data-id")},
			beforeSend:function(){ $this.btn("loading"); },
			complete:function(){ $this.btn("reset"); },
			success:function(json){
				if(json['html']){
					$("#integration-code .modal-content").html(json['html']);
					$("#integration-code").modal("show");
				}
			},
		})
	})

	$(document).ready(function(){
	  $('[data-toggle="tooltip"]').tooltip();
	}); 

</script>