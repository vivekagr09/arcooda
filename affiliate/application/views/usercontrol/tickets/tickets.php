<link rel="stylesheet" href="<?= base_url('assets/css') ?>/jquery.dataTables.min.css">
<script src="<?= base_url('assets/js') ?>/jquery.dataTables.min.js" type="text/javascript" ></script>
<div class="row">
	<div class="col-sm-12">
		<div class="card dashboard-middle">
			<div class="card-body">
				<div class="d-md-flex justify-content-between align-items-center">
					<div class="col-xl-4">
						<div class="mini-stat clearfix bg-white">
							<div class="mini-stat-info text-center">
								<h6 class="mt-0 header-title"><?php echo __( 'user.total_tickets') ?> (<span id="total_tickets">0</span>)</h6>
							</div>
						</div>
					</div>
					<div class="col-xl-4">
						<div class="mini-stat clearfix bg-white">
							<div class="mini-stat-info text-center">
								<h6 class="mt-0 header-title"><?php echo __( 'user.total_open_tickets') ?> (<span id="total_open_tickets">0</span>)</h6>
							</div>
						</div>
					</div>
					<div class="col-xl-4">
						<div class="mini-stat clearfix bg-white">
							<div class="mini-stat-info text-center">
								<h6 class="mt-0 header-title"><?php echo __( 'user.total_close_tickets') ?> (<span id="total_close_tickets">0</span>)</h6>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>

		<div class="card mb-4">
			<div class="card-header">
				<div class="row">
					<div class="col-md-2 mt-4">
						<a href="<?=base_url('usercontrol/createticket')?>" class="btn btn-primary" title=""><?=__('user.create_new_ticket')?></a>
					</div>
					<div class="col-md-3">

						<label><?= __('user.ticket_status');?></label>
						<select name="" id="tickets_status" class="form-control">
							<option value=""><?=__('user.tickets_user_select_status')?></option>
							<?php foreach ($status as $key => $value): ?>
								<option value="<?=$key?>" ><?=$value?></option>
							<?php endforeach ?>
						</select>
					</div>
					<div class="col-md-3">

						<label><?= __('user.ticket_subject');?></label>
						<select name="" id="ticket_subject" class="form-control">
							<option value=""><?=__('user.ticket_subject_selection')?></option>
							<?php foreach ($subjects as $key => $subj): ?>
								<option value="<?=$subj['id']?>" ><?=$subj['subject']?></option>
							<?php endforeach ?>
						</select>
					</div>
					<div class="col-md-2">
						<label class="control-label"><?= __('user.date') ?></label>
						<input autocomplete="off" type="text" name="date" value="" id="date_filter" class="form-control daterange-picker">
					</div>	
				</div>
			</div>
			<div class="card-body">
				

				<div class="table-responsive" data-pattern="priority-columns">
					<table class="table table-striped table-hover" id="tbl_tickets_listing">
						<thead>
							<tr>
								<th><?= __('user.ticket_id') ?></th>
								<th><?= __('user.ticket_date') ?></th>
								<th><?= __('user.ticket_subject') ?></th>
								<th><?= __('user.ticket_status') ?></th>
								<th><?= __('user.ticket_last_update') ?></th>
								<th><?= __('user.actions') ?></th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="<?= base_url('assets/plugins/datatable') ?>/moment.js"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/datatable') ?>/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/plugins/datatable') ?>/daterangepicker.css" />
<script>

	$(document).ready(function()
	{
		function ticketslistingDatables() {
			$("#tbl_tickets_listing").dataTable({
				pageLength: 25,
				lengthMenu:[[25, 50, -1], [25, 50, "All"]],
				processing: true,
				serverSide: true,
				autoWidth: true,
				"search": "Search:",
				 sScrollY: '100%',
				serverMethod: "post",
				oLanguage: {
					sProcessing: "Loading...",
				},
				ajax: {
					url: '<?=base_url()?>' + "usercontrol/tickets",
					type: "POST",
					data: {
						range: $("#date_filter").val(),
						status: $('#tickets_status').val(),
						subject: $('#ticket_subject').val(),
					},
					cache: true,
				},
				order: [[4, "DESC"]],
				columns: [
				{ data: "ticket_id", targets: 0, class:'text-left' },
				{ data: "created_at", targets: 1 ,class:'text-left'},
				{ data: "subjectName", targets: 2,class:'text-left' },
				{ data: "status", targets: 3,class:'text-left' },
				{ data: "updated_at", targets: 4,class:'text-left' },
				{ data: "action", targets: 5, orderable:false,class:'text-left' }
				],
 				"language": 
                        {
                            "decimal":        "",
						    "emptyTable":     "<?php echo __( 'user.no_data_available_in_table'); ?>",
						    "info":           "<?php echo __( 'user.showing'); ?> _START_ to _END_ of _TOTAL_ <?php echo __( 'user.entries'); ?>",
						    "infoEmpty":      "<?php echo __( 'user.showing'); ?> 0 to 0 of 0 <?php echo __( 'user.entries'); ?>",
						    "infoFiltered":   "(filtered from _MAX_ total entries)",
						    "infoPostFix":    "",
						    "thousands":      ",",
						    "lengthMenu":     "<?php echo __( 'user.show'); ?> _MENU_ <?php echo __( 'user.entries'); ?>",
						    "loadingRecords": "<?php echo __( 'user.loading'); ?>",
						    "processing":     "<?php echo __( 'user.processing'); ?>",
						    "search":         "<?php echo __( 'user.search'); ?>",
						    "zeroRecords":    "<?php echo __( 'user.no_records_found'); ?>",
						    "paginate": {
						    "first":      "<?php echo __( 'user.first'); ?>",
						    "last":       "<?php echo __( 'user.last'); ?>",
						    "next":       "<?php echo __( 'user.next'); ?>",
						    "previous":   "<?php echo __( 'user.previous'); ?>"
						    },
						    "aria": {
						        "sortAscending":  ": activate to sort column ascending",
						        "sortDescending": ": activate to sort column descending"
						    }
                        }

			});
		}
		ticketslistingDatables();
 
 

		$(document).on('click','.closeTickets',function(e){
			e.preventDefault();
			if(confirm('are you sure ?')) {
				var ticket_id = $(this).data('id');
				$.ajax({
					url:'<?= base_url('usercontrol/closetickets') ?>',
					type:'POST',
					dataType:'json',
					data:{ticket_id:ticket_id},
					async:false,
					success:function(data){
						if(data.status){
							$("#tbl_tickets_listing").DataTable().destroy()
							ticketslistingDatables();
						}
					}
				})
			}
		});
		function getStaticData() {
			$.ajax({
				url:'<?= base_url('usercontrol/getStaticData') ?>',
				type:'POST',
				dataType:'json',
				async:false,
				success:function(data){
					console.log(data);
					if(data.length!=0) {
						$("#total_tickets").html(data.total)
						$("#total_open_tickets").html(data.totalopen)
						$("#total_close_tickets").html(data.totalclose)
						$("#total_tickets_subject").html(data.totalsubject)
					}
				},
			})
		}
		getStaticData();

		$("#tickets_status").change(function(event) {
			$("#tbl_tickets_listing").DataTable().destroy()
			ticketslistingDatables();
		}); 
		$("#ticket_subject").change(function(event) {
			$("#tbl_tickets_listing").DataTable().destroy()
			ticketslistingDatables();
		});

		

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
				format: 'DD-MM-YYYY'
			}
		});
		$('.daterange-picker').on('apply.daterangepicker', function(ev, picker) {
			$(this).val(picker.startDate.format('DD-MM-YYYY') + ' to ' + picker.endDate.format('DD-MM-YYYY'));
			$("#tbl_tickets_listing").DataTable().destroy()
			ticketslistingDatables();
		});
		$('.daterange-picker').on('cancel.daterangepicker', function(ev, picker) {
			$(this).val('');
			$("#tbl_tickets_listing").DataTable().destroy()
			ticketslistingDatables();
		});  

	})

</script>