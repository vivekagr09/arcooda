<?= $transactions_details; ?>

<div class="row mb-3">
    <div class="col-12">
        <div class="form-floating">
            <select name="recursion_type" id="recursion_type" class="form-select">
                <option value="">--- <?= __('admin.none') ?> ---</option>
                <?php foreach ($recursion_type as $key => $value) {
                    $selected = ($key == $recursion['type']) ? 'selected="selected"' : '';
                    echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
                } ?>
            </select>
            <label for="recursion_type"><?= __('admin.type') ?></label>
            <div class="invalid-feedback">Please select a type.</div>
        </div>
    </div>
</div>

<div class="row mb-3 custom_time" style="<?= $recursion['type'] == 'custom_time' ? 'display:none' : '' ?>">
    <div class="col-12">
        <label class="form-label"><?= __('admin.custom_time') ?></label>
    </div>

    <div class="col-sm-4">
        <input placeholder="<?= __('admin.custom_time') ?>" type="number" class="form-control" value="<?= $day ? $day : '' ?>" id="recur_day" onkeydown="if(event.key==='.'){event.preventDefault();}"  oninput="event.target.value = event.target.value.replace(/[^0-9]*/g,'');">
    </div>

    <div class="col-sm-4">
        <select class="form-select" id="recur_hour">
            <option disabled="" value="0"><?= __('admin.hours') ?></option>
            <?php for ($x = 0; $x <= 23; $x++) { 
                $selected = ($x == $hour ) ? 'selected="selected"' : '';
                echo '<option value="'.$x.'" '.$selected.'>'.$x.'</option>';
            } ?>
        </select>
    </div>

    <div class="col-sm-4">
        <select class="form-select" id="recur_minute">
            <option disabled="" value="0"><?= __('admin.minutes') ?></option>
            <?php for ($x = 0; $x <= 59; $x++) {
                $selected = ($x == $minute ) ? 'selected="selected"' : '';
                echo '<option value="'.$x.'" '.$selected.'>'.$x.'</option>';
            } ?>
        </select>
    </div>
</div>

<div class="endtime-chooser row mb-3">
    <div class="col-12">
        <div class="form-check">
            <input <?= $recursion['endtime'] ? 'checked' : '' ?>  id='setCustomTime' type="checkbox" class="form-check-input">
            <label class="form-check-label" for="setCustomTime"><?= __('admin.choose_custom_endtime') ?></label>
            <div style="<?= !$recursion['endtime'] ? 'display:none' : '' ?>" class='custom_time_container'>
                <input type="text" class="form-control" value="<?= $recursion['endtime'] ? date("d-m-Y H:i",strtotime($recursion['endtime'])) : '' ?>" name="endtime" id="endtime" placeholder="<?= __('admin.choose_endtime') ?>" >
            </div>
        </div>
    </div>
</div>

<div class='row'> 
    <div class='col-6'>
        <button data-bs-dismiss='modal' class='btn btn-primary btn-block'><?= __('admin.cancel') ?></button>
    </div>
    <div class='col-6'>
        <button class='btn btn-danger btn-block' recursion-tran-confirm='<?= $wallet_data->id ?>'><?= __('admin.yes_confirm') ?></button>
    </div>
</div>


<script type="text/javascript">
	$('#setCustomTime').on('change', function(){
		$(".custom_time_container").hide();
		if($(this).prop("checked")){
			$(".custom_time_container").show();
		}
	});

	$('#recursion_type').on('change', function(){
		var recursion_type = $(this).val();
		if( recursion_type == 'custom_time' ){
			$('.custom_time').show();
		}else{
			$('.custom_time').hide();
		}
	});

	$('#endtime').datetimepicker({
		format:'d-m-Y H:i',
		inline:true,
	});

	$("#modal-recursion [recursion-tran-confirm]").on("click",function(e){
		$this = $(this);

		var recursion_type = $('#recursion_type').val();
		var error = 0;

		if( $('.custom_time').is(':visible') ){
			var days = $('#recur_day').val();
			var hours = $('#recur_hour').val();
			var minutes = $('#recur_minute').val();
			var total_minutes;		
			
			total_hours = parseInt(days*24) + parseInt(hours);
			total_minutes = parseInt(total_hours*60) + parseInt(minutes);

			if( total_minutes > 0 ){
				$('.custom_time').find('.error').text("");
				 error = 0;				
			}else{
				$('.custom_time').find('.error').text('<?= __('admin.time_is_required') ?>');
				error++;
			}
		}

		if( error == 0 ){
			$.ajax({
				url: '<?php echo base_url("admincontrol/confirm_recursion_tran") ?>',
				type:'POST',
				dataType:'json',
				data:{
					transaction_id : $this.attr("recursion-tran-confirm"),
					endtime        : $("#endtime").val(),
					setCustomTime  : $("#setCustomTime").prop('checked'),
					type           : recursion_type,
					custom_time    : total_minutes
				},
				beforeSend:function(){ $this.button("loading"); },
				complete:function(){ $this.button("reset"); },
				success:function(json){
					$btn = $('.recursion-tran[data-id="'+ $this.attr("recursion-tran-confirm") +'"]');

					if(json['table']){
						$tr = $(json['table']).find("td:last").html();
						$btn.parents("td").html($tr);
						$(".tooltip").remove();
						$("[data-toggle=tooltip]").tooltip()
					}
					
					$("#modal-recursion .modal-body").html('');
					$("#modal-recursion").modal("hide");
				},
			});
		}
	});
</script>