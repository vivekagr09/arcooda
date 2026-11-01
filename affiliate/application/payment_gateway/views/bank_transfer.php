<?php if($gatewayData['module'] == 'store'){ ?>
	<?php 
		$bank_names = [];

		if(isset($settingData['bank_names']) && ! empty($settingData['bank_names'])){
			$bank_names = (array)json_decode($settingData['bank_names'],1);
		} 
	?>
	<div class="form-group">
		<label class="control-label">Choose Bank</label>
		<select name="bank_method" class="form-control">
			<option value="0"><?= (isset($bank_names[0]) && !empty($bank_names[0])) ? $bank_names[0]  : substr($settingData['bank_details'],0,50)."..." ?></option>
			<?php
				if(isset($settingData['additional_bank_details'])){
					$additional_bank_details = (array)json_decode($settingData['additional_bank_details'],1);
					foreach ($additional_bank_details as $key => $value) {
						$Bname = (isset($bank_names[$key+1]) && !empty($bank_names[$key+1])) ? $bank_names[$key+1]  : substr($value,0,50)."...";
						echo '<option value="'. ($key+1) .'">'. $Bname .'</option>';
					}
				}
			?>
		</select>
	</div>

	<div class="checkout-bank-details">
		<?php $Bname = (isset($bank_names[0]) && !empty($bank_names[0])) ? $bank_names[0]."\r\n"  : ""; ?>
		<pre class="well d-none"><?= $Bname.$settingData['bank_details'] ?></pre>
		<?php
			if(isset($settingData['additional_bank_details'])){
				$additional_bank_details = (array)json_decode($settingData['additional_bank_details'],1);
				foreach ($additional_bank_details as $key => $value) {
					$Bname = (isset($bank_names[$key+1]) && !empty($bank_names[$key+1])) ? $bank_names[$key+1]."\r\n"  : "";
					$value = $Bname.$value;
					echo '<pre class="well d-none">'. $value .'</pre>';
				}
			}
		?>
	</div>

	<?php if($settingData['proof'] == 1){ ?>
		<div class="form-group">
			<label class="control-label"><?= __('user.payment_proof') ?></label>
			<input type="file" name="payment_proof" class="form-control">
		</div>
	<?php } ?>
	<?php if($settingData['proof'] == 2){ ?>
		<div class="form-group">
			<label class="control-label"><?= __('user.payment_proof') ?></label>
			<input type="file" name="payment_proof" class="form-control" required>
		</div>
	<?php } ?>

	<button type="button" class="btn btn-default" onclick='backCheckout()'><?= __('user.back') ?></button>
	<button id="button-confirm" class="btn btn-primary"><?= __('user.confirm') ?></button>

	<script type="text/javascript">
		$("select[name=bank_method]").change(function(){
			var val = $(this).val();
			$('.checkout-bank-details .well').addClass('d-none');
			$('.checkout-bank-details .well').eq(val).removeClass('d-none');
		});

		$("select[name=bank_method]").val('0').trigger("change");

		$("#button-confirm").click(function(){
			$this = $(this);
			
			let ajaxData = $('[name^="comment"]').serialize();
			<?php if($settingData['proof'] == 1){ ?>
				ajaxData += "&payment_proof=" + $('input[name="payment_proof"]').val();
			<?php } ?>

			<?php if($settingData['proof'] == 2){ ?>
				if($('input[name="payment_proof"]').val() == "") {
					alert('<?= __('user.payment_proof_required'); ?>');
					return false
				}

				ajaxData += "&payment_proof=" + $('input[name="payment_proof"]').val();
			<?php } ?>

			$.ajax({
				url:'<?= base_url("store/payment_confirmation") ?>',
				type:'POST',
				dataType:'json',
				data:ajaxData,
				beforeSend:function(){$("#button-confirm").btn("loading");},
				complete:function(){$("#button-confirm").btn("reset");},
				success:function(json){
					$container = $("#checkout-confirm");
					$container.find(".has-error").removeClass("has-error");
					$container.find("span.text-danger").remove();



					if(json['errors']){
						if(json['errors']['comment']){
							$.each(json['errors']['comment'], function(ii,jj){
							    $ele = $container.find('#comment_textarea'+ ii);
							    if($ele){
							        $ele.parents(".form-group").addClass("has-error");
							        $ele.after("<span class='text-danger'>"+ jj +"</span>");
							    }
							});
						}
						
						if(json['errors']['payment_proof']){
							$ele = $container.find('input[name="payment_proof"]');
							if($ele){
								$ele.parents(".form-group").addClass("has-error");
							    $ele.after("<span class='text-danger'>"+ json['errors']['payment_proof'] +"</span>");
							}
						}
					}

					if(json['success']){
						data = localStorage.getItem("selectedCookies");
						data = JSON.parse(data);
						var formData = new FormData();
						formData.append('bank_method', $('select[name="bank_method"]').val());
						
						if (data) {

							formData.append('cookies_consent',data.cookie1);
						}else{
							formData.append('cookies_consent','');
						}
						<?php if($settingData['proof']){ ?>
							formData.append('payment_proof', ($('input[type=file][name=payment_proof]')[0] ? $('input[type=file][name=payment_proof]')[0].files[0] : null)); 
						<?php } ?>

						$.ajax({
							url:'<?= base_url("store/confirm_payment") ?>',
							type:'POST',
							dataType:'json',
							data:formData,
							contentType: false,
				    		processData: false,
							beforeSend:function(){$this.btn("loading");},
							complete:function(){$this.btn("reset");},
							success:function(json){
								if(json['redirect']){
									window.location = json['redirect'];
								}
								if(json['warning']){
									alert(json['warning'])
								}

								$container = $("#checkout-confirm");
								$container.find(".has-error").removeClass("has-error");
								$container.find("span.text-danger").remove();
							

								if(json['errors']){
								    $.each(json['errors'], function(i,j){
								        $ele = $container.find('[name="'+ i +'"]');
								        if($ele){
								            $ele.parents(".form-group").addClass("has-error");
								            $ele.after("<span class='text-danger'>"+ j +"</span>");
								        }
								    });
								}
							},
						});
					}
				},
			});
		});
	</script>
<?php } else if($gatewayData['module'] == 'deposit'){ ?>
	<form id="formConfirmation">
		<?php 
			$bank_names = [];

			if(isset($settingData['bank_names']) && ! empty($settingData['bank_names'])){
				$bank_names = (array)json_decode($settingData['bank_names'],1);
			}
		?>

		<div class="form-group">
			<label class="control-label"><?= __('user.choose_bank') ?></label>
			<select name="bank_method" class="form-control">
				<option value="0"><?= (isset($bank_names[0]) && !empty($bank_names[0])) ? $bank_names[0]  : substr($settingData['bank_details'],0,50)."..." ?></option>
				<?php
					if(isset($settingData['additional_bank_details'])){
						$additional_bank_details = (array)json_decode($settingData['additional_bank_details'],1);
						foreach ($additional_bank_details as $key => $value) {
							$Bname = (isset($bank_names[$key+1]) && !empty($bank_names[$key+1])) ? $bank_names[$key+1]  : substr($value,0,50)."...";
							echo '<option value="'. ($key+1) .'">'. $Bname .'</option>';
						}
					}
				?>
			</select>
		</div>

		<div class="checkout-bank-details">
			<?php $Bname = (isset($bank_names[0]) && !empty($bank_names[0])) ? $bank_names[0]."\r\n"  : ""; ?>
			<pre class="well d-none"><?= $Bname.$settingData['bank_details'] ?></pre>
			<?php
				if(isset($settingData['additional_bank_details'])){
					$additional_bank_details = (array)json_decode($settingData['additional_bank_details'],1);
					foreach ($additional_bank_details as $key => $value) {
						$Bname = (isset($bank_names[$key+1]) && !empty($bank_names[$key+1])) ? $bank_names[$key+1]."\r\n"  : "";
						$value = $Bname.$value;
						echo '<pre class="well d-none">'. $value .'</pre>';
					}
				}
			?>
		</div>

		<?php $Bname = (isset($bank_names[0]) && !empty($bank_names[0])) ? $bank_names[0]."\r\n"  : ""; ?>
		<input type="hidden" name="bank_details[]" value="<?= $Bname.$settingData['bank_details'] ?>"/>
		<?php
			if(isset($settingData['additional_bank_details'])){
				$additional_bank_details = (array)json_decode($settingData['additional_bank_details'],1);
				foreach ($additional_bank_details as $key => $value) { 
					$Bname = (isset($bank_names[$key+1]) && !empty($bank_names[$key+1])) ? $bank_names[$key+1]."\r\n"  : "";
					$value = $Bname.$value;
					?>

					<input type="hidden" name="bank_details[]" value="<?= $value ?>"/>
					<?php
				}
			}
		?>

		<?php if($settingData['proof'] == 1){ ?>
			<div class="form-group">
				<label class="control-label"><?= __('user.payment_proof') ?></label>
				<input type="file" name="payment_proof" class="form-control">
			</div>
		<?php } ?>
		<?php if($settingData['proof'] == 2){ ?>
			<div class="form-group">
				<label class="control-label"><?= __('user.payment_proof') ?></label>
				<input type="file" name="payment_proof" class="form-control" required>
			</div>
		<?php } ?>
		</form>
		
		<div class="payment-button-group">
			<button type="button" class="btn btn-default" onclick='backCheckout()'><?= __('user.back') ?></button>
			<button id="button-confirm" class="btn btn-primary"><?= __('user.confirm') ?></button>
		</div>

		<script type="text/javascript">
			$("select[name=bank_method]").change(function(){
				var val = $(this).val();
				$('.checkout-bank-details .well').addClass('d-none');
				$('.checkout-bank-details .well').eq(val).removeClass('d-none');
			});

			$("select[name=bank_method]").val('0').trigger("change");

			$("#button-confirm").click(function(){
				<?php if($settingData['proof'] == 2){ ?>
					if($('input[name="payment_proof"]').val() == "") {
						alert('<?= __('user.payment_proof_required'); ?>');
						return false
					}
				<?php } ?>

				$this = $(this);
				
				$this.prop('disabled',true);
				
				$.ajax({
					url:'<?= base_url("usercontrol/payment_confirmation") ?>',
					type:'POST',
					dataType:'json',
					data:$('#formConfirmation').serialize(),
					beforeSend:function(){$("#button-confirm").btn("loading");},
					complete:function(){$("#button-confirm").btn("reset");},
					success:function(json){
						$container = $("#formConfirmation");
						$container.find(".has-error").removeClass("has-error");
						$container.find("span.text-danger").remove();

						if(json['errors']){
							$.each(json['errors']['comment'], function(ii,jj){
							    $ele = $container.find('#comment_textarea'+ ii);
							    if($ele){
							        $ele.parents(".form-group").addClass("has-error");
							        $ele.after("<span class='text-danger'>"+ jj +"</span>");
							    }
							});

							$.each(json['errors'], function(ii,jj){
							    $ele = $container.find('name['+ ii+']');
							    if($ele){
							        $ele.parents(".form-group").addClass("has-error");
							        $ele.after("<span class='text-danger'>"+ jj +"</span>");
							    }
							});
						}

						if(json['success']){
							var formData = new FormData();
							
							formData.append('bank_method', $('select[name="bank_method"]').val());
							
							<?php if($settingData['proof']){ ?>
								formData.append('payment_proof', ($('input[type=file][name=payment_proof]')[0] ? $('input[type=file][name=payment_proof]')[0].files[0] : null)); 
							<?php } ?>

							$.ajax({
								url:'<?= base_url("usercontrol/confirm_payment") ?>',
								type:'POST',
								dataType:'json',
								data:formData,
								contentType: false,
					    		processData: false,
								beforeSend:function(){$this.btn("loading");},
								complete:function(){$this.btn("reset");},
								success:function(json){
									if(json['redirect']){
										window.location = json['redirect'];
									}
									if(json['warning']){
										alert(json['warning'])
									}

									$container = $("#formConfirmation");
									$container.find(".has-error").removeClass("has-error");
									$container.find("span.text-danger").remove();
								

									if(json['errors']){
									    $.each(json['errors'], function(i,j){
									    	console.log(i);
									        $ele = $container.find('[name="'+ i +'"]');
									        console.log($ele.length)
									        if($ele){
									            $ele.parents(".form-group").addClass("has-error");
									            $ele.after("<span class='text-danger'>"+ j +"</span>");
									        }
									    });
									}
								},
							});
						}
					},
				});
			});
		</script>
<?php } else if($gatewayData['module'] == 'membership'){ ?>
	<form  method="post" enctype="multipart/form-data">
		<div class="well">
			<?php 
			$bank_names = [];

			if(isset($settingData['bank_names']) && ! empty($settingData['bank_names'])){
				$bank_names = (array)json_decode($settingData['bank_names'],1);
			}
		?>

		<div class="form-group">
			<label class="control-label"><?= __('user.choose_bank') ?></label>
			<select name="bank_method" class="form-control">
				<option value="0"><?= (isset($bank_names[0]) && !empty($bank_names[0])) ? $bank_names[0]  : substr($settingData['bank_details'],0,50)."..." ?></option>
				<?php
					if(isset($settingData['additional_bank_details'])){
						$additional_bank_details = (array)json_decode($settingData['additional_bank_details'],1);
						foreach ($additional_bank_details as $key => $value) {
							$Bname = (isset($bank_names[$key+1]) && !empty($bank_names[$key+1])) ? $bank_names[$key+1]  : substr($value,0,50)."...";
							echo '<option value="'. ($key+1) .'">'. $Bname .'</option>';
						}
					}
				?>
			</select>
		</div>

		<div class="checkout-bank-details">
			<?php $Bname = (isset($bank_names[0]) && !empty($bank_names[0])) ? $bank_names[0]."\r\n"  : ""; ?>
			<pre class="well d-none"><?= $Bname.$settingData['bank_details'] ?></pre>
			<?php
				if(isset($settingData['additional_bank_details'])){
					$additional_bank_details = (array)json_decode($settingData['additional_bank_details'],1);
					foreach ($additional_bank_details as $key => $value) {
						$Bname = (isset($bank_names[$key+1]) && !empty($bank_names[$key+1])) ? $bank_names[$key+1]."\r\n"  : "";
						$value = $Bname.$value;
						echo '<pre class="well d-none">'. $value .'</pre>';
					}
				}
			?>
		</div>

		<?php $Bname = (isset($bank_names[0]) && !empty($bank_names[0])) ? $bank_names[0]."\r\n"  : ""; ?>
		<input type="hidden" name="bank_details[]" value="<?= $Bname.$settingData['bank_details'] ?>"/>
		<?php
			if(isset($settingData['additional_bank_details'])){
				$additional_bank_details = (array)json_decode($settingData['additional_bank_details'],1);
				foreach ($additional_bank_details as $key => $value) { 
					$Bname = (isset($bank_names[$key+1]) && !empty($bank_names[$key+1])) ? $bank_names[$key+1]."\r\n"  : "";
					$value = $Bname.$value;
					?>

					<input type="hidden" name="bank_details[]" value="<?= $value ?>"/>
					<?php
				}
			}
		?>

			<?php if($settingData['proof'] == 1){ ?>
				<div class="form-group">
					<label class="control-label"><?= __('user.payment_proof') ?></label>
					<input type="file" name="payment_proof" class="form-control">
				</div>
			<?php } ?>
			<?php if($settingData['proof'] == 2){ ?>
				<div class="form-group">
					<label class="control-label"><?= __('user.payment_proof') ?></label>
					<input type="file" name="payment_proof" class="form-control" required>
				</div>
			<?php } ?>

			<div class="text-info mb-4">
			<?= __('user.if_admin_asked_you_send_payment_proof'); ?>
			</div>
			<div class="text-center">
				<button type="button" class="btn btn-default" onclick='backCheckout()'>
					<?= __('user.back'); ?></button>
				<button id="btn-confirm" type="button" class="btn btn-primary">
					<?= __('user.buy_now'); ?>
				</button>
			</div>
		</div>
	</form>

	<script type="text/javascript">

		$("select[name=bank_method]").change(function(){
			var val = $(this).val();
			$('.checkout-bank-details .well').addClass('d-none');
			$('.checkout-bank-details .well').eq(val).removeClass('d-none');
		});

		$("select[name=bank_method]").val('0').trigger("change");

		$('#btn-confirm').on('click',function(){
			$this = $(this);

			<?php if($settingData['proof'] == 2){ ?>
				if($('input[name="payment_proof"]').val() == "") {
					alert('<?= __('user.payment_proof_required'); ?>');
					return false
				}
			<?php } ?>

			var formData = new FormData();
			formData.append('payment_proof', ($('input[type=file][name=payment_proof]')[0] ? $('input[type=file][name=payment_proof]')[0].files[0] : null));
 
			$.ajax({
				url:'<?= base_url("membership/confirm_plan") ?>',
				type:'POST',
				dataType:'json',
				data:formData,
				contentType: false,
	    		processData: false,
	    		beforeSend:function(){$this.btn("loading");$this.attr("disabled","disabled");},
				complete:function(){$this.btn("reset");$this.removeAttr("disabled");},
				success:function(json){
 					$this.removeAttr("disabled");
					if(json['redirect'])
						window.location = json['redirect'];

					if(json['warning'])
						alert(json['warning']);
				},
			});  
		})
	</script>
<?php } ?>