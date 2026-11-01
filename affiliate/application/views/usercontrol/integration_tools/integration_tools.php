<div class="row">
	<div class="col-sm-3">
		<div class="card m-b-30 text-white bg-primary">
			<div class="card-body">
				<blockquote class="card-bodyquote mb-0">
					<h3><?php echo __('admin.banners') ?></h3>
					<a href="<?= base_url('usercontrol/integration_tools_form/banner') ?>" class="btn btn-dark waves-effect waves-light"><i class="fa fa-plus"></i> <?php echo __('admin.create_new') ?></a>
				</blockquote>
			</div>
		</div>
	</div>
	<div class="col-sm-3">
		<div class="card m-b-30 text-white bg-secondary">
			<div class="card-body">
				<blockquote class="card-bodyquote mb-0">
					<h3><?= __('admin.text_ads') ?></h3>
					<a href="<?= base_url('usercontrol/integration_tools_form/text_ads') ?>" class="btn btn-dark waves-effect waves-light"><i class="fa fa-plus"></i> <?php echo __('admin.create_new') ?></a>
				</blockquote>
			</div>
		</div>
	</div>
	<div class="col-sm-3">
		<div class="card m-b-30 text-white bg-info">
			<div class="card-body">
				<blockquote class="card-bodyquote mb-0">
					<h3><?php echo __('admin.invisible_links') ?></h3>
					<a href="<?= base_url('usercontrol/integration_tools_form/link_ads') ?>" class="btn btn-dark waves-effect waves-light"><i class="fa fa-plus"></i> <?php echo __('admin.create_new') ?></a>
				</blockquote>
			</div>
		</div>
	</div>
	<div class="col-sm-3">
		<div class="card m-b-30 text-white bg-warning">
			<div class="card-body">
				<blockquote class="card-bodyquote mb-0">
					<h3><?php echo __('admin.viral_videos') ?></h3>
					<a href="<?= base_url('usercontrol/integration_tools_form/video_ads') ?>" class="btn btn-dark waves-effect waves-light"><i class="fa fa-plus"></i> <?php echo __('admin.create_new') ?></a>
				</blockquote>
			</div>
		</div>
	</div>
</div>

<?php if(isset($campaign_count_alert)){?>
<h5 class="notification_on_pages mt-3">
    <div class="bg-danger text-white p-3 rounded">
      <?= $campaign_count_alert; ?>
  	</div>
  </h5>
<?php } ?>

<div class="row mt-3">
	<div class="col-12">
		<div class="card m-b-20">
			<div class="card-body">
				<div class="row">
					<div class="col-12">
						<div class="row mb-3">
							<div class="col-sm-3">
								<div class="form-group">
									<select class="form-control category_id" >
										<option value=""><?= __('user.all_categories') ?></option>
										<?php 
                                    if(count($categories)>0)
                                    {
                                        $parentcategoyrid=0;
                                            foreach ($categories as $key => $value)
                                            {
                                                if($parentcategoyrid!=0 && $parentcategoyrid!=$value['pid'])
                                                { 
                                                    ?>
                                                    <?php        
                                                }
                                                if($parentcategoyrid!=$value['pid'])
                                                {
                                                    ?>
                                                  <option value="<?= $value['value'] ?>"><?= $value['label'] ?></option>  
                                                    <?php
                                                }
                                                else 
                                                {
                                                    ?>
                                                    <option value="<?= $value['value'] ?>">--<?= $value['label'] ?></option>
                                                    <?php 
                                                }
                                                    $parentcategoyrid=$value['pid'];

                                            } ?>
                                        
                                        <?php
                                    }
                                   ?>
									</select>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group">
									<input class="table-search form-control ads_name" placeholder="Search" type="search">
								</div>	
							</div>
							<div class="col-sm-3">
								<div class="form-group">
									<select class="form-control" name="status">
										<option value=""><?= __('user.search_by_all_status') ?></option>
										<option value="1"><?= __('user.public'); ?></option>
										<option value="2"><?= __('user.in_review'); ?></option>
										<option value="0"><?= __('user.draft'); ?></option>
									</select>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group">
								  	<button class="btn btn-dark waves-effect waves-light btn-integration-tools" data-bs-toggle="modal" data-bs-target="#perform-security-check-modal"><?= __('admin.perform_security_check')?></button>
								</div>  
							</div>	
						</div>
					</div>

					<div class="text-center col-12 empty-div d-none">
						 <div class="d-flex justify-content-center align-items-center flex-column mt-5">
							 <i class="fas fa-exchange-alt fa-5x text-muted"></i>
							 <h3 class="text-muted"><?= __('admin.no_data_found') ?></h3>
						 </div>
                    </div>
					<div class="table-responsive">
						<table id="myTable" class="table table-striped table-hover">
							<thead>
								<tr>
									<th><?= __('user.image') ?></th>
									<th><?= __('user.campaign_name') ?></th>
									<th><?= __('user.integration_plugin_name') ?></th>
									<th class="text-center"><?= __('user.view') ?></th>
									<th ><?= __('user.ratio') ?></th>
									<th class="text-center"><?= __('user.security_status') ?></th>
									<th class="text-center"><?= __('user.status') ?></th>
									<th class="text-center"><?= __('user.action') ?></th>

								</tr>
							</thead>
							<tbody></tbody>
							<tfoot>
								<tr>
									<td colspan="12" class="text-right">
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
			<div id="vendor-security-progressbar" class="progress">
				<div class="progress-bar progress-bar-striped" role="progressbar" style="width: 10%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
			</div>
      		<h6 class="text-success approved" data-count="0" style="display:none;">0 <?= __('admin.campaigns_verified_successfully') ?></h6>
      		<h6 class="text-info pending" data-count="0" style="display:none;">0 <?= __('admin.campaigns_in_pending_integration') ?></h6>
      		<h6 class="text-warning warning" style="display:none;font-size: 21px;"><?= __('admin.no_campagins_available') ?></h6>
      	</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary allow_to_perform_security_check"><?= __('admin.yes_continue') ?></button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= __('user.close') ?></button>
      </div>
    </div>
  </div>
</div>

<?= $social_share_modal ?>

<script type="text/javascript">

	$(document).on('click','.check-campaign-with-id',function(){
		var el = $(this);
		var id = el.data('id');
		$.ajax({
	      type:"POST",
	      url: '<?= base_url('usercontrol/check_campaign_security_with_id/') ?>' + id,
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
	      url: '<?= base_url('usercontrol/check_campaign_security')?>',
	      dataType:"json",
	      data:{index:index},
	      success: function(data){
	      	if(data.progress_percentage){
	      		$('#vendor-security-progressbar').show();
		      	$('#vendor-security-progressbar > div').css('width',data.progress_percentage);
	      	} else {
	      		$('#vendor-security-progressbar').hide();
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
			},
		})

		xhr = $.ajax({
			url:url,
			type:'POST',
			dataType:'html',
			data:{
				category_id: $(".category_id").val(),
				ads_name: $(".ads_name").val(),
				status: $("select[name='status']").val(),
				paginate: true,
			},
			beforeSend:function(){$(".btn-search").btn("loading");},
			complete:function(){$(".btn-search").btn("reset");},
			success:function(json){
				$("#myTable .pagination-td").html(json);
			},
		})
	}

	$(".category_id,select[name='status']").on("change",function(){
		getPage('<?= base_url("usercontrol/integration_tools/") ?>/1');
	});
	$(".ads_name").on("keyup",function(){
		getPage('<?= base_url("usercontrol/integration_tools/") ?>/1');
	});
	
	getPage('<?= base_url("usercontrol/integration_tools") ?>/1');

	$("#myTable .pagination-td").delegate("a","click",function(e){
		e.preventDefault();
		getPage($(this).attr("href"));
		return false;
	})

	$("#myTable").delegate(".btn-show-integration-mlm-info",'click',function(){
		$this = $(this);
		$.ajax({
			url:'<?= base_url("usercontrol/getIntegrationMlmInfo") ?>',
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
			url:'<?= base_url("usercontrol/integration_code_modal_new") ?>',
			type:'POST',
			dataType:'json',
			data:{id: $this.attr("data-id")},
			beforeSend:function(){$this.btn("loading");},
			complete:function(){$this.btn("reset");},
			success:function(json){
				if(json['html']){
					$("#showcode-code").html(json['html']);
					$("#showcode-code").modal("show");
				}
			},
		})
	})

	$("#myTable").delegate(".btn-show-terms",'click',function(){
		$this = $(this);
		$.ajax({
			url:'<?= base_url("usercontrol/integration_terms_modal") ?>',
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

	$("#myTable").delegate(".wallet-toggle .tog",'click',function(){
		$(this).parents(".wallet-toggle").find("> div").toggleClass("hide");
	})
	$("#myTable").delegate(".tool-remove-link",'click',function(){
		if(!confirm('<?= __('user.are_you_sure') ?>')) return false;
		return true;
	})

	$("#myTable").delegate(".get-code",'click',function(){
		$this = $(this);
		$.ajax({
			url:'<?= base_url("usercontrol/tool_get_code") ?>',
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

	$(".not-show-alert").on('click',function(){
		$this = $(this);
		$.ajax({
			url:'<?= base_url("usercontrol/setCookie") ?>',
			type:'POST',
			dataType:'json',
			data:{
				name: 'campaign_count_alert',
			},
			success:function(result){
				if(result)
					$this.parents('.row').remove();
			},
		})
	})
</script>