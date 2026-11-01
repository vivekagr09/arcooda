<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header bg-secondary text-white">
        <div>
          <h5 class="pull-left"><?= __('admin.integration_programs') ?></h5>
          <div class="pull-right">
            <a id="toggle-uploader" class="btn btn-light btn-sm" href="<?= base_url('integration/programs') ?>"><?= __('admin.back') ?></a>
          </div>
        </div>
      </div>

      <div class="card-body">
        <form action="" method="get" id="form_program">
          <div class="row">
            <div class="col">
              <input name="program_id" type="hidden" value="<?= isset($programs) ? $programs['id'] : '0' ?>">
              <div class="form-group mb-4">
                <label class="control-label"><?= __('admin.program_name') ?></label>
                <input class="form-control" name="name" type="text" 
                  <?= ($programs['vendor_id']) ? 'disabled' : '' ?>
                  value="<?= isset($programs) ? $programs['name'] : '' ?>">
              </div>
              <?php if ($programs['vendor_id']) { ?>
                <fieldset class="custom-design mb-2">
                  <legend><?= __('admin.other_affiliate_commission') ?></legend>
                  <div class="row">
                    <div class="col">
                      <div class="form-group mb-1">
                        <label class="control-label"><?= __('admin.click_commission') ?> : </label> 
                        <?php if ($programs['click_status']) { ?>
                          <span><?= c_format($programs['commission_click_commission']) ?> Per <?= (int)$programs['commission_number_of_click'] ?> <?= __('admin.clicks') ?></span>
                        <?php } else { ?>
                          <span><?= __('admin.disabled') ?></span>
                        <?php } ?>
                      </div>
                    </div>
                    <div class="col">
                      <div class="form-group mb-1">
                        <label class="control-label"><?= __('admin.sale_commission') ?> : </label> 
                        <?php if ($programs['sale_status']) { ?>
                          <span> 
                            <?php 
                              if ($programs['commission_type'] == 'percentage') {
                                echo (float)$programs['commission_sale']."%";
                              } else if ($programs['commission_type'] == 'fixed') {
                                echo c_format($programs['commission_sale']);
                              } else {
                                echo __('admin.not_set');
                              }
                            ?>
                          </span>
                        <?php } else { ?>
                          <span><?= __('admin.disabled') ?></span>
                        <?php } ?>
                      </div>
                    </div>
                  </div>
                </fieldset>
                <div class="form-group mt-3">
                  <label class="control-label"><?= __('admin.status'); ?></label>
                  <div>
                    <div class="radio status-radio">
                      <div class="row">
                        <div class="col-sm-3">
                          <label><input type="radio" name="status" value="0" checked=""> <span class="badge bg-warning"><?= __('admin.in_review'); ?></span></label> &nbsp;
                        </div>
                        <div class="col-sm-3">
                          <label><input type="radio" name="status" value="1" <?= (int)$programs['status'] == 1 ? 'checked' : '' ?> > <span class="badge bg-success"><?= __('admin.approved'); ?></span></label>
                        </div>
                        <div class="col-sm-3">
                          <label><input type="radio" name="status" value="2" <?= (int)$programs['status'] == 2 ? 'checked' : '' ?> ><span class="bg-danger text-white"><?= __('admin.denied'); ?></span></label>
                        </div>
                        <div class="col-sm-3">
                          <label><input type="radio" name="status" value="3" <?= (int)$programs['status'] == 3 ? 'checked' : '' ?> ><span class="badge bg-warning"><?= __('admin.ask_to_edit'); ?></span></label>
                        </div>
                      </div>
                    </div>
                  </div>  

                  <div class="p-3 mb-2 bg-warning text-dark border rounded">
                    <div class="d-flex align-items-center">
                      <i class="bi bi-exclamation-circle-fill flex-shrink-0 me-2" style="font-size: 1.5rem;"></i>
                      <div>
                        <?= __('admin.review_program_need_admin_aprove_warning'); ?>
                      </div>
                    </div>
                  </div>
                </div>
              <?php } ?>
            </div>
            <div class="col">
              <?php if ($programs['vendor_id']) { ?>
                <div class="form-group">
                  <label class="control-label d-inline-block"><?= __('admin.vendor'); ?> : </label>
                  <div class="d-inline-block">
                    <?= $programs['vendor_name'] ?> ( <?= $programs['username'] ?> ) 
                    <a class="font-weight-bold " href="<?= base_url('admincontrol/addusers/'. $programs['vendor_id']) ?>" target="_blank"><i class="fa fa-link"></i></a>
                  </div>
                </div>

                <div class="card mt-3">
                  <div class="card-header bg-secondary text-white text-center"><h5><?= __('admin.admin_comments'); ?></h5></div>
                  <div class="card-body chat-card">
                    <?php $comment = json_decode($programs['comment'],1); ?>
                    <?php if($comment){ ?>
                      <ul class="comment-products">
                        <?php foreach ($comment as $key => $value) { ?>
                          <li class="<?= $value['from'] == 'admin' ? 'me' : 'other' ?>"> <div><?= $value['comment'] ?></div> </li>
                        <?php } ?>
                      </ul>
                    <?php } ?>
                    <div class="bg-white form-group m-0 p-2">
                      <textarea class="form-control" placeholder="<?= __('admin.enter_message_and_save_program_to_send'); ?>" name="comment"></textarea>
                    </div>
                  </div>
                </div>
              <?php } ?>
            </div>
          </div>

          <script type="text/javascript">
            $('input[name="status"]').change(function(){
              $(".in_review-status").hide();
              if($('input[name="status"]:checked').val() == '0'){
                $(".in_review-status").show();
              }
            })
            $('input[name="status"]:checked').trigger("change")
          </script>

          <?php if ($programs['vendor_id']) { ?>
            <div class="row">
              <div class="col-sm-6">
                <div class="custom-card card">
                  <div class="card-header bg-secondary text-white text-center"><h5><?= __('admin.admin_sale_settings') ?></h5></div>

                  <div class="card-body">
                    <?php $readonly = $programs['sale_status'] ? '' : 'readonly'; ?>
                    <div class="form-group">
                      <label class="control-label"><?= __('admin.commission_type') ?></label>
                      <input readonly value="<?= $programs['commission_type'] ? strtolower($programs['commission_type']) : __('admin.percentage')  ?>"  class="form-control">
                      <input value="<?= $programs['commission_type'] ? strtolower($programs['commission_type']) : strtolower(__('admin.percentage'))  ?>" name="admin_commission_type" class="form-control d-none">
                    </div>

                    <div class="form-group">
                      <label class="control-label"><?= __('admin.commission_for_sale') ?> </label>
                      <input <?= $readonly ?> class="form-control" name="admin_commission_sale" type="number" value="<?= isset($programs) ? $programs['admin_commission_sale'] : '' ?>">
                    </div>

                    <div class="form-group">
                      <label class="control-label"><?= __('admin.sale_status') ?></label>
                      <div class="<?= $programs['sale_status'] ? '' : 'd-none' ?>">
                        <div class="radio radio-inline"> <label> <input  type="radio" checked="" name="admin_sale_status" value="0"> <?= __('admin.disable') ?> </label> </div>
                        <div class="radio radio-inline"> <label> <input  <?= (isset($programs) && $programs['admin_sale_status']) ? 'checked' : '' ?> type="radio" name="admin_sale_status" value="1"> <?= __('admin.enable') ?> </label> </div>
                      </div>
                      <div class="<?= !$programs['sale_status'] ? '' : 'd-none' ?>">
                        <?= __('admin.disabled') ?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-sm-6">
                <div class="custom-card card">
                  <div class="card-header bg-secondary text-white text-center"><h5><?= __('admin.admin_click_settings') ?></h5></div>

                  <div class="card-body">
                    <?php $readonly = $programs['click_status'] ? '' : 'readonly'; ?>

                    <div class="form-group">
                      <div class="row">
                        <div class="col-sm-12">
                          <div class="form-group">
                            <label class="control-label"><?= __('admin.admin_Clicks_allow') ?></label>
                            <select name="click_allow" class="form-control">
                              <option <?= (isset($programs) && $programs['click_allow'] == 'multiple') ? 'selected' : '' ?> value="multiple"><?= __('admin.admin_allow_multi_click') ?></option>
                              <option <?= (isset($programs) && $programs['click_allow'] == 'single') ? 'selected' : '' ?> value="single"><?= __('admin.admin_allow_single_click') ?></option>
                            </select>
                          </div>
                        </div>
                        
                        <div class="col-sm-6">
                          <div class="form-group">
                            <label class="control-label"><?= __('admin.number_of_click') ?></label>
                            <input <?= $readonly ?> class="form-control" name="admin_commission_number_of_click" type="number" value="<?= isset($programs) ? $programs['admin_commission_number_of_click'] : '' ?>">
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="form-group">
                            <label class="control-label"><?= __('admin.amount_per_click') ?></label>
                            <input <?= $readonly ?> class="form-control" name="admin_commission_click_commission" type="number" value="<?= isset($programs) ? $programs['admin_commission_click_commission'] : '' ?>">
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="control-label"><?= __('admin.click_status') ?></label>
                      <div class="<?= $programs['click_status'] ? '' : 'd-none' ?>">
                        <div class="radio radio-inline"> <label> <input type="radio" checked="" name="admin_click_status" value="0"> <?= __('admin.disable') ?> </label> </div>
                        <div class="radio radio-inline"> <label> <input type="radio" <?= (isset($programs) && $programs['admin_click_status']) ? 'checked' : '' ?> name="admin_click_status" value="1"> <?= __('admin.enable') ?> </label> </div>
                      </div>
                      <div class="<?= !$programs['click_status'] ? '' : 'd-none' ?>">
                        <?= __('admin.disabled') ?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <?php } else { ?>
            <div class="row">
              <div class="col-sm-6">
                <div class="custom-card card">
                  <div class="card-header bg-secondary text-white text-center"><h5><?= __('admin.sale_settings') ?></h5></div>

                  <div class="card-body">
                    <div class="form-group">
                      <label class="control-label"><?= __('admin.commission_type') ?></label>
                      <select name="commission_type" class="form-control">
                        <option value=""><?= __('admin.select_product_commission_type') ?></option>
                        <option <?= (isset($programs) && $programs['commission_type'] == 'percentage') ? 'selected' : '' ?> value="percentage"><?= __('admin.percentage') ?></option>
                        <option <?= (isset($programs) && $programs['commission_type'] == 'fixed') ? 'selected' : '' ?> value="fixed"><?= __('admin.fixed') ?></option>
                      </select>
                    </div>

                    <div class="form-group">
                      <label class="control-label"><?= __('admin.commission_for_sale') ?> </label>
                      <input class="form-control" name="commission_sale" type="number" value="<?= isset($programs) ? $programs['commission_sale'] : '' ?>">
                    </div>

                    <div class="form-group">
                      <label class="control-label"><?= __('admin.sale_status') ?></label>
                      <div>
                        <div class="radio radio-inline"> <label> <input type="radio" checked="" name="sale_status" value="0"> <?= __('admin.disable') ?> </label> </div>
                        <div class="radio radio-inline"> <label> <input <?= (isset($programs) && $programs['sale_status']) ? 'checked' : '' ?> type="radio" name="sale_status" value="1"> <?= __('admin.enable') ?> </label> </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-sm-6">
                <div class="custom-card card">
                  <div class="card-header bg-secondary text-white text-center"><h5><?= __('admin.click_settings') ?></h5></div>

                  <div class="card-body">
                    <div class="form-group">
                      <div class="row">
                        <div class="col-sm-12">
                          <div class="form-group">
                            <label class="control-label"><?= __('admin.admin_Clicks_allow') ?></label>
                            <select name="click_allow" class="form-control">
                              <option <?= (isset($programs) && $programs['click_allow'] == 'multiple') ? 'selected' : '' ?> value="multiple"><?= __('admin.admin_allow_multi_click') ?></option>
                              <option <?= (isset($programs) && $programs['click_allow'] == 'single') ? 'selected' : '' ?> value="single"><?= __('admin.admin_allow_single_click') ?></option>
                            </select>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="form-group">
                            <label class="control-label"><?= __('admin.number_of_click') ?></label>
                            <input class="form-control" name="commission_number_of_click" type="number" value="<?= isset($programs) ? $programs['commission_number_of_click'] : '' ?>">
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="form-group">
                            <label class="control-label"><?= __('admin.amount_per_click') ?></label>
                            <input class="form-control" name="commission_click_commission" type="number" value="<?= isset($programs) ? $programs['commission_click_commission'] : '' ?>">
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="control-label"><?= __('admin.click_status') ?></label>
                      <div>
                        <div class="radio radio-inline"> <label> <input type="radio" checked="" name="click_status" value="0"> <?= __('admin.disable') ?> </label> </div>
                        <div class="radio radio-inline"> <label> <input type="radio" <?= (isset($programs) && $programs['click_status']) ? 'checked' : '' ?> name="click_status" value="1"> <?= __('admin.enable') ?> </label> </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <?php } ?>
        </form>  
      </div>

      <div class="card-footer text-right">
        <button class="btn btn-primary btn-save"><?= __('admin.save') ?></button>
      </div>
    </div>
  </div>
</div>



<script type="text/javascript">
	$(".btn-save").on('click',function(){
	 	$this = $("#form_program");
	 	
		$.ajax({
            url:'<?= base_url('integration/editProgram') ?>',
            type:'POST',
            dataType:'json',
            data:$this.serialize(),
            success:function(result){
                $this.find(".has-error").removeClass("has-error");
                $this.find("span.text-danger").remove();
                
                if(result['location']){ window.location = result['location']; }

                if(result['errors']){
                    $.each(result['errors'], function(i,j){
                        $ele = $this.find('[name="'+ i +'"]');
                        if($ele){
                            $ele.parents(".form-group").addClass("has-error");
                            $ele.after("<span class='text-danger'>"+ j +"</span>");
                        }
                    });
                }
            },
        })
	});
</script>