<style type="text/css">
    .market-heade>ul>li:nth-child(3) {
    
    color: #232D42;
}
</style>
<?php
$db =& get_instance();
$userdetails=$db->userdetails();
$pro_setting = $this->Product_model->getSettings('productsetting');
$form_setting = $this->Product_model->getSettings('formsetting');
?>
<?php foreach($data_list as $index => $product){ ?>
    <?php
        //$display_class = $index >= $pagination ? 'd-none' : '';
    $display_class = '';
    if ($product['on_store'] == 0 && $product['product_created_by'] == 1) {
        $display_class = 'd-none';
    }else{
        $display_class = '';
    }
    
    ?>
    <?php if(isset($product['is_form'])){ ?>
        <div class="accordion-item <?= $display_class ?>">
            <div class="accordion-header" id="headingOne">
              <div class="market-heade">
                <ul>
                  <li>
                    <img class="accordion-image" src="<?php echo base_url($product['fevi_icon']) ?>" alt="Open Cart">
                  </li> 
                  <li class="name-text">
                    <h6><?= $product['title'] ?>
                    </h6>
                  </li>
                  <li class="offer-text">
                    <?php
                        echo "<p><b>".__('user.you_will_get'). "</b>";
                        if($product['sale_commision_type'] == 'default'){
                            $commissionType = $form_default_commission['product_commission_type'];
                            if($form_default_commission['product_commission_type'] == 'percentage'){
                                if($award_level_status == 1 && $userComission['status'] && $userComission['value'] && $userComission['value'] < $form_default_commission['product_commission'])
                                    $product_commission = $userComission['value'];
                                else
                                    $product_commission = $form_default_commission['product_commission'];

                                echo $product_commission .'% '.__('user.per_sale').'</p>';
                            }
                            else if($form_default_commission['product_commission_type'] == 'Fixed'){
                                echo c_format($form_default_commission['product_commission']) .' '.__('user.per_sale').'</p>';
                            }
                        }
                        else if($product['sale_commision_type'] == 'percentage'){
                            if($award_level_status == 1 && $userComission['status'] && $userComission['value'] && $userComission['value'] < $product['sale_commision_value'])
                                $sale_commision_value = $userComission['value'];
                            else
                                $sale_commision_value = $product['sale_commision_value'];

                            echo $sale_commision_value .'% '.__('user.per_sale').'</p>';
                        }
                        else if($product['sale_commision_type'] == 'fixed'){
                            echo c_format($product['sale_commision_value']) .' '.__('user.per_sale').'</p>';
                        }

                        echo "<p><b>".__('user.you_will_get')."</b>";
                        if($product['click_commision_type'] == 'default'){
                            if((int)$product['vendor_id']){
                                $vendor_setting = $this->db->query("SELECT * FROM vendor_setting WHERE user_id=". (int)$product['vendor_id'] ." ")->row();
                                echo c_format($vendor_setting->form_affiliate_click_amount) .' '.__('user.of_per').' '. (int)$vendor_setting->form_affiliate_click_count .' '.__('user.click').'</p>';
                            } else {
                                $commissionType = $form_default_commission['product_commission_type'];
                                if($form_default_commission['product_commission_type'] == 'percentage'){
                                    echo c_format($form_default_commission['product_ppc']) .' '.__('user.of_per').' '. $form_default_commission['product_noofpercommission'] .' '.__('user.click').'</p>';
                                }
                                else if($form_default_commission['product_commission_type'] == 'Fixed'){
                                    echo c_format($form_default_commission['product_ppc']) .' '.__('user.of_per').' '. $form_default_commission['product_noofpercommission'] .' '.__('user.click').'</p>';
                                }
                            }
                        }
                        else if($product['click_commision_type'] == 'custom') {
                            echo c_format($product['click_commision_per']) .' '.__('user.of_per').' '. $product['click_commision_ppc'] .' '.__('user.click').'</p>';
                        }
                        ?>
                        <div>
                            <?php 
                            if($product['form_recursion_type']){
                                if($product['form_recursion_type'] == 'custom'){
                                    if($product['form_recursion'] != 'custom_time'){
                                        echo '<p><b>'. __('user.recurring') .' </b> : ' . __('user.'. $product['form_recursion']).'</p>';
                                    } else {
                                        echo '<p><b>'. __('user.recurring') .' </b> : '. timetosting($product['recursion_custom_time']).'</p>';
                                    }
                                } else{
                                    if($form_setting['form_recursion'] == 'custom_time' ){
                                        echo '<p><b>'. __('user.recurring') .' </b> : '. timetosting($form_setting['recursion_custom_time']).'</p>';
                                    } else {
                                        echo '<p><b>'. __('user.recurring') .' </b> : '. __('user.'. $form_setting['form_recursion']).'</p>';
                                    }
                                }
                            }
                            ?>
                        </div>
                  </li>
                  <li class="offer-text">
                    <h6><?php echo $product['view_statistics'] ?>
                    </h6>
                  </li>
                  <li class="offer-text">
                    <?php 
                         $ordercountratio=0;
                         if($product['view_statistics']>0)
                            $ordercountratio=$product['all_count_commission']*100/$product['view_statistics'];
                         $ordercountratio = is_float($ordercountratio) ==1 ? number_format((float)$ordercountratio, 2, '.', '') : $ordercountratio ;
                         echo '<p>Sale : '. $ordercountratio .'%</p>';

                         $ordercountratio=0;
                        $comissionclickcount=(int)$product['all_commition_click_count']  ; 
                        if($product['view_statistics']>0)
                           $ordercountratio=$comissionclickcount*100/$product['view_statistics'];
                        $ordercountratio = is_float($ordercountratio) ==1 ? number_format((float)$ordercountratio, 2, '.', '') : $ordercountratio;
                        echo '<p>Click : '. $ordercountratio.'%</p>';
                        ?>
                  </li>
                  <li>
                    <?php
                        if($product['slug']) {
                            $shareUrl = base_url($product['slug']);
                        }else{
                            $shareUrl = $product['public_page'];
                        }
                    ?>
                    <div class="flex align-items-center list-user-action show-tiny-link">
                      
                      <input readonly="readonly" value="<?= $shareUrl ?>" placeholder="<?= $shareUrl ?>" class="form-control input-form-url-<?= $product['form_id'] ?>">

                      <a class="bt-all btn-warning" copyToClipboard="<?= $shareUrl ?>" href="javascript:void(0);" data-bs-toggle="tooltip" title="<?= __('user.copied') ?>">
                        <span class="btn-inner">
                          <i class="far fa-copy"></i>
                        </span>
                      </a>

                      <a class="bt-all btn-danger  btn-model-slug" data-type="form" data-related-id="<?= $product['form_id'] ?>" data-input-class="input-form-url-<?= $product['form_id'] ?>" href="javascript:void(0);" data-bs-toggle="tooltip" title="<?= __('user.slug_settings') ?>">
                          <span class="btn-inner">
                            <i class="fas fa-cog"></i>
                          </span>
                      </a>

                      
                      <a class="bt-all btn-dark btn-group-markettools" target='_black' href="<?= $product['public_page'] ?>">
                        <span class="btn-inner">
                          <i class="fa-solid fa-info"></i>
                        </span>
                      </a>

                      <a class="bt-all btn-success btn-group-markettools" onclick="generateCodeForm(<?php echo $product['form_id'];?>,this);"  href="javascript:void(0);">
                        <span class="btn-inner">
                            <i class="fa-solid fa-code"></i>
                        </span>
                      </a>

                  <a class="bt-all btn-danger get-downloads btn-group-markettools"  href="javascript:void(0)" onclick="downloadCode(this, <?= $product['form_id'] ?>, 'form')" data-bs-toggle="tooltip" title="<?= __('user.download_cam_pack') ?>">
                    <span class="btn-inner">
                      <i class="fa-solid fa-download"></i>
                    </span>
                  </a>

                      
                      <a class="bt-all btn-secondary btn-group-markettools qrcode" href="javascript:void(0)" data-id="<?= $shareUrl ?>">
                        <span class="btn-inner" data-bs-toggle="tooltip" title="<?= __('user.qrcode') ?>">
                          <i class="fas fa-walkie-talkie"></i>
                        </span>
                      </a>

                    <a href="javascript:void(0);" class="bt-all btn-success" data-social-share data-share-url="<?= $shareUrl ?>" data-share-title="<?= $product['title'];?>" data-share-desc="<?= $product['description'];?>" data-bs-toggle="tooltip" title="<?= __('user.share_campaign') ?>">
                        <span class="ms-1">
                            <i class="fa-solid fa-share-from-square"></i>
                        </span>
                    </a>

                    </div>
                    <div class="flex align-items-center list-user-action show-mega-link d-none">
                      
                      <input readonly="readonly" value="<?= $shareUrl ?>?id=<?= $userdetails['id'] ?>" placeholder="<?= $shareUrl ?>" class="form-control input-form-url-<?= $product['form_id'] ?>" data-addition-url="?id=<?= $userdetails['id'] ?>">


                      <a class="bt-all btn-warning" copyToClipboard="<?= $shareUrl ?>?id=<?= $userdetails['id'] ?>" href="javascript:void(0);" data-bs-toggle="tooltip" title="<?= __('user.copied') ?>">
                        <span class="btn-inner">
                          <i class="far fa-copy"></i>
                        </span>
                      </a>

                      <a class="bt-all btn-danger btn-model-slug" data-type="form" data-related-id="<?= $product['form_id'] ?>" data-input-class="input-form-url-<?= $product['form_id'] ?>" href="javascript:void(0);" data-bs-toggle="tooltip" title="<?= __('user.slug_settings') ?>">
                        <span class="btn-inner">
                          <i class="fas fa-cog"></i>
                        </span>
                      </a>

                      
                      <a class="bt-all btn-dark btn-group-markettools" target='_black' href="<?= $product['public_page'] ?>">
                        <span class="btn-inner">
                          <i class="fa-solid fa-info"></i>
                        </span>
                      </a>

                      <a class="bt-all btn-success btn-group-markettools" onclick="generateCodeForm(<?php echo $product['form_id'];?>,this);"  href="javascript:void(0);">
                        <span class="btn-inner">
                          <i class="fa-solid fa-code"></i>
                        </span>
                      </a>


                    <a class="bt-all btn-danger get-downloads btn-group-markettools"  href="javascript:void(0)" onclick="downloadCode(this, <?= $product['form_id'] ?>, 'form')" data-bs-toggle="tooltip" title="<?= __('user.download_cam_pack') ?>">
                      <span class="btn-inner">
                        <i class="fa-solid fa-download"></i>
                      </span>
                    </a>

                      
                      <a class="bt-all btn-secondary btn-group-markettools qrcode" href="javascript:void(0)" data-id="<?= $shareUrl ?>" data-bs-toggle="tooltip" title="<?= __('user.qrcode') ?>">
                          <span class="btn-inner">
                              <i class="fas fa-walkie-talkie"></i>
                          </span>
                      </a>

                      <a href="javascript:void(0);" class="bt-all btn-success" data-social-share data-share-url="<?= $shareUrl ?>" data-share-title="<?= $product['title'];?>" data-share-desc="<?= $product['description'];?>" data-bs-toggle="tooltip" title="<?= __('user.share_campaign') ?>">
                        <span class="ms-1">
                          <i class="fa-solid fa-share-from-square"></i>
                        </span>
                      </a>
                    </div>
                  </li>
                </ul>
              </div>
              <span class="accordion-button" role="button" data-bs-toggle="collapse" data-bs-target="#collapseOne<?= $index ?>" aria-expanded="true" aria-controls="collapseOne"></span>
            </div>
            <div id="collapseOne<?= $index ?>" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
              <div class="accordion-body counting-box">
                <ul class=" d-flex align-items-center justify-content-between">
                  <li>
                    <div class="item-counting">
                      <p>
                        <strong><?= __('admin.coupon_code'); ?>: </strong><?= $product['coupon_code'] ? $product['coupon_code'] : __('user.n_a') ?>
                      </p>
                    </div>
                  </li>
                  <li>
                    <div class="item-counting">
                      <p>
                        <strong><?= __('admin.coupon_use'); ?> : </strong><?= ($product['coupon_name'] ? $product['coupon_name'] : '-').' / '.$product['count_coupon'] ?>
                      </p>
                    </div>
                  </li>
                  <li>
                    <div class="item-counting">
                      <p>
                        <strong><?= __('admin.sales_commission'); ?> : </strong><?= (int)$product['count_commission'].' / '.c_format($product['total_commission']) ?>
                      </p>
                    </div>
                  </li>
                  <li>
                    <div class="item-counting ">
                      <p>
                        <strong><?= __('admin.clicks_commission'); ?> : </strong><?= (int)$product['commition_click_count'].' / '.c_format($product['commition_click']); ?>
                      </p>
                    </div>
                  </li>
                  <li>
                    <div class="item-counting bg-green">
                      <p>
                        <strong><?= __('admin.total_commission'); ?>:</strong> <?= c_format($product['total_commission']+$product['commition_click']); ?>
                      </p>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
        </div>
    <?php } else if(isset($product['is_product'])) { ?>
        <?php 
        if($product['is_campaign_product']) {
            $af_id = _encrypt_decrypt($userdetails['id']."-".$product['product_id']);
            $productLink = addParams($product['product_url'],"af_id",$af_id);
        } else {
            $productLink = base_url('store/'. base64_encode($userdetails['id']) .'/product/'.$product['product_slug'] );
        }
        ?>
        <div class="accordion-item <?= $display_class ?>">
            <div class="accordion-header" id="headingOne">
              <div class="market-heade">
                <ul>
                  <li>
                    <img class="accordion-image" src="<?php echo base_url('assets/images/product/upload/thumb/'. $product['product_featured_image']) ?>" alt="Open Cart">
                  </li> 
                  <li class="name-text">
                    <h6><?php echo $product['product_name'];?>
                    </h6>
                  </li>
                  <li class="offer-text">
                       <?php 

                        if($product['seller_id']){
                            $seller = $this->Product_model->getSellerFromProduct($product['product_id']);
                            $seller_setting = $this->Product_model->getSellerSetting($seller->user_id);

                            $commnent_line = "";
                            if($seller->affiliate_sale_commission_type == 'default'){ 
                                if($seller_setting->affiliate_sale_commission_type == ''){
                                    $commnent_line .= __('user.warning_default_commission_not_set');
                                }
                                else if($seller_setting->affiliate_sale_commission_type == 'percentage'){
                                    if($award_level_status == 1 && $userComission['status'] && $userComission['value'] && $userComission['value'] < $seller_setting->affiliate_commission_value)
                                        $affiliate_commission_value = $userComission['value'];
                                    else
                                        $affiliate_commission_value = (float) $seller_setting->affiliate_commission_value;

                                    $commnent_line .= $affiliate_commission_value .'%';
                                }
                                else if($seller_setting->affiliate_sale_commission_type == 'fixed'){
                                    $commnent_line .= c_format($seller_setting->affiliate_commission_value);
                                }
                            } else if($seller->affiliate_sale_commission_type == 'percentage'){
                                if($award_level_status == 1 && $userComission['status'] && $userComission['value'] && $userComission['value'] < $seller->affiliate_commission_value)
                                    $affiliate_commission_value = $userComission['value'];
                                else
                                    $affiliate_commission_value = (float) $seller->affiliate_commission_value;

                                $commnent_line .=  $affiliate_commission_value .'%';
                            } else if($seller->affiliate_sale_commission_type == 'fixed'){
                                $commnent_line .= c_format($seller->affiliate_commission_value);
                            } 

                            echo '<p><b>'.__('user.you_will_get').'</b> : ' .$commnent_line.' '.__('user.per_sale').'</p>';

                            $commnent_line = "";
                            if($seller->affiliate_click_commission_type == 'default'){ 
                                $commnent_line .= c_format($seller_setting->affiliate_click_amount) ." ".__('user.per')." ". (int)$seller_setting->affiliate_click_count ." ".__('user.clicks');
                            } else{
                                $commnent_line .= c_format($seller->affiliate_click_amount) ." ".__('user.per')." ". (int)$seller->affiliate_click_count ." ".__('user.clicks');
                            } 
                            echo '<p><b>'.__('user.you_will_get').'</b> : ' .$commnent_line.'</p>';

                            ?>


                            <div>
                                <?php 
                                if($product['product_recursion_type']){
                                    if($product['product_recursion_type'] == 'custom'){
                                        if($product['product_recursion'] != 'custom_time'){
                                            echo '<p><b>'. __('user.recurring') .' </b> : ' . __('user.'.$product['product_recursion']).'</p>';
                                        } else {
                                            echo '<p><b>'. __('user.recurring') .' </b> : '. timetosting($product['recursion_custom_time']).'</p>';
                                        }
                                    } else{
                                        if($pro_setting['product_recursion'] == 'custom_time' ){
                                            echo '<p><b>'. __('user.recurring') .' </b> : '. timetosting($pro_setting['recursion_custom_time']).'</p>';
                                        } else {
                                            echo '<p><b>'. __('user.recurring') .' </b> : '. __('user.'.$pro_setting['product_recursion']).'</p>';
                                        }
                                    }
                                }
                                ?>
                            </div>
                            <?php

                        } else { ?>

                            <b><?= __('user.you_will_get') ?></b> : 
                            <?php
                            if($product['product_commision_type'] == 'default'){
                                if($default_commition['product_commission_type'] == 'percentage'){
                                    if($award_level_status == 1 && $userComission['status'] && $userComission['value'] && $userComission['value'] < $default_commition['product_commission'])
                                        $product_commission = $userComission['value'];
                                    else
                                        $product_commission = $default_commition['product_commission'];

                                    echo "<span>".$product_commission. "% ".__('user.per_sale').'</span>';
                                } else {
                                    echo "<p>".c_format($default_commition['product_commission']) ." ".__('user.per_sale').'</p>';
                                }
                            } else if($product['product_commision_type'] == 'percentage'){
                                if($award_level_status == 1 && $userComission['status'] && $userComission['value'] && $userComission['value'] < $product['product_commision_value'])
                                    $product_commision_value = $userComission['value'];
                                else
                                    $product_commision_value = $product['product_commision_value'];

                                echo "<span>".$product_commision_value. "% ".__('user.per_sale').'</span>';
                            } else{
                                echo "<span>".c_format($product['product_commision_value']) ." ".__('user.per_sale').'</span>';
                            }
                            ?>
                            <p><b><?= __('user.you_will_get') ?></b> :
                            <?php
                            if($product['product_click_commision_type'] == 'default'){
                                echo c_format($default_commition['product_ppc']) ." ".__('user.per')." {$default_commition['product_noofpercommission']} ".__('user.click').'</p>';   
                                echo "</small>";
                            } else{
                                echo c_format($product['product_click_commision_ppc']) ." ".__('user.per')." {$product['product_click_commision_per']} ".__('user.click').'</p>';
                            }
                            ?>

                            <div>
                                <?php 
                                if($product['product_recursion_type']){
                                    if($product['product_recursion_type'] == 'custom'){
                                        if($product['product_recursion'] != 'custom_time'){
                                            echo '<p><b>'. __('user.recurring') .' </b> : ' . __('user.'.$product['product_recursion']).'</p>';
                                        } else {
                                            echo '<p><b>'. __('user.recurring') .' </b> : '. timetosting($product['recursion_custom_time']).'</p>';
                                        }
                                    } else{
                                        if($pro_setting['product_recursion'] == 'custom_time' ){
                                            echo '<p><b>'. __('user.recurring') .' </b> : '. timetosting($pro_setting['recursion_custom_time']).'</p>';
                                        } else {
                                            echo '<p><b>'. __('user.recurring') .' </b> : '. __('user.'.$pro_setting['product_recursion']).'</p>';
                                        }
                                    }
                                }
                                ?>
                            </div>
                        <?php } ?>

                  </li>
                  <li class="offer-text">
                    <h6><?= $product['view_statistics'] ?>
                    </h6>
                  </li>
                  <li class="offer-text">
                    <?php 
                        $ordercountratio=0;
                        if($product['view_statistics']>0)
                            $ordercountratio=$product['all_order_count']*100/$product['view_statistics'];
                        $ordercountratio = is_float($ordercountratio) ==1 ? number_format((float)$ordercountratio, 2, '.', '') : $ordercountratio;
                        echo "<p>".__('user.sale') . ' : '.$ordercountratio .'%</p>';
                        ?>

                        <?php 
                        $ordercountratio=0;
                        $comissionclickcount=(int)$product['all_commition_click_count'] ;
                        if($product['view_statistics']>0)
                            $ordercountratio=$comissionclickcount*100/$product['view_statistics'];
                        $ordercountratio = is_float($ordercountratio) ==1 ? number_format((float)$ordercountratio, 2, '.', '') : $ordercountratio;
                        echo "<p>".__('user.click') . ' : '.$ordercountratio .'%</p>';
                        ?>
                  </li>
                  <li>
                    <?php
                        if($product['slug']) {
                            $shareUrl = base_url($product['slug']);
                        }else{
                            $shareUrl = $productLink;
                        }
                        ?>
                    <div class="flex align-items-center list-user-action show-tiny-link">
                      
                      <input readonly="readonly" value="<?= $shareUrl ?>" placeholder="<?= $shareUrl ?>" class="form-control input-form-url-<?= $product['form_id'] ?>">

                      <a class="bt-all btn-warning" copyToClipboard="<?= $shareUrl ?>" href="javascript:void(0);" data-bs-toggle="tooltip" title="<?= __('user.copied') ?>">
                        <span class="btn-inner">
                          <i class="far fa-copy"></i>
                        </span>
                      </a>

                      <a class="bt-all btn-danger  btn-model-slug" data-type="product" data-related-id="<?= $product['product_id'] ?>" data-input-class="input-product-url-<?= $product['product_id'] ?>" href="javascript:void(0);" data-bs-toggle="tooltip" title="<?= __('user.slug_settings') ?>">
                        <span class="btn-inner">
                          <i class="fas fa-cog"></i>
                        </span>
                      </a>

                      <a class="bt-all btn-dark btn-group-markettools" target='_black' href="<?= $productLink ?>">
                        <span class="btn-inner">
                          <i class="fa-solid fa-info"></i>
                        </span>
                      </a>

                      <a class="bt-all btn-success btn-group-markettools" onclick="generateCode(<?php echo $product['product_id'];?>,this);"  href="javascript:void(0);">
                        <span class="btn-inner">
                          <i class="fa-solid fa-code"></i>
                        </span>
                        
                      </a>

                      <a class="bt-all btn-danger get-downloads btn-group-markettools"  href="javascript:void(0)" onclick="downloadCode(this, <?= $product['product_id'] ?>, 'form')" data-bs-toggle="tooltip" title="<?= __('user.download_cam_pack') ?>">
                        <span class="btn-inner">
                          <i class="fa-solid fa-download"></i>
                        </span>
                      </a>


                      <a class="bt-all btn-secondary btn-group-markettools qrcode" href="javascript:void(0)" data-id="<?= $shareUrl ?>" data-bs-toggle="tooltip" title="<?= __('user.qrcode') ?>">
                        
                        <span class="btn-inner">
                          <i class="fas fa-walkie-talkie"></i>
                        </span>
                      </a>

                      <a href="javascript:void(0);" class="bt-all btn-success" data-social-share data-share-url="<?= $shareUrl ?>" data-share-title="<?= $product['product_name'];?>" data-share-desc="<?= $product['product_short_description'];?>" data-bs-toggle="tooltip" title="<?= __('user.share_campaign') ?>">
                        <span class="ms-1">
                          <i class="fa-solid fa-share-from-square"></i>
                        </span>
                      </a>
                    </div>
                    <div class="flex align-items-center list-user-action show-mega-link d-none">
                      

                      <input readonly="readonly" value="<?= $shareUrl ?>?id=<?= $userdetails['id'] ?>"placeholder="<?= $shareUrl ?>" class="form-control input-form-url-<?= $product['product_id'] ?>" data-addition-url="?id=<?= $userdetails['id'] ?>">


                      <a class="bt-all btn-warning" copyToClipboard="<?= $shareUrl ?>?id=<?= $userdetails['id'] ?>" href="javascript:void(0);" data-bs-toggle="tooltip" title="<?= __('user.copied') ?>">
                        <span class="btn-inner">
                          <i class="far fa-copy"></i>
                        </span>
                      </a>

                      <a class="bt-all btn-danger btn-model-slug" data-type="product" data-related-id="<?= $product['product_id'] ?>" data-input-class="input-form-url-<?= $product['product_id'] ?>" href="javascript:void(0);" data-bs-toggle="tooltip" title="<?= __('user.slug_settings') ?>">
                        <span class="btn-inner">
                          <i class="fas fa-cog"></i>
                        </span>
                      </a>

                      
                      <a class="bt-all btn-dark btn-group-markettools" target='_black' href="<?= $productLink ?>">
                        <span class="btn-inner">
                          <i class="fa-solid fa-info"></i>
                        </span>
                      </a>

                      <a class="bt-all btn-success btn-group-markettools" onclick="generateCode(<?php echo $product['product_id'];?>,this);"  href="javascript:void(0);">
                        <span class="btn-inner">
                          <i class="fa-solid fa-code"></i>
                        </span>
                      </a>

                      <a class="bt-all btn-danger get-downloads btn-group-markettools"  href="javascript:void(0)" onclick="downloadCode(this, <?= $product['product_id'] ?>, 'form')" data-bs-toggle="tooltip" title="<?= __('user.download_cam_pack') ?>">
                        <span class="btn-inner">
                          <i class="fa-solid fa-download"></i>
                        </span>
                      </a>

                      <a class="bt-all btn-secondary btn-group-markettools qrcode" href="javascript:void(0)" data-id="<?= $shareUrl ?>" data-bs-toggle="tooltip" title="<?= __('user.qrcode') ?>">
                        <span class="btn-inner">
                          <i class="fas fa-walkie-talkie"></i>
                        </span>
                      </a>



                      <a href="javascript:void(0);" class="bt-all btn-success" data-social-share data-share-url="<?= $shareUrl ?>" data-share-title="<?= $product['product_name'];?>" data-share-desc="<?= $product['product_short_description'];?>" data-bs-toggle="tooltip" title="<?= __('user.share_campaign') ?>">
                        <span class="ms-1">
                          <i class="fa-solid fa-share-from-square"></i>
                        </span>
                      </a>
                    </div>
                  </li>
                </ul>
              </div>
              <span class="accordion-button" role="button" data-bs-toggle="collapse" data-bs-target="#collapseOne<?= $index ?>" aria-expanded="true" aria-controls="collapseOne"></span>
            </div>
           
            <div id="collapseOne<?= $index ?>" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
              <div class="accordion-body counting-box">
                <ul class=" d-flex align-items-center justify-content-between">
                  <li>
                    <div class="item-counting">
                      <p>
                        <strong><?= __('admin.price') ?>: </strong><?php echo c_format($product['product_price']); ?>
                      </p>
                    </div>
                  </li>
                  <li>
                    <div class="item-counting">
                      <p>
                        <strong><?= __('admin.sku') ?> : </strong><?php echo $product['product_sku'];?>
                      </p>
                    </div>
                  </li>
                  <li>
                    <div class="item-counting">
                      <p>
                        <strong><?= __('admin.sales_/_commission') ?> : </strong><?php echo $product['order_count'];?> / 
                                <?php echo c_format($product['commission']) ;?>
                      </p>
                    </div>
                  </li>
                  <li>
                    <div class="item-counting ">
                      <p>
                        <strong><?= __('admin.clicks_/_commission') ?>  : </strong><?php echo (int)$product['commition_click_count'];?> / <?php echo c_format($product['commition_click']) ;?>
                      </p>
                    </div>
                  </li>
                  <li>
                    <div class="item-counting ">
                      <p>
                        <strong><?= __('admin.display') ?> : </strong><?= $product['on_store'] == '1' ? __('user.yes') : __('user.no') ?>
                      </p>
                    </div>
                  </li>

                  <li>
                    <div class="item-counting bg-green">
                      <p>
                        <strong><?= __('admin.total') ?>:</strong> <?php echo c_format((float)$product['commition_click'] + (float)$product['commission']); ?>
                      </p>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
        </div>
    <?php } else{ ?>

        <?php 
        $productLink = base_url('store/'. base64_encode($userdetails['id']) .'/product/'.$product['product_slug'] );
        ?>
        <div class="accordion-item <?= $display_class ?>">
            <div class="accordion-header" id="headingOne">
              <div class="market-heade">
                <ul>
                  <li>
                    <img class="accordion-image" src="<?php echo base_url('assets/images/product/upload/thumb/'. $product['featured_image']) ?>" alt="Open Cart">
                  </li> 
                  <li class="name-text">
                    <h6><?= $product['name'] ?>
                    </h6>
                  </li>
                  <li class="offer-text">
                    <div class="wallet-toggle ">
                        <div class="<?= $product['_tool_type'] == 'program' && $product['sale_status'] ? '' : 'd-none' ?>">
                            <?php 
                            $comm = '';
                            if($product['commission_type'] == 'percentage'){
                                if($award_level_status == 1 && $userComission['status'] && $userComission['value'] && $userComission['value'] < $product['commission_sale'])
                                    $commission_sale = $userComission['value'];
                                else
                                    $commission_sale = $product['commission_sale'];

                                $comm = $commission_sale.'%'; 
                            } else if($product['commission_type'] == 'fixed'){ 
                                $comm = c_format($product['commission_sale']); 
                            }
                            echo "<p><b>". __('user.you_will_get')." :</b><small> {$comm} ".__('user.per_sale')."</small></p>"
                            ?>
                        </div>
                    </div>
                    
                    <div class="wallet-toggle ">
                        <div class="<?= $product['_tool_type'] == 'program' && $product['click_status'] ? '' : 'd-none' ?>">
                            <?php 
                            echo "<p><b>".__('user.you_will_get')." :</b><small> ";
                            echo c_format($product["commission_click_commission"]). " ".__('user.per')." ". $product['commission_number_of_click'] ." ".__('user.clicks')." </small></p>";
                            ?>
                        </div>
                    </div>
                    
                    <div class="wallet-toggle ">
                        <div class="<?= $product['_tool_type'] == 'general_click' ? '' : 'd-none' ?>">
                            <?php 
                            echo "<p><b>".__('user.you_will_get')." :</b><small> ";
                            echo c_format($product["general_amount"]). " ".__('user.per')." ". $product['general_click'] ." ".__('user.clicks')." </small></p>";
                            ?>
                        </div>
                    </div>
                    <div class="wallet-toggle ">
                        <div class="<?= ($product['_tool_type'] == 'action' || $product['_tool_type'] == 'single_action') ? '' : 'd-none' ?>">
                            <?php 
                            echo "<p><b>".__('user.you_will_get')." :</b><small> ";
                            echo c_format($product["action_amount"]). " ".__('user.per')." ". $product['action_click'] ." ".__('user.actions')." </small><p>"; 
                            ?>
                        </div>
                    </div>

                    

                    <?php 
                    if($product['recursion']){
                        if($product['recursion'] != 'custom_time'){
                            echo '<p><b>'. __('user.recurring') .' </b> : ' . __('user.'.$product['recursion']).'</p>';
                        } else {
                            echo '<p><b>'. __('user.recurring') .' </b> : '. timetosting($product['recursion_custom_time']).'</p>';
                        }
                    }
                    ?> 
                   </li>
                  <li class="offer-text">
                    <h6><?= $product['total_trigger_count'] ?>
                    </h6>
                  </li>
                  <li class="offer-text">
                      <div class="">

                            <?php 
                            if($product['_tool_type'] == 'program' && $product['sale_status'])  
                            {
                                $conversionratio=0;
                                $totalratiocount=(int)$product['all_sale_count'];
                                if($product['total_trigger_count']>0)
                                $conversionratio=(int)($totalratiocount*100/$product['total_trigger_count']);
                                $conversionratio = is_float($conversionratio) ==1 ? number_format((float)$conversionratio, 2, '.', '') : $conversionratio;
                                echo "<p>".__('user.sale'). ' : '. $conversionratio . '%</p>';
                            }
                              if($product['_tool_type'] == 'action' || $product['_tool_type'] == 'single_action')
                            {
                                $conversionratio=0;
                                $totalratiocount=(int)$product['all_action_click_count'];
                                if($product['total_trigger_count']>0)
                                    $conversionratio=(int)($totalratiocount*100/$product['total_trigger_count']);
                                $conversionratio = is_float($conversionratio) ==1 ? number_format((float)$conversionratio, 2, '.', '') : $conversionratio;
                                echo "<p>".__('user.action'). ' : '. $conversionratio . '%</p>';
                                
                            }
                            if($product['_tool_type'] == 'general_click')
                            {
                                $conversionratio=0;
                                $totalratiocount=(int)$product['all_general_click_count'];
                                if($product['total_trigger_count']>0)
                                    $conversionratio=(int)($totalratiocount*100/$product['total_trigger_count']);
                                $conversionratio = is_float($conversionratio) ==1 ? number_format((float)$conversionratio, 2, '.', '') : $conversionratio;
                                echo "<p>".__('user.click'). ' : '. $conversionratio . '%</p>';
                            }
                            if($product['_tool_type'] == 'program' && $product['click_status']) 
                            {
                                $conversionratio=0;
                                $totalratiocount=(int)$product['all_click_count'];
                                if($product['total_trigger_count']>0)
                                    $conversionratio=(int)($totalratiocount*100/$product['total_trigger_count']);
                                $conversionratio = is_float($conversionratio) ==1 ? number_format((float)$conversionratio, 2, '.', '') : $conversionratio;
                                echo "<p>".__('user.product_click'). ' : '. $conversionratio . '%</p>';
                            }
                            
                            ?>
                        </div>

                  </li>
                  <li>
                    <?php
                        if($product['slug']) {
                            $shareUrl = base_url($product['slug']);
                        }else{
                            $shareUrl = $product['redirectLocation'][0];
                        }
                        ?>
                    <div class="flex align-items-center list-user-action show-tiny-link">
                      
                    

                      <input readonly="readonly" value="<?= $shareUrl ?>" placeholder="<?= $shareUrl ?>" class="form-control input-<?= $product['_tool_type'] ?>-url-<?= $product['id'] ?>">

                      <a class="bt-all btn-warning" copyToClipboard="<?= $shareUrl ?>" href="javascript:void(0);" data-bs-toggle="tooltip" title="<?= __('user.copied') ?>">
                        <span class="btn-inner">
                          <i class="far fa-copy"></i>
                        </span>
                      </a>

                      <a class="bt-all btn-danger  btn-model-slug" data-type="<?= $product['_tool_type'] ?>" data-related-id="<?= $product['id'] ?>" data-input-class="input-<?= $product['_tool_type'] ?>-url-<?= $product['id'] ?>" href="javascript:void(0);" data-bs-toggle="tooltip" title="<?= __('user.slug_settings') ?>">
                        <span class="btn-inner">
                          <i class="fas fa-cog"></i>
                        </span>
                      </a>

                      <a class="bt-all btn-dark btn-group-markettools get-terms" data-id="<?= $product['id'] ?>" href="javascript:void(0);" data-bs-toggle="tooltip" title="<?= __('user.terms') ?>">
                        <span class="btn-inner">
                          <i class="fa-solid fa-info"></i>
                        </span>
                      </a>

                      <a class="bt-all btn-success btn-group-markettools get-code" data-id="<?= $product['id'] ?>" href="javascript:void(0);" data-bs-toggle="tooltip" title="<?= __('user.get_code') ?>">
                        <span class="btn-inner">
                          <i class="fa-solid fa-code"></i>
                        </span>

                      </a>

                      <a class="bt-all btn-danger get-downloads btn-group-markettools"  href="javascript:void(0)" onclick="downloadCode(this, <?= $product['id'] ?>, 'tool')" data-bs-toggle="tooltip" title="<?= __('user.download_cam_pack') ?>">
                        
                        <span class="btn-inner">
                          <i class="fa-solid fa-download"></i>
                        </span>
                      </a>


                      <a class="bt-all btn-secondary btn-group-markettools qrcode" href="javascript:void(0)" data-id="<?= $shareUrl ?>" data-bs-toggle="tooltip" title="<?= __('user.qrcode') ?>">
                        <span class="btn-inner">
                          <i class="fas fa-walkie-talkie"></i>
                        </span>
                      </a>



                      <a href="javascript:void(0);" class="bt-all btn-success" data-social-share data-share-url="<?= $shareUrl ?>" data-share-title="<?= $product['name'];?>" data-bs-toggle="tooltip" title="<?= __('user.share_campaign') ?>" data-bs-toggle="tooltip" title="<?= __('user.share_campaign') ?>">
                        <span class="ms-1">
                          <i class="fa-solid fa-share-from-square"></i>
                        </span>
                      </a>
                    </div>
                    <div class="flex align-items-center list-user-action show-mega-link d-none">
                      

                      <input readonly="readonly" value="<?= $shareUrl ?>?id=<?= $userdetails['id'] ?>"placeholder="<?= $shareUrl ?>" class="form-control input-<?= $product['_tool_type'] ?>-url-<?= $product['id'] ?>" data-addition-url="?id=<?= $userdetails['id'] ?>">


                      <a class="bt-all btn-warning" copyToClipboard="<?= $shareUrl ?>?id=<?= $userdetails['id'] ?>" href="javascript:void(0);" data-bs-toggle="tooltip" title="<?= __('user.copied') ?>">
                        <span class="btn-inner">
                          <i class="far fa-copy"></i>
                        </span>
                      </a>

                      <a class="bt-all btn-danger btn-model-slug" data-type="<?= $product['_tool_type'] ?>" data-related-id="<?= $product['id'] ?>" data-input-class="input-<?= $product['_tool_type'] ?>-url-<?= $product['id'] ?>" href="javascript:void(0);" data-bs-toggle="tooltip" title="<?= __('user.slug_settings') ?>">
                        <span class="btn-inner">
                          <i class="fas fa-cog"></i>
                        </span>
                      </a>
                      
                      <a class="bt-all btn-dark get-terms btn-group-markettools" data-id="<?= $product['id'] ?>" href="javascript:void(0);" data-bs-toggle="tooltip" title="<?= __('user.terms') ?>">
                        <span class="btn-inner">
                          <i class="fa-solid fa-info"></i>
                        </span>
                      </a>

                      <a class="bt-all btn-success btn-group-markettools get-code" data-id="<?= $product['id'] ?>" href="javascript:void(0);" data-bs-toggle="tooltip" title="<?= __('user.get_code') ?>">
                        <span class="btn-inner">
                          <i class="fa-solid fa-code"></i>
                        </span>
                      </a>

                      <a class="bt-all btn-danger get-downloads btn-group-markettools"  href="javascript:void(0)" onclick="downloadCode(this, <?= $product['id'] ?>, 'tool')" data-bs-toggle="tooltip" title="<?= __('user.download_cam_pack') ?>">
                          <span class="btn-inner">
                              <i class="fa-solid fa-download"></i>
                          </span>
                      </a>

                      <a class="bt-all btn-secondary btn-group-markettools qrcode" href="javascript:void(0)" data-id="<?= $shareUrl ?>" data-bs-toggle="tooltip" title="<?= __('user.qrcode') ?>">
                        <span class="btn-inner">
                          <i class="fas fa-walkie-talkie"></i>
                        </span>
                      </a>

                      <a href="javascript:void(0);" class="bt-all btn-success" data-social-share data-share-url="<?= $shareUrl ?>" data-share-title="<?= $product['name'];?>" data-bs-toggle="tooltip" title="<?= __('user.share_campaign') ?>">
                        <span class="ms-1">
                          <i class="fa-solid fa-share-from-square"></i>
                        </span>
                      </a>
                    </div>
                  </li>
                </ul>
              </div>
              <span class="accordion-button" role="button" data-bs-toggle="collapse" data-bs-target="#collapseOne<?= $index ?>" aria-expanded="true" aria-controls="collapseOne"></span>
            </div>
           
            <div id="collapseOne<?= $index ?>" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
              <div class="accordion-body counting-box">
                <ul class=" d-flex align-items-center justify-content-between">
                    <?php 
                        if($product['_tool_type'] == 'program' && $product['sale_status']){ ?>
                  <li>
                    <div class="item-counting">
                      <p>
                        <strong><?= __('user.sale_count') ?>: </strong><?php echo (int)$product['total_sale_count']; ?>
                      </p>
                    </div>
                  </li>
                  <li>
                    <div class="item-counting">
                      <p>
                        <strong><?= __('user.sale_amount') ?>: </strong><?php echo c_format((int)$product['total_sale_amount']); ?>
                      </p>
                    </div>
                  </li>
                  <?php } if($product['_tool_type'] == 'program' && $product['click_status']){?>
                  <li>
                    <div class="item-counting">
                      <p>
                        <strong><?= __('user.click_count') ?> : </strong><?php echo (int)$product['total_click_count'];?>
                      </p>
                    </div>
                  </li>
                  <li>
                    <div class="item-counting bg-green">
                      <p>
                        <strong><?= __('user.click_amount') ?> : </strong><?php echo (int)$product['total_click_amount'];?>
                      </p>
                    </div>
                  </li>
                  <?php } if($product['_tool_type'] == 'general_click'){?>
                  <li>
                    <div class="item-counting ">
                      <p>
                        <strong><?= __('user.general_count') ?>  : </strong><?php echo (int)$product['total_general_click_count'];?>
                      </p>
                    </div>
                  </li>
                  <li>
                    <div class="item-counting bg-green">
                      <p>
                        <strong><?= __('user.general_amount') ?> : </strong><?php echo $product['total_general_click_amount'];?>
                      </p>
                    </div>
                  </li>
                  <?php } if($product['_tool_type'] == 'action' || $product['_tool_type'] == 'single_action'){?>
                  <li>
                    <div class="item-counting ">
                      <p>
                        <strong><?= __('user.action_count') ?> : </strong><?= (int)$product['total_action_click_count'] ?>
                      </p>
                    </div>
                  </li>

                  <li>
                    <div class="item-counting bg-green">
                      <p>
                        <strong><?= __('user.action_amount') ?>:</strong> <?= $product['total_action_click_amount']; ?>
                      </p>
                    </div>
                  </li>
                 <?php }
                        ?>
                </ul>
              </div>
            </div>
        </div>
        
    <?php } ?>
<?php } ?>
<div class="pagination-div bg-area ps-3 pt-2"><?php echo $pagination?></div>
