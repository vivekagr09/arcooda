<link href="<?php echo base_url(); ?>assets/css/datepicker.css" rel="stylesheet" type="text/css" />
<script src="<?php echo base_url(); ?>assets/js/bootstrap-datepicker.js"></script>

	    <div class="card all-transaction">
			<div class="card-header">
				<form action="<?= base_url('usercontrol/all_transaction') ?>" method="get">
					<div class="col-md-12">
						<div class="row">
							<div class="col-2">
								<select class="form-control" name="module">
									<option value=""><?= __('user.module') ?></option>
									<?php foreach($payment_module as $key => $value): ?>
										<option value="<?= $value ?>">
											<?php  
												if ($value == 'store') {
													echo __('user.store');
												}elseif ($value == 'membership') {
													echo __('user.membership');
												}elseif ($value == 'deposit') {
													echo __('user.deposit');
												}
											?>
										</option>
									<?php endforeach ?>
								</select>
							</div>
							<div class="col-2">
								<input type="text" class="form-control datepicker" name="date" placeholder="<?= __('user.date') ?>">
							</div>
							<div class="col-2">
								<select class="form-control" name="payment_gateway">
									<option value=""><?= __('user.payment_gateway') ?></option>
									<?php foreach($filter_field['payment_gateway'] as $key => $value): ?>
										<option value="<?= $key ?>"><?= $value ?></option>
									<?php endforeach ?>
								</select>
							</div>
							<div class="col-2">
								<select class="form-control" name="status">
									<option value=""><?= __('user.status') ?></option>
									<?php foreach($filter_field['status'] as $key => $value): ?>
										<option value="<?= $key ?>"><?= $value ?></option>
									<?php endforeach ?>
								</select>
							</div>
							<div class="col-2">
								<input type="text" class="form-control" name="transaction" placeholder="<?= __('user.transaction') ?>">
							</div>
							<div class="col-2">
								<a href="<?= base_url('usercontrol/uncompleted_payments') ?>" class="btn btn-primary">
			          				<?= __('user.menu_uncompleted_payments') ?></a>
								</a>
							</div>
						</div>
					</div>

					<div class="col-md-6 mt-3">
						<div class="row">
							<div class="col-3">
								<a href="<?= base_url('usercontrol/all_transaction_export_to_excel') ?>" class="btn btn-primary export-excel">
									<?= __('user.all_transaction_export_to_excel') ?>
								</a>
							</div>
							<div class="col-3">
								<a href="<?= base_url('usercontrol/all_transaction_export_to_pdf') ?>" class="btn btn-primary export-pdf">
									<?= __('user.all_transaction_export_to_pdf') ?>
								</a>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>

		<div class="card-content">
			<?= $html ?>
		</div>


<script type="text/javascript">
	$('.datepicker').datepicker({ 
        autoclose: true, 
        todayHighlight: true,
        format:"dd-mm-yyyy"
    })

    $('.datepicker').each(function(){
        var d= $(this).val().split("-");
        if(d[0]){
            var date = d[1]  + "-" + d[2] + "-" + d[0];
            $(this).datepicker('update', new Date(date))
        } else{ 
        	$(this).val(''); 
        }
    })

    $("select[name='module'],select[name='user'],select[name='payment_gateway'],select[name='status']").on('change',function(){
    	callAjaxForFilter();
    })

    $('.datepicker').datepicker().on('changeDate',function(ev){
        callAjaxForFilter();
    });

    $("input[name='date']").on('keyup',function(){
    	if($(this).val().length == 0)
    		callAjaxForFilter();
    })

    $("input[name='transaction']").on('keyup',function(){
    	if($(this).val().length == 0 || $(this).val().length > 3)
    		callAjaxForFilter();
    })

    $(document).on('click','.pagination a',function(e){
    	e.preventDefault();

    	let page = $(this).data('ci-pagination-page');
    	callAjaxForFilter(page);
    })

    function callAjaxForFilter(page = 0){
    	$.ajax({
			url: $('.all-transaction form').attr('action'),
			type:'post',
			dataType:'html',
			data:{
				module: $("select[name='module']").val(),
				date: $("input[name='date']").val(),
				payment_gateway: $("select[name='payment_gateway']").val(),
				status: $("select[name='status']").val(),
				transaction: $("input[name='transaction']").val(),
				page: page
			},
			success:function(html){
				$(".card-content").html(html);
			},
		})
    }

    $('.all-transaction .export-excel').on('click',function(e){
    	e.preventDefault();

    	let data = {
    		module: $("select[name='module']").val(),
			date: $("input[name='date']").val(),
			payment_gateway: $("select[name='payment_gateway']").val(),
			status: $("select[name='status']").val(),
			transaction: $("input[name='transaction']").val()
    	};
    	
    	let queryData = encodeQueryData(data);		

    	let url = $(this).attr('href') + '?' + queryData; 
    	window.open(url,'_target');
    })

    $('.all-transaction .export-pdf').on('click',function(e){
    	e.preventDefault();

    	let data = {
    		module: $("select[name='module']").val(),
			date: $("input[name='date']").val(),
			payment_gateway: $("select[name='payment_gateway']").val(),
			status: $("select[name='status']").val(),
			transaction: $("input[name='transaction']").val()
    	};
    	
    	let queryData = encodeQueryData(data);		

    	let url = $(this).attr('href') + '?' + queryData; 
    	window.open(url,'_target');
    })

    function encodeQueryData(data){
	   const ret = [];
	   for (let d in data)
	     ret.push(encodeURIComponent(d) + '=' + encodeURIComponent(data[d]));
	   return ret.join('&');
	}
</script>