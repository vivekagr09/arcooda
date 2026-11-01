<div class="clearfix"></div>
<div class="card">
	<div class="card-header bg-secondary text-white">
		<h5 class="pull-left"><?= __('admin.integration_category') ?></h5>
		<div class="pull-right">
			<a id="toggle-uploader" href="<?= base_url('integration/integration_category_add') ?>" class="btn btn-light"><?= __('admin.add_category') ?></a>
		</div>
	</div>
	<div class="card-body p-0">
        <div class="dimmer">
        	<div class="loader"></div>
        	<div class="dimmer-content">
<div class="table-responsive m-0">
	<table class="table table-striped table-hover orders-table">
		<thead>
			<tr>
				<th scope="col"><?= __('admin.id') ?></th>
				<th scope="col"><?= __('admin.name') ?></th>
				<th scope="col"><?= __('admin.parent') ?></th>
				<th scope="col"><?= __('admin.date') ?></th>
				<th scope="col">#</th>
			</tr>
		</thead>
		<tbody>
        </tbody>
	</table>
</div>

        	</div>
        </div>
	</div>
	<div class="card-footer text-right" style="display: none;"> <div class="pagination"></div> </div>
</div>

<script type="text/javascript">

	function getPage(page,t) {
		$this = $(t);
		var data ={
			page:page,
			filter_status:$(".filter_status").val()
		}
		$.ajax({
			url:'<?= base_url("integration/integration_category") ?>/' + page,
			type:'POST',
			dataType:'json',
			data:data,
			beforeSend:function(){
				$this.btn("loading");
				$(".dimmer").addClass("active");
			},
			complete:function(){
				$this.btn("reset");
				$(".dimmer").removeClass("active");
			},
			success:function(json){
				$(".orders-table tbody").html(json['html']);
				$(".card-footer").hide();
				
				if(json['pagination']){
					$(".card-footer").show();
					$(".card-footer .pagination").html(json['pagination'])
				}
			},
		})
	}

	$(".card-footer .pagination").delegate("a","click", function(e){
		e.preventDefault();
		getPage($(this).attr("data-ci-pagination-page"),$(this));
	})

	getPage(1)
</script>