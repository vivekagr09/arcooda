<link href="<?= base_url(); ?>assets/css/datepicker.css" rel="stylesheet" type="text/css" />
<script src="<?= base_url(); ?>assets/js/bootstrap-datepicker.js"></script>

<div class="row">
	<div class="col-12">
		<div class="card all-transaction">
			<div class="card-header">
				<form action="<?= base_url('admincontrol/uncompleted_payments') ?>" method="get">
					<div class="form-group">
						<select class="form-control" name="module">
							<option value=""><?= __('admin.module') ?></option>
							<?php foreach ($payment_module as $key => $value): ?>
								<option value="<?= $key ?>"><?= __('admin.'.$value) ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="form-group">
						<select class="form-control" name="user">
							<option value=""><?= __('admin.user') ?></option>
							<?php foreach ($users as $value): ?>
								<option value="<?= $value['id'] ?>"><?= $value['username'] ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="form-group">
						<input type="text" class="form-control datepicker" name="date" placeholder="<?= __('admin.date') ?>">
					</div>
				</form>
			</div>
			<div class="card-content">
				<?= $html ?>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$('.datepicker').datepicker({
		autoclose: true,
		todayHighlight: true,
		format: "dd-mm-yyyy"
	});

	$('.datepicker').each(function() {
		var d = $(this).val().split("-");
		if (d[0]) {
			var date = d[1] + "-" + d[2] + "-" + d[0];
			$(this).datepicker('update', new Date(date))
		} else {
			$(this).val('');
		}
	});

	$("select[name='module'],select[name='user']").on('change', function() {
		callAjaxForFilter();
	});

	$('.datepicker').datepicker().on('changeDate', function(ev) {
		callAjaxForFilter();
	});

	$("input[name='date']").on('keyup', function() {
		if ($(this).val().length == 0)
			callAjaxForFilter();
	});

	$(document).on('click', '.pagination a', function(e) {
		e.preventDefault();

		let page = $(this).data('ci-pagination-page');
		callAjaxForFilter(page);
	});

	function callAjaxForFilter(page = 0) {
		$.ajax({
			url: "<?= base_url("admincontrol/uncompleted_payments") ?>",
			type: 'post',
			dataType: 'html',
			data: {
				module: $("select[name='module']").val(),
				user: $("select[name='user']").val(),
				date: $("input[name='date']").val(),
				page: page,
				ajax: 1
			},
			success: function(html) {
				$(".all-transaction .card-content").html(html);
			},
		});
	}
</script>
