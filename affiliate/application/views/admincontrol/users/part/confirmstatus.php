<?php if(isset($invalid_order_status)){ ?>
<h5 class="text-center"><?= __('admin.wallet_change_status_order_is_pending') ?></h5>
<hr><br>
<div class="row">
	<div class="col-sm-4 mr-auto ml-auto"><button data-id='<?= $id ?>' class="btn close-modal btn-default btn-block"><?= __('admin.ok') ?></button></div> 
</div>
<?php } else { ?>

	<?= $transactions_details ?>

	<hr><br>
	<div class="row">
		<div class="col-sm-4"><button data-id='<?= $id ?>' class="btn close-modal btn-default btn-block"><?= __('admin.cancel') ?></button></div> 
		<div class="col-sm-4"><button class="btn btn-danger  btn-block" data-type='changeall' status-tran-confirm="<?= $id ?>"><?= __('admin.yes_change') ?></button></div> 
	</div>

	<script type="text/javascript">
		$("[status-tran-confirm]").click(function(){
			$this = $(this);
			$.ajax({
				url: '<?php echo base_url("admincontrol/wallet_change_status") ?>',
				type:'POST',
				dataType:'json',
				data:{
					confirm:$this.attr("data-type"),
					id:'<?= $tran->id ?>',
					val:'<?= $status ?>',
				},
				beforeSend:function(){$this.button('loading');},
				complete:function(){$this.button('reset');},
				success:function(json){
					if(json['success']){
						window.location.reload();
					}
				},
			})
		});
	</script>

<?php } ?>
