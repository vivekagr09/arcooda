<div class="row">
	<div class="col-12">
		<ul class="nav nav-tabs">
			<li class="nav-item">
				<a class="nav-link active" data-bs-toggle="tab" href="#tab-menu_statistics"><?= __('user.menu_statistics') ?></a>
			</li>
			<li class="nav-item">
				<a class="nav-link" data-bs-toggle="tab" href="#tab-menu_report_statistics"><?= __('user.menu_report_statistics') ?></a>
			</li>
			<li class="nav-item">
				<a class="nav-link" data-bs-toggle="tab" href="#tab-menu_report_store_orders"><?= __('user.my_all_orders') ?></a>
			</li>
			<li class="nav-item">
				<a class="nav-link" data-bs-toggle="tab" href="#tab-menu_report_logs"><?= __('user.page_title_logs') ?></a>
			</li>
		</ul>

		<div class="tab-content">
			<div class="tab-pane active" id="tab-menu_statistics">
				<div class="card">
					<div class="card-header">
						<h4 class="card-title"><?= __('user.menu_statistics') ?></h4>
					</div>
					<div class="card-body">
						<div class="row mb-5">
							<div class="col-sm-4 mb-5">
								<div class="card">
									<div class="card-body">
										<h4 class="text-center"><span class="pull-left"> <?= (int)$statistics['clicks_count'] ?></span> <?= __('user.click_by_country') ?></h4>
										<?php if((int)$statistics['clicks_count'] > 0){ ?>
											<ul class="list-unstyled list-inline text-center">
							                    <?php $i = 0; foreach($statistics['clicks'] as $country => $counts){ ?>
							                        <li class="list-inline-item">
							                            <p><i class="mdi mdi-checkbox-blank-circle <?php echo 'color-'.$i++ % 5 ; ?> mr-2"></i><?php echo $country; ?></p>
							                        </li>
							                    <?php } ?>
											</ul>
											<div id="clicks-chart"></div>
										<?php } else { ?>
											<div class="empty-graph">
												<div class="text-center mt-5">
													<div class="d-flex justify-content-center align-items-center flex-column mt-5">
														 <i class="fas fa-exchange-alt fa-5x text-muted"></i>
														 <h3 class="text-muted"><?= __('admin.no_data_found') ?></h3>
													</div>
												</div>
											</div>
										<?php } ?>
									</div>
								</div>
							</div>

							<div class="col-sm-4 mb-5">
								<div class="card">
									<div class="card-body">
										<h4 class="text-center"><span class="pull-left"> <?= (int)$statistics['action_clicks_count'] ?></span> <?= __('user.action_click_by_country') ?></h4>
										<?php if((int)$statistics['action_clicks_count'] > 0){ ?>
											<ul class="list-unstyled list-inline text-center">
							                    <?php $i = 0; foreach($statistics['action_clicks'] as $country => $counts){ ?>
							                        <li class="list-inline-item">
							                            <p><i class="mdi mdi-checkbox-blank-circle <?php echo 'color-'.$i++ % 5 ; ?> mr-2"></i><?php echo $country; ?></p>
							                        </li>
							                    <?php } ?>
											</ul>
											<div id="action_click-chart"></div>
										<?php } else { ?>
											<div class="empty-graph">
												<div class="text-center mt-5">
													<div class="d-flex justify-content-center align-items-center flex-column mt-5">
														 <i class="fas fa-exchange-alt fa-5x text-muted"></i>
														 <h3 class="text-muted"><?= __('admin.no_data_found') ?></h3>
													</div>
												</div>
											</div>
										<?php } ?>
									</div>
								</div>
							</div>

							<div class="col-sm-4 mb-5">
								<div class="card">
									<div class="card-body">
										<h4 class="text-center"><span class="pull-left"> <?= (int)$statistics['sale_count'] ?></span> <?= __('user.sale_by_country') ?></h4>
										<?php if((int)$statistics['sale_count'] > 0){ ?>
											<ul class="list-unstyled list-inline text-center">
							                    <?php $i = 0; foreach($statistics['sale'] as $country => $counts){ ?>
							                        <li class="list-inline-item">
							                            <p><i class="mdi mdi-checkbox-blank-circle <?php echo 'color-'.$i++ % 5 ; ?> mr-2"></i><?php echo $country; ?></p>
							                        </li>
							                    <?php } ?>
											</ul>
											<div id="sale-chart"></div>
										<?php } else { ?>
											<div class="empty-graph">
												<div class="text-center mt-5">
													<div class="d-flex justify-content-center align-items-center flex-column mt-5">
														 <i class="fas fa-exchange-alt fa-5x text-muted"></i>
														 <h3 class="text-muted"><?= __('admin.no_data_found') ?></h3>
													</div>
												</div>
											</div>
										<?php } ?>
									</div>
								</div>
							</div>
						</div>

						<div class="row ">
							<?php if($refer_status){ ?>
							<div class="col-sm-6 mb-5">
								<div class="card">
									<div class="card-body">
										<h4 class="text-center"><span class="pull-left"> <?= (int)$statistics['affiliate_user_count'] ?></span> <?= __('user.refered_user_by_country') ?></h4>
										<?php if((int)$statistics['affiliate_user_count'] > 0){ ?>
											<ul class="list-unstyled list-inline text-center">
							                    <?php $i = 0; foreach($statistics['affiliate_user'] as $country => $counts){ ?>
							                        <li class="list-inline-item">
							                            <p><i class="mdi mdi-checkbox-blank-circle <?php echo 'color-'.$i++ % 5 ; ?> mr-2"></i><?php echo $country; ?></p>
							                        </li>
							                    <?php } ?>
											</ul>
											<div id="affiliate_user-chart"></div>
										<?php } else { ?>
											<div class="empty-graph">
												<div class="text-center mt-5">
													<div class="d-flex justify-content-center align-items-center flex-column mt-5">
														 <i class="fas fa-exchange-alt fa-5x text-muted"></i>
														 <h3 class="text-muted"><?= __('admin.no_data_found') ?></h3>
													</div>
												</div>
											</div>
										<?php } ?>
									</div>
								</div>
							</div>
							<?php } ?>

							<div class="col-sm-6 mb-5">
								<div class="card">
									<div class="card-body">
										<h4 class="text-center"><span class="pull-left"> <?= (int)$statistics['client_user_count'] ?></span> <?= __('user.client_by_country') ?></h4>
										<?php if((int)$statistics['client_user_count'] > 0){ ?>
											<ul class="list-unstyled list-inline text-center">
							                    <?php $i = 0; foreach($statistics['client_user'] as $country => $counts){ ?>
							                        <li class="list-inline-item">
							                            <p><i class="mdi mdi-checkbox-blank-circle <?php echo 'color-'.$i++ % 5 ; ?> mr-2"></i><?php echo $country; ?></p>
							                        </li>
							                    <?php } ?>
											</ul>
											<div id="client_user-chart"></div>
										<?php } else { ?>
											<div class="empty-graph">
												<div class="text-center mt-5">
													<div class="d-flex justify-content-center align-items-center flex-column mt-5">
														 <i class="fas fa-exchange-alt fa-5x text-muted"></i>
														 <h3 class="text-muted"><?= __('admin.no_data_found') ?></h3>
													</div>
												</div>
											</div>
										<?php } ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="tab-pane " id="tab-menu_report_statistics">
				<div class="card">
					<div class="card-header">
						<div class="row">
		                    <div class="col-sm-3">
		                        <div class="form-group">
		                            <label class="control-label"><?= __('user.date') ?></label>
		                            <input autocomplete="off" type="text" name="date" value="" class="form-control daterange-picker">
		                        </div>
		                    </div>
		                    <div class="col-sm-2">
		                        <label class="control-label">&nbsp;</label>
		                        <div>
		                            <button class="btn btn-primary" onclick="table.ajax.reload();"> <i class="fa fa-search"></i> <?= __('user.search') ?></button>
		                            <button class="btn btn-primary export-excel" > <i class="fa fa-file-excel-o"></i> <?= __('user.export') ?></button>
		                        </div>
		                    </div>
		                </div>
					</div>
					<div class="card-body p-0">
						<div class="table-responsive">
							<table class="table table-striped table-bordered" id="table-report">
								<thead>
									<tr class="main-tr">
										<th></th>
										<th><?= __('user.affiliate') ?></th>
										
										<th colspan="2" class="text-center two-border"><?= __('user.clicks') ?></th>
										<th colspan="3" class="text-center two-border"><?= __('user.sale') ?></th>
										<th class="text-center two-border"><?= __('user.cpa') ?></th>
										
										<th colspan="2" class="text-center two-border"><?= __('user.total') ?></th>
									</tr>
									<tr class="sub-tr">
										<th>No</th>
										<th><?= __('user.affiliate_name') ?></th>

										<th -width="90px"><?= __('user.count') ?></th>
										<th -width="120px"><?= __('user.commission') ?></th>

										<th -width="90px"><?= __('user.count') ?></th>
										<th -width="90px"><?= __('user.total') ?></th>
										<th -width="120px"><?= __('user.commission') ?></th>
										<th -width="120px"><?= __('user.cpa') ?></th>
										<th -width="90px"><?= __('user.total_income') ?></th>
										<th -width="120px"><?= __('user.total_commission') ?></th>
									</tr>
								</thead>
								<tbody class="tiny-table"></tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="tab-pane" id="tab-menu_report_store_orders">
				<div class="card">
					<div class="card-header">
						<div class="row">
							<div class="col-sm-4">
								<div class="form-group">
									<label class="control-label"><?= __('user.status') ?></label>
									<select class="form-control filter_status">
										<option value=""><?= __('user.all'); ?></option>
										<?php foreach ($status as $key => $value) { ?>
											<option value="<?= $key ?>">
												<?php   
													if ($value == 'Received') {
														echo __('user.received');
													}elseif ($value == 'Complete') {
														echo __('user.complete');
													}elseif ($value == 'Total not match') {
														echo __('user.total_not_match');
													}elseif ($value == 'Denied') {
														echo __('user.denied');
													}elseif ($value == 'Expired') {
														echo __('user.expired');
													}elseif ($value == 'Failed') {
														echo __('user.failed');
													}elseif ($value == 'Processed') {
														echo __('user.processed');
													}elseif ($value == 'Refunded') {
														echo __('user.refunded');
													}elseif ($value == 'Reversed') {
														echo __('user.reversed');
													}elseif ($value == 'Voided') {
														echo __('user.voided');
													}elseif ($value == 'Canceled Reversal') {
														echo __('user.cancel_reversal');
													}elseif ($value == 'Waiting For Payment') {
														echo __('user.waiting_for_payment');
													}elseif ($value == 'Pending') {
														echo __('user.pending');
													}else{
														echo $value;
													}
												?>
											</option>
										<?php } ?>
									</select>
									 
								</div>
							</div>
							<div class="col-sm-4">
								<div class="form-group">
									<label class="control-label d-block">&nbsp;</label>
									<button class="btn btn-primary" onclick="getPage(1,this)"><?= __('user.search') ?></button>
								</div>
							</div>
						</div>
					</div>
					<div class="card-body">
						<div class="table-responsive">
							<section class="empty-div d-none">
								<div class="text-center mt-5">
									<div class="d-flex justify-content-center align-items-center flex-column mt-5">
										 <i class="fas fa-exchange-alt fa-5x text-muted"></i>
										 <h3 class="text-muted"><?= __('admin.no_data_found') ?></h3>
									</div>
								</div>
				            </section>
							<table class="table orders-table">
								<thead>
									<tr>
										<th width="80px">#</th>
										<th width="80px"><?= __('user.order_id') ?></th>
										<th><?= __('user.total') ?></th>
										<th><?= __('user.country') ?></th>
										<th><?= __('user.store') ?></th>
										<th><?= __('user.status') ?></th>
										<th><?= __('user.commission') ?></th>
										<th><?= __('user.commission_status') ?></th>
										<th><?= __('user.date') ?></th>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>
					</div>
					<div class="card-footer orders text-right" style="display: none;"> <div class="pagination"></div> </div>
				</div>

				<script type="text/javascript">
					 $(".orders-table").delegate(".toggle-child-tr","click",function(){
				        $tr = $(this).parents("tr");
				        $ntr = $tr.next("tr.detail-tr");

				        if($ntr.css("display") == 'table-row'){
				            $ntr.hide();
				            $(this).find("i").attr("class","bi bi-plus-circle");
				        }else{
				            $(this).find("i").attr("class","bi bi-dash-circle");
				            $ntr.show();
				        }
				    })
				    
					function getPage(page,t) {
						$this = $(t);
						var data ={
							page:page, 
							filter_status:$(".filter_status").val()
						}
				  
						$.ajax({
							url:'<?= base_url("usercontrol/store_orders") ?>/' + page,
							type:'POST',
							dataType:'json',
							data:data,
							beforeSend:function(){$this.btn("loading");},
							complete:function(){$this.btn("reset");},
							success:function(json){
								if(json['html']){
				                   $(".orders-table tbody").html(json['html']);
				                    $(".orders-table").show();
				                } else {
				                    $(".empty-div").removeClass("d-none");
				                    $(".orders-table").hide();
				                }
								
								if(json['pagination']){
									$(".card-footer.orders").show();
									$(".card-footer.orders .pagination").html(json['pagination'])
								}
							},
						})
					}

					$(".card-footer.orders .pagination").delegate("a","click", function(e){
						e.preventDefault();
						getPage($(this).attr("data-ci-pagination-page"),$(this));
					})

					getPage(1)
				</script>

			</div>
			<div class="tab-pane" id="tab-menu_report_logs">

				<div class="clearfix"></div>
				<br>
				<div class="card">
					<div class="card-header">
						<h4 class="card-title">Click Logs</h4>
					</div>
					<div class="card-body">
						<div class="table-responsive">
							<table class="table click-table">
								<thead>
									<tr>
										<th width="80px">#</th>
										<th width="80px"><?= __('user.click_id') ?></th>
										<th><?= __('user.website') ?></th>
										<th><?= __('user.ip') ?></th>
										<th><?= __('user.created_at') ?></th>
										<th><?= __('user.click_type') ?></th>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>
					</div>
					<div class="card-footer logs text-right" style="display: none;"> <div class="pagination"></div> </div>
				</div>


				<script type="text/javascript">
					 $(".click-table").delegate(".toggle-child-tr","click",function(){
				        $tr = $(this).parents("tr");
				        $ntr = $tr.next("tr.detail-tr");

				        if($ntr.css("display") == 'table-row'){
				            $ntr.hide();
				            $(this).find("i").attr("class","bi bi-plus-circle");
				        }else{
				            $(this).find("i").attr("class","bi bi-dash-circle");
				            $ntr.show();
				        }
				    })
				    
					function getLogPage(page,t) {
						$this = $(t);
						$.ajax({
							url:'<?= base_url("usercontrol/store_logs") ?>/' + page,
							type:'POST',
							dataType:'json',
							data:{page:page},
							beforeSend:function(){$this.btn("loading");},
							complete:function(){$this.btn("reset");},
							success:function(json){
								$(".click-table tbody").html(json['html']);
								$(".card-footer.logs").hide();
								
								if(json['pagination']){
									$(".card-footer.logs").show();
									$(".card-footer.logs .pagination").html(json['pagination'])
								}
							},
						})
					}

					$(".card-footer.logs .pagination").delegate("a","click", function(e){
						e.preventDefault();
						getLogPage($(this).attr("data-ci-pagination-page"),$(this));
					})

					getLogPage(1)
				</script>
				
			</div>
		</div>
	</div>
</div>

<script type="text/javascript" src="<?= base_url('assets/plugins/datatable') ?>/jquery.dataTables.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/plugins/datatable') ?>/jquery.dataTables.css?v=<?= av() ?>">
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/plugins/datatable') ?>/dataTables.bootstrap.min.css?v=<?= av() ?>">

<script src="<?= base_url('assets/plugins/datatable') ?>/moment.js"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/datatable') ?>/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/plugins/datatable') ?>/daterangepicker.css?v=<?= av() ?>" />

<script type="text/javascript">

	$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
		var hash = $(e.target).attr('href');
		localStorage.setItem("report_tab",hash)
		if(hash == '#tab-menu_statistics'){apply_chart()}
	});

	$(document).ready(function(){
		var hash = localStorage.getItem("report_tab")
		if (hash) { 
			if(hash == '#tab-menu_statistics'){apply_chart()}
			$('.nav-link[href="' + hash + '"]').tab('show'); 
		} else {
			apply_chart()
		}

		$( ".ip-details-flag" ).each(function( index ) {
		  $(this).tooltip({title: $(this).parent().find('.ip-details-flag-details').html(), html: true, placement: "top"});
		});
	});

	var colorss = ['#40a4f1', '#5b6be8', '#c1c5e2', '#e785da', '#00bcd2'];
	var is_apply = false;

	function apply_chart(){
		if(!is_apply){
			is_apply = true;
			if($("#clicks-chart").length){
				var donutData = [
					<?php $str = '';
						foreach($statistics['clicks'] as $country=>$counts){ $str .= '{label: "' . $country . '", value: ' . $counts . '},'; }
						echo $str;
					?>
				];
				Morris.Donut({
					element: 'clicks-chart',
					data: donutData,
					resize: true,
					colors: colorss,
				});
			}

			if($("#action_click-chart").length){
				var donutData = [
					<?php $str = '';
						foreach($statistics['action_clicks'] as $country=>$counts){ $str .= '{label: "' . $country . '", value: ' . $counts . '},'; }
						echo $str;
					?>
				];
				Morris.Donut({
					element: 'action_click-chart',
					data: donutData,
					resize: true,
					colors: colorss,
				});
			}

			if($("#sale-chart").length){
				var donutData = [
					<?php $str = '';
						foreach($statistics['sale'] as $country=>$counts){ $str .= '{label: "' . $country . '", value: ' . $counts . '},'; }
						echo $str;
					?>
				];
				Morris.Donut({
					element: 'sale-chart',
					data: donutData,
					resize: true,
					colors: colorss,
				});
			}

			if($("#affiliate_user-chart").length){
				var donutData = [
					<?php $str = '';
						foreach($statistics['affiliate_user'] as $country=>$counts){ $str .= '{label: "' . $country . '", value: ' . $counts . '},'; }
						echo $str;
					?>
				];
				Morris.Donut({
					element: 'affiliate_user-chart',
					data: donutData,
					resize: true,
					colors: colorss,
				});
			}

			if($("#client_user-chart").length){
				var donutData = [
					<?php $str = '';
						foreach($statistics['client_user'] as $country=>$counts){ $str .= '{label: "' . $country . '", value: ' . $counts . '},'; }
						echo $str;
					?>
				];
				Morris.Donut({
					element: 'client_user-chart',
					data: donutData,
					resize: true,
					colors: colorss,
				});
			}
		}
	}

	$('.daterange-picker').daterangepicker({
        opens: 'left',
        autoUpdateInput: false,
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        locale: {
            cancelLabel: 'Clear',
            format: 'DD-M-YYYY'
        }
    });
	$('.daterange-picker').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD-M-YYYY') + ' - ' + picker.endDate.format('DD-M-YYYY'));
    });
    $('.daterange-picker').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
	var table = $('#table-report').DataTable({
	    dom: 'Bfrtip',
	    ajax:{
	    	url:"<?= base_url('incomereport/get_data') ?>",
	    	data: function ( d ) {
				d.date     = $(".daterange-picker").val();
		  	},
	    	dataType:'json',
	    	type:'post',
	    },
	    buttons: [],
	    bFilter: false, 
        bInfo: false,
        processing: true,
        language: {
            'loadingRecords': '&nbsp;',
            'processing': 'Loading...'
        },
	});

	$(".export-excel").on('click',function(){
    	$this = $(this);
    	$.ajax({
    		url:'<?= base_url('incomereport/get_data') ?>?export=excel&filter=is_admin=1&date=' + $(".daterange-picker").val(),
    		type:'POST',
    		dataType:'json',
    		data: {
	    		date:$(".daterange-picker").val(),
	    	},
    		beforeSend:function(){
    			$this.btn("loading");
    		},
    		complete:function(){
    			$this.btn("reset");
    		},
    		success:function(json){
    			if (json['download']) {
    				window.location.href = json['download'];
    			}
    		},
    	})
    })
</script>