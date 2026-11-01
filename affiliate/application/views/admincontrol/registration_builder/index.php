<div class="row">
			<div class="col-12">
				<div class="card m-b-30">
					<div class="card-header">
						<h4 class="card-title pull-left"><?= __("admin.registration_builder") ?></h4>
						<div class="pull-right">
							<button class="btn btn-primary save-form"><?= __('admin.save') ?></button>
						</div>
					</div>
					<div class="card-body">
						<div class="table-rep-plugin">
							<div class="table-responsive b-0" data-pattern="priority-columns">
							<script type="text/javascript" src="<?= base_url('assets/plugins/ui/jquery-ui.min.js') ?>"></script>
								<script type="text/javascript" src="<?= base_url('assets/plugins/registration_builder/js/form-builder.js') ?>"></script>

								<div id="build-wrap"></div>
								
								<div id="form-data" style='display:none'><?= htmlspecialchars($builder['registration_builder']) ?></div>

								<script type="text/javascript">
									let controls = ['autocomplete', 'button', 'checkbox-group', 'date', 'file', 'header', 'hidden', 'number', 'paragraph', 'radio-group', 'select', 'starRating', 'text', 'textarea'];
									let typeUserAttrs = {
								        text: {
									        mobile_validation: {

										      label: '<?= __('admin.mobile_validation') ?>',
										      value: false,
										      type: 'checkbox',
										    }
								        }
							       	};
									for (var i = controls.length - 1; i >= 0; i--) {
										let xyz = {
											hide_on_registration: {
										      label: '<?= __('admin.hide_on_registration') ?>',
										      value: false,
										      type: 'checkbox',
										    }
										};

										if(typeof typeUserAttrs[controls[i]] != 'undefined') {
											typeUserAttrs[controls[i]].hide_on_registration = {
										      label: '<?= __('admin.hide_on_registration') ?>',
										      value: false,
										      type: 'checkbox',
										    }
										} else {
											typeUserAttrs[controls[i]] = xyz
										}
									}

									const fbTemplate = document.getElementById('build-wrap');
									var fields = [
									    {
									      label: '<?= __('admin.static_field') ?>',
									      type: 'header',
									      subtype: 'header',
									      icon: '',
									    },
									    {
									      label: '<?= __('admin.mobile_number') ?>',
									      type: 'text',
									      icon:"<i style=\"font-size:24px\" class=\"fa\">&#xf095;</i>",
									    }
									];

									var formBuilder = $(fbTemplate).formBuilder({
										fields:fields,
								     	typeUserAttrs: typeUserAttrs,
								       	disabledFieldButtons: {
										    header: ['remove','edit','copy']
									  	},
										disableFields:['hidden'],
										disabledActionButtons:['clear','save','save'],
										disabledAttrs:['access','description','inline','other','rows','step','style','subtype','toggle'],
										formData:$("#form-data").html(),
								       dataType: 'json'
									}).promise.then(formBuilder => {
										let formData = JSON.parse($("#form-data").html());
										for (var i = formData.length - 1; i >= 0; i--) {
											let elementsParent = $('input[value="'+formData[i].name+'"]').closest('.form-elements');
											if(elementsParent.length > 0) {
												$(elementsParent).find('input[type="checkbox"]').each(function(index) {
													console.log($(this).prop('name'), formData[i][$(this).prop('name')])
													if(formData[i][$(this).prop('name')] == "true"){
														$(this).prop('checked', true);
													}
												});
											}
										}

										$(".save-form").on('click',function(){
											$this = $(this);
											$.ajax({
												url:'',
												type:'POST',
												dataType:'json',
												data:{
													registration_builder:formBuilder.actions.getData(),
												},
												beforeSend:function(){ $this.btn("loading"); },
												complete:function(){ $this.btn("reset"); },
												success:function(json){
													
												},
											})
										});
									});
								</script>
							</div>
						</div>
					</div>
				</div> 
			</div> 
		</div>
<script type="text/javascript">
	
</script>