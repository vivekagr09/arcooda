<style type="text/css">
	.user-table .accordian-body .row div {
		white-space: normal !important;
	}
</style>
<link rel="stylesheet" href="<?= base_url('assets/css') ?>/jquery.dataTables.min.css">
<script src="<?= base_url('assets/js') ?>/jquery.validate.min.js" type="text/javascript" ></script>
<script src="<?= base_url('assets/js') ?>/jquery.dataTables.min.js" type="text/javascript" ></script>
<div class="modal fade" id="importUsersModel" role="dialog">

	<div class="modal-dialog modal-md">

		<div class="modal-content">

			<div class="modal-header">

				<h4 class="modal-title"><?= __('admin.add_ticket_subject') ?></h4>

				<button type="button" class="close btn-close" data-bs-dismiss="modal">&times;</button>

				<a href="" class="close hidden-close d-none">&times;</a>

			</div>

			<div class="modal-body">

				<form id="frm_addsubject">

					<div class=" form-group">
						<label><?= __('admin.ticket_subject');?></label>
						<input type="text" name="subject" id="tssubject" placeholder="<?= __('admin.ticket_subject');?>" class="form-control">
						<input type="hidden" name="id" value="0" id="tceditid">
					</div>
					<div class="form-group float-right">
						<button class="btn btn-primary" type="submit"><?=__('admin.add')?></button>
					</div>

				</form>

			</div>

		</div>

	</div>

</div>



<div class="row">

	<div class="col-12">

		<div class="card">

			<div class="card m-b-30">

				<div class="card-body">


						<div class="col-sm-12 float-right">
							<div class="form-group">
								<label class="control-label d-block">&nbsp;</label>
								<div>
									<div class="btn-group mb-1 d-inline-block btn-group-md" role="group" aria-label="Export/Import Users">
										<button type="button" class="btn btn-dark " id="btnAddSubject" > <i class="fa fa-plus"></i> <?= __('admin.add_ticket_subject') ?></button>
									</div>

								</div>
							</div>
						</div>
						<div class="table-responsive b-0" data-pattern="priority-columns">
							<table id="tbl_tickets_subject" class="table  table-striped">
								<thead class="bg-blue">
									<tr>
										<th><?= __('admin.tickets_sr_no') ?></th>
										<th><?= __('admin.ticket_subject') ?></th>
										<th><?= __('admin.action')?></th>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>


				</div>

			</div>

		</div>

	</div>

</div>

<script type="text/javascript" async="">

	$(document).ready(function() {

		
		$("#btnAddSubject").click(function(e){
			e.preventDefault();
			$("#importUsersModel").find('.modal-title').text('Add Task Subject');
			$("#frm_addsubject").find('button').text('Add')
			$("#tceditid").val(0);
			$("#tssubject").val('');
			$("#importUsersModel").modal('show');

		});

		$("#frm_addsubject").validate({
			rules:{
				'subject':{required:true}
			},
			messages:{
				'subject':{
					"required":'Add Ticket Subject required'
				}
			},
			submitHandler: function(form, event) {
				event.preventDefault();
				$.ajax({
					url: '<?=base_url()?>'+'Tickets/addticketssubject',
					type: "POST",
					data: $("#frm_addsubject").serialize(),
					dataType: 'json',
					beforeSend: function() {

					},
					success: function(result) {

						if(result.status){
							$('#tbl_tickets_subject').DataTable().destroy();
							ticketsSubjectDatables();
							$("#importUsersModel").modal('hide');	
						} else {
							alert(result.message)
						}
					},
					error: function() {
						;
					},
					complete: function() {

					}
				});
			},

		})
		$(document).on('click','.edit',function(e){
			e.preventDefault();
			var id = $(this).data('id');
			var title = $(this).data('title');
			$("#tceditid").val(id);

			$("#tssubject").val(title);

			$("#importUsersModel").modal('show');
			$("#importUsersModel").find('.modal-title').text('Update Task Subject');
			$("#frm_addsubject").find('button').text('Update')

		});
		$(document).on('click', '.removets', function(e) {
			e.preventDefault();
			if(confirm('<?= __('admin.are_you_sure')?>')){
				var id = $(this).data('id');
				var $that = $(this);
				$.ajax({
					url:'<?= base_url('tickets/actiontasksubject') ?>',
					type:'POST',
					dataType:'json',
					data:{id:id,action:1},
					async:false,
					success:function(data){
						if(data.status) {
							$('#tbl_tickets_subject').DataTable().destroy();
							ticketsSubjectDatables();	
						}
						else {
							alert(data.message)
						}
					},
				});
			}
		});

		function ticketsSubjectDatables() {
			$("#tbl_tickets_subject").dataTable({
				pageLength: 10,
				lengthMenu:[[ 10, 25, 50, -1], [10, 25, 50, "All"]],
				processing: true,
				serverSide: true,
				 sScrollY: '100%',
				serverMethod: "post",
				oLanguage: {
					sProcessing: "Loading...",
				},
				ajax: {
					url: '<?=base_url()?>' + "tickets/getticketssubject",
					type: "POST",
					cache: true,
				},
				order: [[0, "DESC"]],
				columns: [
				{ data: "id", targets: 0,  },
				{ data: "subject", targets: 1 },
				{ data: "action", targets: 2,orderable:false }
				],
			});
		}
		ticketsSubjectDatables();
	});
</script>

