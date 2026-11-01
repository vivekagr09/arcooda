<div class="card">
	<div class="card-header bg-secondary text-white">
		<h5><?= __('admin.click_logs') ?></h5>
	</div>
	<div class="card-body">
		<div class="table-responsive">
			<table class="table click-table btn-part">
				<thead>
					<tr>
						<th>#</th>
						<th><?= __('admin.click_id') ?></th>
						<th><?= __('admin.website') ?></th>
						<th><?= __('admin.ip') ?></th>
						<th><?= __('admin.created_at') ?></th>
						<th><?= __('admin.click_type') ?></th>
						<th><?= __('admin.custom_data') ?></th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
	</div>
	<div class="card-footer text-end" style="display: none;">
		<div class="pagination"></div>
	</div>
</div>



<script type="text/javascript">
	 $(".click-table").delegate(".toggle-child-tr","click",function(){
        $tr = $(this).parents("tr");
        $ntr = $tr.next("tr.detail-tr");

        if($ntr.css("display") == 'table-row'){
            $ntr.hide();
            $(this).find("i").attr("class","fa fa-plus");
        }else{
            $(this).find("i").attr("class","fa fa-minus");
            $ntr.show();
        }
    })
    
	function getPage(page,t) {
		$this = $(t);
		$.ajax({
			url:'<?= base_url("admincontrol/store_logs") ?>/' + page,
			type:'POST',
			dataType:'json',
			data:{page:page},
			beforeSend:function(){$this.btn("loading");},
			complete:function(){$this.btn("reset");},
			success:function(json){
				$(".click-table tbody").html(json['html']);
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