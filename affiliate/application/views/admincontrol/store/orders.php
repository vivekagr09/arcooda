<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/wallet.css') ?>">

<div class="row">
	<div class="col-12">
	    <div class="card">
	        <div class="card-header bg-secondary text-white">
	            <div class="row">
	                <div class="col-sm-6">
	                    <h5><?= __('admin.orders') ?></h5>
	                </div>
	                <div class="col-sm-6">
						<div class="row">
						    <div class="col-sm-6">
						        <div class="form-group mb-0">
						            <select class="form-select filter_status">
						                <option value=""><?= __('admin.all') ?></option>
						                <?php foreach ($status as $key => $value) { ?>
						                    <option value="<?= $key ?>"><?= $value ?></option>
						                <?php } ?>
						            </select>
						        </div>
						    </div>
						    <div class="col-sm-6">
						        <button id="toggle-uploader" class="btn btn-light" onclick="getPage(1, this)"><?= __('admin.search') ?></button>
						    </div>
						</div>
	                </div>
	            </div>
	        </div>

			<div class="card-body">
			    <div class="table-responsive">
			        <table class="table orders-table">
			            <thead>
			                <tr>
			                    <th><?= __('admin.order_id') ?></th>
			                    <th><?= __('admin.total') ?></th>
			                    <th><?= __('admin.country') ?></th>
			                    <th><?= __('admin.store') ?></th>
			                    <th><?= __('admin.status') ?></th>
			                    <th><?= __('admin.commission') ?></th>
			                    <th><?= __('admin.date') ?></th>
			                    <th><?= __('admin.action') ?></th>
			                </tr>
			            </thead>
			            <tbody>
			                <tr>
			                    <td colspan="100%" class="text-center">
			                        <h3 class="text-muted py-4"><?= __("admin.loading_orders_data_text") ?></h3>
			                        <h5 class="text-muted py-4"><?= __("admin.not_taking_longer") ?></h5>
			                    </td>
			                </tr>
			            </tbody>
			        </table>
			    </div>
			</div>
			
	        <div class="card-footer text-end" style="display: none;">
	            <div class="pagination"></div>
	        </div>
    	</div>

	    <div class="modal fade" id="modal-confirm">
	        <div class="modal-dialog">
	            <div class="modal-content">
	                <div class="modal-body"></div>
	            </div>
	        </div>
	    </div>

	    <div class="modal fade" id="modal-order-detail">
	        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
	            <div class="modal-content">
	                <div class="modal-header">
	                    <h5 class="modal-title"><?= __('admin.order_details') ?></h5>
	                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	                </div>
	                <div class="modal-body"></div>
	                <div class="modal-footer">
	                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal"><?= __('admin.close') ?></button>
	                </div>
	            </div>
	        </div>
	    </div>

	    <div id="wallet-details-model" class="modal fade" tabindex="-1" role="dialog">
	        <div class="modal-dialog modal-lg" role="document">
	            <div class="modal-content">
	                <div class="modal-header">
	                    <h5 class="modal-title"><?= __('admin.order_details') ?></h5>
	                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	                </div>
	                <div class="modal-body"></div>
	            </div>
	        </div>
	    </div>
	</div>
</div>



<script type="text/javascript">
    $(document).on('click', '.order-transactions-toggle', function(){
        $this = $(this);
        
        var uniqkey =$(this).data('order_type')+'-'+$(this).data('order_id')
        
        if($($this).hasClass("shown-transactions")){
            $('tr.'+uniqkey).remove();
            $(this).text('<?= __('admin.show_transactions') ?>');
		    $(this).removeClass("shown-transactions");
        } else {
    		$.ajax({
    			url:'<?= base_url("admincontrol/get_orders_transactions") ?>/'+$(this).data('order_type')+'/'+$(this).data('order_id')+'/order_page',
    			type:'GET',
    			dataType:'html',
    			beforeSend:function(){$this.btn("loading");},
    			complete:function(){
    			    $this.btn("reset");
    			    $($this).text('<?= __('admin.hide_transactions') ?>');
    			    $($this).addClass("shown-transactions");
    			},
    			success:function(html){
			        $($this).closest('tr').after(html);
			        
			        $(document).delegate('.wallet-popover','click', function(){
                		var html = $(this).parents("tr").find(".dpopver-content").html();
                        $(this).attr('data-content',html);
                	    if($('.popover').hasClass('show')){
                	        $('.popover').remove()
                	    } else {
                	        $(this).popover('show');
                	    }
                	});
                
                	$('html').on('click', function(e) {
                	  if (typeof $(e.target).data('original-title') == 'undefined' &&
                	     !$(e.target).parents().is('.popover.in')) {
                	    $('[data-original-title]').popover('hide');
                	  }
                	});
                
                	$(document).ready(function(){
                		$(".wallet-popover").popover({
                	        placement : 'right',
                		    html : true,
                	    });
                	})

    			},
    		});
        }
    });

	$(".orders-table").delegate(".toggle-child-tr","click",function(){
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
		var data ={
			page:page,
			filter_status:$(".filter_status").val(),
			action:'order_page',
		}
		$.ajax({
			url:'<?= base_url("admincontrol/store_orders") ?>/' + page,
			type:'POST',
			dataType:'json',
			data:data,
			beforeSend:function(){$this.btn("loading");},
			complete:function(){$this.btn("reset");},
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

	$(document).delegate(".remove-order", "click", function(){
		$this = $(this);
		$.ajax({
			url: '<?php echo base_url("admincontrol/info_remove_order") ?>',
			type:'POST',
			dataType:'json',
			data:{id:$this.attr("data-order_id"), type:$this.attr("data-order_type")},
			beforeSend:function(){ $this.button("loading"); },
			complete:function(){ $this.button("reset"); },
			success:function(json){
				$("#modal-confirm .modal-body").html(json['html']);
				$("#modal-confirm").modal("show");
			},
		})
	})

	$("#modal-confirm .modal-body").delegate("[delete-order-confirm]","click",function(){
		$this = $(this);
		$.ajax({
			url: '<?php echo base_url("admincontrol/confirm_remove_order") ?>',
			type:'POST',
			dataType:'json',
			data:{
				id:$this.attr("delete-order-confirm"), 
				sale_commission: $('input[name="sale_commission"]').prop('checked'),
				order_type: $('input[name="order_type"]').val()
			},
			beforeSend:function(){ $this.button("loading"); },
			complete:function(){ $this.button("reset"); },
			success:function(json){
				window.location.reload();
			},
		})
	})

	$(document).delegate(".order-detail", "click",function(){
		let order_type = $(this).data('order_type');
		let order_id = $(this).data('order_id');

		if(order_type == 'ex')
			$("#modal-order-detail .modal-dialog").removeClass('modal-xl').addClass('modal-lg');
		else 
			$("#modal-order-detail .modal-dialog").removeClass('modal-lg').addClass('modal-xl');
			
		let template = jsOrders[order_type][order_id];

		$("#modal-order-detail .modal-body").html('');
		$("#modal-order-detail .modal-body").html(template);
		$("#modal-order-detail").modal("show");
	})

	$(document).on('click', '.view-tran-details', function () {
	    let data = {
	        type : $(this).data('comm_from'),
	        ref1 : $(this).data('ref_id_1'),
	        ref2 : $(this).data('ref_id_2')
	    };

	    $.ajax({
	        url:'<?= base_url('admincontrol/getOrderDetails') ?>',
	        type:'POST',
	        dataType:'html',
	        data:data,
	        success:function(response){
	            $('#wallet-details-model .modal-body').html(response);
	            $('#wallet-details-model').modal('show');
	        },
	    });
	});
</script>