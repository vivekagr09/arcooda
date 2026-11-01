<?php
	$db =& get_instance();
	$userdetails=$db->userdetails();
	$store_setting =$db->Product_model->getSettings('store');
	$Product_model =$db->Product_model;
?>

<div id="overlay"></div>
<div class="popupbox" style="display: none;">
	<div class="backdrop box">
		<div class="modalpopup" style="display:block;">
			<a href="javascript:void(0)" class="close js-menu-close" onclick="closePopup();">
				<i class="bi bi-x"></i>
			</a>
			<div class="modalpopup-dialog">
				<div class="modalpopup-content">
					<div class="modalpopup-body">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header bg-secondary text-white">
				<h5><?= __('admin.tutorial') ?></h5>
			</div>
			<div class="card-body">
				<div class="tab-pane p-3" id="store-setting" role="tabpanel">
					<ul class="nav nav-pills bg-orange" role="tablist" id="TabsNav">
						<li class="nav-item">
							<div class="form-check form-switch">
								<input class="form-check-input update_all_settings" type="checkbox" <?= $site['tutorial_module_status']==1 ? 'checked' : '' ?> data-bs-toggle="toggle" data-size="sm" data-on="<?= __('admin.status_on') ?>" data-off="<?= __('admin.status_off') ?>" data-setting_key="tutorial_module_status" data-setting_type="site">
							</div>
						</li>
						<li class="nav-item">
							<a class="nav-link active show category_tab_option" href="#category_tab" aria-controls="category_tab" role="tab" data-bs-toggle="tab">
								<?= __('admin.category') ?>
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link product-part tutorial_tab_option" href="#tutorial_tab_option" aria-controls="tutorial_tab_option" role="tab" data-bs-toggle="tab">
								<?= __('admin.pages') ?>
							</a>
						</li>
					</ul>
				</div>

				<div class="tab-content">
					<div class="tab-pane active" id="category_tab">
						<div class="filter mt-3 mb-3">
							<form id="form2" name="form2">
								<div class="row">
									<div class="col-sm-4">
										<div class="form-group mb-3">
											<label class="form-label"><?= __('admin.select_language') ?></label>
											<select class="form-select" name="language_id2" id="drpLanguage2" onchange="return changeLanguage2();">
												<?php 
												if(isset($languages))
												{
													$language_id=1;
													foreach($languages as $language)
													{?>
														<option <?= isset($userlangid) && $userlangid==$language['id'] ? 'selected' : '' ?> value="<?= $language['id'] ?>"><?= $language['name'] ?></option>
														<?php
													}
												}
												?>
											</select>
										</div>
									</div>
									<div class="col-sm-8">
										<div class="text-end">
											<a class="btn btn-primary" href="<?= base_url('admincontrol/manage_tutorial_catgory/') ?>">
												<?= __('admin.add_new_category'); ?>
											</a>
										</div>
									</div>
								</div>
							</form>
						</div>

						<div class="table-responsive" id="table-category">
						</div>
					</div>

					<div class="tab-pane" id="tutorial_tab_option">
						<div class="filter mt-3 mb-3">
							<form id="form1" name="form1">
								<div class="row">
									<div class="col-sm-4">
										<div class="form-group mb-3">
											<label class="form-label"><?= __('admin.select_language') ?></label>
											<select class="form-select" name="language_id" id="drpLanguage" onchange="return changeLanguage();">
												<?php 
												if(isset($languages))
												{
													$language_id=1;
													foreach($languages as $language)
													{?>
														<option <?= isset($userlangid) && $userlangid==$language['id'] ? 'selected' : '' ?> value="<?= $language['id'] ?>"><?= $language['name'] ?></option>
														<?php
													}
												}
												?>
											</select>
										</div>
									</div>
									<div class="col-sm-8">
										<div class="text-end">
											<a class="btn btn-primary" href="<?= base_url('admincontrol/manage_tutorial/') ?>">
												<?= __('admin.add_new_page'); ?>
											</a>
										</div>
									</div>
								</div>
							</form>
						</div>

						<div class="table-responsive" id="table-tutorial">
						</div>
					</div>
				 </div>
			</div>
		</div>
	</div>
</div>






<script type="text/javascript">
	function changeLanguage() {
		getTutorials('<?= base_url("admincontrol/listTutorals_ajax")?>');
		return false;
	}

	$("#table-tutorial").delegate(".pagination-td a", "click", function() {
		getTutorials($(this).attr("href"));
		return false;
	});

	function getTutorials(url) {
		var $this = $(this);
		$.ajax({
			url: url,
			type: 'POST',
			dataType: 'json',
			data: $("#form1").serialize(),
			beforeSend: function() {
				$this.btn("loading");
			},
			complete: function() {
				$this.btn("reset");
			},
			success: function(json) {
				if (json['view']) {
					$("#table-tutorial").html(json['view']);
				} else {

				}

				$("#table-tutorial .pagination-td").html(json['pagination']);
			},
		});
	}

	$(document).ready(function() {
		getTutorials('<?= base_url("admincontrol/listTutorals_ajax")?>');
	});

	function changeLanguage2() {
		getCategory('<?= base_url("admincontrol/listTutorialCategory_ajax")?>');
		return false;
	}

	$("#table-category").delegate(".pagination-td a", "click", function() {
		getCategory($(this).attr("href"));
		return false;
	});

	function getCategory(url) {
		var $this = $(this);
		$.ajax({
			url: url,
			type: 'POST',
			dataType: 'json',
			data: $("#form2").serialize(),
			beforeSend: function() {
				$this.btn("loading");
			},
			complete: function() {
				 $this.btn("reset");
			},
			success: function(json) {
				if (json['view']) {
					$("#table-category").html(json['view']);
				} else {

				}
				$("#table-category .pagination-td").html(json['pagination']);
			},
		});
	}

	$(document).ready(function() {
		getCategory('<?= base_url("admincontrol/listTutorialCategory_ajax")?>');
	});

	$('.update_all_settings').on('change', function() {
		var checked = $(this).prop('checked');
		var setting_key = $(this).data('setting_key');
		var setting_type = $(this).data('setting_type');
		var controle_id = $(this).attr('id');

		if (checked == true) {
			var status = 1;
		} else {
			var status = 0;
		}

		$.ajax({
			url: '<?= base_url("admincontrol/update_all_settings") ?>',
			type: 'POST',
			dataType: 'json',
			data: {
				'action': 'update_all_settings',
				'status': status,
				'setting_key': setting_key,
				'setting_type': setting_type
			},
			success: function(json) {

			},
		});
	});
</script>
	