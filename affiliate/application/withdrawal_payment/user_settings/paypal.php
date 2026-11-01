
<script type="text/javascript">
	$("#payment-form-paypal").submit(function(){
		$this = $(this);
		$.ajax({
			url:'<?= base_url('payment/call_payment_function/paypal/saveUserSubmit') ?>',
			type:'POST',
			dataType:'json',
			data:$("#payment-form-paypal").serialize(),
			beforeSend:function(){
				$this.find('.btn-submit').btn("loading");
				$this.find('.btn-submit').attr("disabled","disabled");
			},
			complete:function(){
				$this.find('.btn-submit').btn("reset");
				$this.find('.btn-submit').removeAttr("disabled");
			},
			success:function(json){
				$container = $this;
				$container.find(".is-invalid").removeClass("is-invalid");
				$container.find("span.invalid-feedback").remove();
				$this.find('.btn-submit').removeAttr("disabled");

				if (json['success']) {
					$("#withdrawal-payments").modal("hide");

					Swal.fire({
						title: 'Success',
						text: "<?= __('admin.withdrawal_request_sent_successfully') ?>",
						icon: 'success',
					}).then((result) => {
						window.location.reload();
					})
				}
				
				if(json['errors']){
				    $.each(json['errors'], function(i,j){
				        $ele = $container.find('[name="'+ i +'"]');
				        if($ele){
				            $ele.addClass("is-invalid");
				            if($ele.parent(".input-group").length){
				                $ele.parent(".input-group").after("<span class='invalid-feedback'>"+ j +"</span>");
				            } else{
				                $ele.after("<span class='invalid-feedback'>"+ j +"</span>");
				            }
				        }
				    })
				}
			},
		})
		return false;
	})
</script>