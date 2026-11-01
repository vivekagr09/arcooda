	<small class="text-muted">change position by simply drag and drop rows</small>
	<table class="table-hover table-striped table">
		<thead>
			<tr>
				<th><?= __('admin.title') ?></th>
				<th width="450"><?= __('admin.category') ?></th> 
				<th><?= __('admin.status') ?></th>
				<th><?= __('admin.action')?></th>
			</tr>
		</thead>

		<tbody data-whe_column="id" data-pos_column="position" data-table="tutorial_pages" class="sortable">

			<?php if(empty($tutorials)){ ?>
				<tr>
				    <td colspan="100%" class="text-center mt-5">
				        <div class="d-flex justify-content-center align-items-center flex-column">
				            <i class="fas fa-exchange-alt fa-5x text-muted"></i>
				            <h3 class="text-muted"><?= __('admin.no_data_found') ?></h3>
				        </div>
				    </td>
				</tr>
			<?php } ?>

			<?php 


			foreach ($tutorials as  $tutorial) { ?>

			<tr data-id="<?= $tutorial['id'] ?>" style="background-color:#FFF!important; cursor: move;">

				<td><?= $tutorial['title'] ?></td>

				<td><?= $tutorial['name'] ?></td>
				
				<td><?= ($tutorial['status'] == 1) ?

					'<lable class="badge bg-success">'.__('admin.active').'</lable>' :

					'<lable class="badge bg-secondary">'.__('admin.inactive').'</lable>' ?>

				</td>

				<td>

					<a class="btn btn-primary btn-sm" href="<?= base_url('admincontrol/manage_tutorial/'. $tutorial['id']) ?>"><i class="fa fa-edit"></i></a>

					<a class="btn confirm btn-danger btn-sm" href="<?= base_url('admincontrol/deleteTutorial/'. $tutorial['id']) ?>" onclick="return onDeleteReview(<?= $tutorial['id'] ?>);"><i class="fa fa-trash"></i></a>
 

				</td>

			</tr>

			<?php } ?>

		</tbody>

	</table>

<script type="text/javascript">
	function onDeleteReview($rating_id)
	{
		if(!confirm("<?= __('admin.are_you_sure') ?>")) 
		return false;
		else
		return true;	 
	}

	

	 $(function() {

		$( ".sortable" ).sortable({

			update: function( event, ui ) {

				let positions = [];

				$(this).children('tr').each(function () {

					if($(this).data('id') != null) {

						positions.push($(this).data('id'));
					}

				});

				$.ajax({

					url: "<?= base_url('themes/change_positions')  ?>",

					type: "POST",

					dataType: "json",

					data: {table:$(this).data('table'), whe_column:$(this).data('whe_column'), pos_column:$(this).data('pos_column'),positions:JSON.stringify(positions)},

					success: function (response) {	
					}

				});

			}

		});

		$( ".sortable" ).disableSelection();

	});
</script>