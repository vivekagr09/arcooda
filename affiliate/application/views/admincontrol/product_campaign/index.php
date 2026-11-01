<div id="overlay"></div>
<div class="popupbox" style="display: none;">
	<div class="backdrop box">
		<div class="modalpopup" style="display:block;">
			<a href="javascript:void(0)" class="close js-menu-close" onclick="closePopup();"><i class="fa fa-times"></i></a>
			<div class="modalpopup-dialog">
				<div class="modalpopup-content">
					<div class="modalpopup-body">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php if ($currentTheme=="cart" ||$StoreStatus=="0"){?>
<!-- Notification On Pages -->
<h5 class="notification_on_pages mb-3">
    <div class="bg-danger text-white p-3 rounded">
        <?= __('admin.sales_product_notice') ?>
    </div>
</h5>
<?php } ?>
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header bg-secondary text-white">
						<h5 class="pull-left"><?= __('admin.sales_mode_products') ?></h5>
						<div class="pull-right">
								<a id="toggle-uploader" class="btn btn-light" href="<?php echo base_url('Productsales/create') ?>"><?= __('admin.add_sale_page_product') ?>
								</a>
						</div>
					</div>
					<div class="card-body">
						<div class="row top-panel">
							<span>
								<button style="display:none;" type="button" class="btn btn-lg btn-danger" name="deletebutton" id="deletebutton" value="<?= __('admin.save_exit') ?>" onclick="deleteuserlistfunc('deleteAllproducts');"><?= __('admin.delete_products') ?></button>
							</span>
						</div>
						<br>

						<div class="filter">
							<form id="filter-form">
								<div class="row">
									<div class="col-sm-5">
										<div class="form-group">
											<label class="control-label"><?= __('admin.vendor') ?></label>
											<select name="seller_id" class="form-control">
												<?php $selected = isset($_GET['seller_id']) ? $_GET['seller_id'] : ''; ?>
												<option value=""><?= __('admin.all_vendor') ?></option>
												<?php foreach ($vendors as $key => $value) { ?>
													<option <?= $selected == $value['id'] ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
												<?php } ?>
											</select>
										</div>
									</div>	
									<div class="col-sm-3">
										<div class="form-group">
											<label class="control-label d-block">&nbsp;</label>
											<button type="submit" class="btn btn-primary"><?= __('admin.search') ?></button>
										</div>
									</div>	
								</div>
							</form>
						</div>

						<?php if ($productlist == null) {?>
							<div class="text-center mt-5">
							 <div class="d-flex justify-content-center align-items-center flex-column mt-5">
								 <i class="fas fa-exchange-alt fa-5x text-muted"></i>
								 <h3 class="text-muted"><?= __('admin.no_data_found') ?></h3>
							 </div>
							</div>
							<?php } else { ?>
								<div class="table-responsive b-0" data-pattern="priority-columns">
									<form method="post" name="deleteAllproducts" id="deleteAllproducts" action="<?php echo base_url('Productsales/delete'); ?>">
										<table id="campaign-products-table" class="table">
											<thead>
												<tr>
													<th><input name="product[]" type="checkbox" value="" onclick="checkAll(this)"></th>
													<th><?= __('admin.image') ?></th>
													<th width="220px"><?= __('admin.product_name') ?></th>
													<th><?= __('admin.user') ?></th>
													<th><?= __('admin.price') ?></th>
													<th><?= __('admin.sku') ?></th>
													<th width="220px"><?= __('admin.get_ncommission') ?></th>
													<th><?= __('admin.sales_/_commission') ?></th>
													<th><?= __('admin.clicks_/_commission') ?></th>
													<th><?= __('admin.total') ?></th>
													<th><?= __('admin.status') ?></th>
													<th><?= __('admin.action') ?></th>
												</tr>
											</thead>
											<tbody></tbody>
											<tfoot>
												<tr>
													<td colspan="12" class="text-right">
														<ul class="pagination pagination-td"></ul>
													</td>
												</tr>
											</tfoot>
										</table>
									</form>
								</div>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>

			<div class="modal fade" id="showcode-code"></div>

			<script type="text/javascript" async="">



				$(".show-more").on('click',function(){
					$(this).parents("tfoot").remove();
					$("#product-list tr.d-none").hide().removeClass('d-none').fadeIn();
				});

				$(".delete-button").on('click',function(){
					if(!confirm("<?= __('admin.are_you_sure') ?>")) return false;
				});
				$(".toggle-child-tr").on('click',function(){
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

				function checkAll(bx) {
					var cbs = document.getElementsByTagName('input');
					if(bx.checked)
					{
						document.getElementById('deletebutton').style.display = 'block';
					} else {
						document.getElementById('deletebutton').style.display = 'none';
					}
					for(var i=0; i < cbs.length; i++) {
						if(cbs[i].type == 'checkbox') {
							cbs[i].checked = bx.checked;
						}
					}
				}

				function checkonly(bx,checkid) {
					if($(".list-checkbox:checked").length){
						$('#deletebutton').show();
					} else {
						$('#deletebutton').hide();
					}
				}

				function deleteuserlistfunc(formId){
					if(! confirm("<?= __('admin.are_you_sure') ?>")) return false;

					$('#'+formId).submit();
				}


				$("#filter-form").on("submit",function(){
					getPage('<?= base_url("Productsales/listproduct_ajax/") ?>/1');
					return false;
				})

				function getPage(url){
					$this = $(this);
					$.ajax({
						url:url,
						type:'POST',
						dataType:'json',
						data:$("#filter-form").serialize(),
						beforeSend:function(){$this.btn("loading");},
						complete:function(){$this.btn("reset");},
						success:function(json){
							if(json['view']){
								$("#campaign-products-table tbody").html(json['view']);
								$("#campaign-products-table").show();
							} else {
								$(".empty-div").removeClass("d-none");
								$("#campaign-products-table").hide();
							}

							$("#campaign-products-table .pagination-td").html(json['pagination']);
						},
					});
				}

				getPage('<?= base_url("Productsales/listproduct_ajax/") ?>/1');

				$("#campaign-products-table .pagination-td").delegate("a","click",function(){
					getPage($(this).attr("href"));
					return false;
				})

				function closePopup(){
					$('.popupbox').hide();
					$('#overlay').hide();
				}

				function generateCode(affiliate_id){
					$('.popupbox').show();
					$('#overlay').show();
					$('.modalpopup-body').load('<?php echo base_url();?>admincontrol/generateproductcode/'+affiliate_id);
					$('.popupbox').ready(function () {
						$('.backdrop, .box').animate({
							'opacity': '.50'
						}, 300, 'linear');
						$('.box').animate({
							'opacity': '1.00'
						}, 300, 'linear');
						$('.backdrop, .box').css('display', 'block');
					});
				}

				$(document).delegate(".delete-product",'click',function(){
					if(! confirm("<?= __('admin.are_you_sure') ?>")) return false;
					window.location = $("#deleteAllproducts").attr("action") + "?delete_id=" + $(this).attr("data-id");
				})

				$("#campaign-products-table").delegate(".btn-show-code",'click',function(e){
					e.preventDefault();

					$this = $(this);

					$.ajax({
						url:'<?= base_url("Productsales/integration_code_modal") ?>',
						type:'POST',
						dataType:'html',
						data:{
							id: $this.attr("data-id"),
						},
						beforeSend:function(){
							$this.btn("loading");
						},
						complete:function(){
							$this.btn("reset");
						},
						success:function(html){
							$("#showcode-code").html(html);
							$("#showcode-code").modal("show");
						},
					})
				});
			</script>			