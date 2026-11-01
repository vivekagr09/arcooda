<div class="card">
	<div class="card-header bg-secondary text-white">
		<h5 class="pull-left"><?= __('admin.categories') ?></h5>
            <div class="pull-right">
           <a id="toggle-uploader" class="btn btn-light" href="<?php echo base_url("admincontrol/store_category_add") ?>"><?= __('admin.add_category') ?>
           </a>
        </div>
	</div>

	<div class="card-body">
        <div class="dimmer">
        	<div class="loader"></div>
        	<div class="dimmer-content">
				<div class="table-responsive m-0">
					<table class="table orders-table">
						<thead>
							<tr>
								<th width="80px">#</th>
								<th width="80px"><?= __('admin.id') ?></th>
								<th><?= __('admin.name') ?></th>
								<th><?= __('admin.parent') ?></th>
								<th><?= __('admin.total_product') ?></th>
								<th><?= __('admin.date') ?></th>
								<th width="180px">#</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
        	</div>
        </div>
	</div>
	<div class="card-footer text-right" style="display: none;"> <div class="pagination"></div> </div>
</div>

<script type="text/javascript">

	$(document).delegate("[product-category]",'click',function(){
  		$this = $(this);

  		var data = {};
  		data['category_id'] = $this.attr("product-category");

  		$.ajax({
  			url:'<?= base_url('admincontrol/product_logs') ?>',
  			type:'POST',
  			dataType:'json',
  			data:data,
  			beforeSend:function(){$this.btn("loading");},
  			complete:function(){$this.btn("reset");},
  			success:function(json){
  				if(json['html']){
  					$("#log-widzard").modal({
						backdrop: 'static',
						keyboard: false
					});
					$("#log-widzard").html(json['html']);
				}
  			},
  		})
  	})

	function getPage(page,t) {
		$this = $(t);
		var data ={
			page:page,
			filter_status:$(".filter_status").val()
		}
		$.ajax({
			url:'<?= base_url("admincontrol/store_category") ?>/' + page,
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