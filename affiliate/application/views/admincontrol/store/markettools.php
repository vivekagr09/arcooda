<?php

    $db =& get_instance();

    $userdetails=$db->userdetails();

?>
<div class="row">

    <div class="col-sm-12">

        <div class="card m-b-20">

            <div class="card-body">

                <h4 class="mt-0 header-title m-b-30"> <?= __('admin.affiliates_links') ?>...</h4>

                <div class="clearfix"></div>

                <div class="card-body p-0" style="height: 100%;overflow: auto;">

                    

                   <?php if ($data_list==null) {?>
                        <div class="text-center mt-5">
                         <div class="d-flex justify-content-center align-items-center flex-column mt-5">
                             <i class="fas fa-exchange-alt fa-5x text-muted"></i>
                             <h3 class="text-muted"><?= __('admin.no_data_found') ?></h3>
                         </div>
                        </div>
                    <?php } else { ?>

                        <div class="table-responsive b-0" >
                            <table id="product-list" class="table table-no-wrap">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="1"></th>
                                        <th><?= __('admin.image') ?></th>
                                        <th><?= __('admin.name') ?></th>
                                        <th><?= __('admin.commission') ?></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </thead>

                                <?php $pagination = 100; ?>

                                <tbody>

                                    <?php foreach($data_list as $index => $product){ ?>

                                        <?php

                                            $display_class = $index >= $pagination ? 'd-none' : '';

                                        ?>

                                        <?php if(isset($product['is_form'])){ ?>

                                            <tr class="<?= $display_class ?>">

                                                <td class="text-center">

                                                    <button type="button" class="toggle-child-tr"><i class="fa fa-plus"></i></button>

                                                </td>

                                                <td><img width="50px" height="50px" src="<?php echo base_url($product['fevi_icon']) ?>" ></td>

                                                <td>

                                                    <?= $product['title'] ?>

                                                    <div>

                                                        <small>

                                                            <a href="<?= $product['public_page'] ?>"  target='_black'><?= __('admin.public_page'); ?></a> /

                                                            <a href="javascript:void(0);" onclick="generateCodeForm(<?php echo $product['form_id'];?>,this);" ><?= __('admin.get_ncode') ?></a>

                                                        </small>    

                                                    </div>

                                                </td>

                                                <td>

                                                    <?php

                                                        echo "<b>You Will Get</b> ";

                                                        if($product['sale_commision_type'] == 'default'){

                                                            $commissionType = $form_default_commission['product_commission_type'];

                                                            if($form_default_commission['product_commission_type'] == 'percentage'){

                                                                echo $form_default_commission['product_commission'] .'% '.__('admin.per_sale');

                                                            }

                                                            else if($form_default_commission['product_commission_type'] == 'Fixed'){

                                                                echo c_format($form_default_commission['product_commission']) .' '.__('admin.per_sale');

                                                            }

                                                        }

                                                        else if($product['sale_commision_type'] == 'percentage'){

                                                            echo $product['sale_commision_value'] .'% '.__('admin.per_sale');

                                                        }

                                                        else if($product['sale_commision_type'] == 'fixed'){

                                                            echo c_format($product['sale_commision_value']) .' '.__('admin.per_sale');

                                                        }

                                                        

                                                        echo "<br> <b>You Will Get</b> ";

                                                        if($product['click_commision_type'] == 'default'){

                                                            $commissionType = $form_default_commission['product_commission_type'];

                                                            if($form_default_commission['product_commission_type'] == 'percentage'){

                                                                echo $form_default_commission['product_ppc'] .'% '.__('admin.of_per').' '. $form_default_commission['product_noofpercommission'] .' '.__('admin.click');

                                                            }

                                                            else if($form_default_commission['product_commission_type'] == 'Fixed'){

                                                                echo c_format($form_default_commission['product_ppc']) .' '.__('admin.of_per').' '. $form_default_commission['product_noofpercommission'] .' '.__('admin.click');

                                                            }

                                                        }

                                                        else if($product['click_commision_type'] == 'custom') {

                                                            echo c_format($product['click_commision_ppc']) .' '.__('admin.of_per').' '. $product['click_commision_per'] .' '.__('admin.click');

                                                        }

                                                        ?>

                                                        <div>
                                                            <?php 
                                                                if($product['form_recursion_type']){
                                                                    if($product['form_recursion_type'] == 'custom'){
                                                                        if($product['form_recursion'] != 'custom_time'){
                                                                            echo '<b>'. __('admin.recurring') .' </b> : ' . __('admin.'. $product['form_recursion']);
                                                                        } else {
                                                                            echo '<b>'. __('admin.recurring') .' </b> : '. timetosting($product['recursion_custom_time']);
                                                                        }
                                                                    } else{
                                                                        if($form_setting['form_recursion'] == 'custom_time' ){
                                                                            echo '<b>'. __('admin.recurring') .' </b> : '. timetosting($form_setting['recursion_custom_time']);
                                                                        } else {
                                                                            echo '<b>'. __('admin.recurring') .' </b> : '. __('admin.'. $form_setting['form_recursion']);
                                                                        }
                                                                    }
                                                                }
                                                            ?>
                                                        </div>

                                                </td>

                                                <td>

                                                    <div class="form-group m-0">

                                                        <div class="input-group copy-input">

                                                            <input readonly="readonly" value="<?= $product['public_page'] ?>" class="form-control">

                                                            <button type="button" copyToClipboard="<?= $product['public_page'] ?>" class="input-group-addon">

                                                            </button>

                                                        </div>

                                                    </div>

                                                </td>

                                                <td>

                                                <span class="btn btn-md btn-primary" data-social-share data-share-url="<?= $product['public_page']; ?>?id=<?= $userdetails['id'] ?>" data-share-title="<?= $product['product_name'];?>" data-share-desc="<?= $product['product_short_description'];?>"><i class="fa fa-share-alt" aria-hidden="true"></i></span>

                                                </td>

                                            </tr>

                                            <tr class="detail-tr">

                                                <td colspan="100%">

                                                    <div>

                                                        <ul>

                                                            <li><b><?= __('admin.coupon_code'); ?>: </b> <span><?= $product['coupon_code'] ? $product['coupon_code'] : 'N/A' ?></span></li>

                                                            <li><b><?= __('admin.coupon_use'); ?>: </b> <span><?= ($product['coupon_name'] ? $product['coupon_name'] : '-').' / '.$product['count_coupon'] ?></span></li>

                                                            <li><b><?= __('admin.sales_commission'); ?>: </b> <span><?= (int)$product['count_commission'].' / '.c_format($product['total_commission']) ?></span></li>

                                                            <li><b><?= __('admin.clicks_commission'); ?>: </b> <span><?= (int)$product['commition_click_count'].' / '.c_format($product['commition_click']); ?></span></li>

                                                            <li><b><?= __('admin.total_commission'); ?>: </b> <span><?= c_format($product['total_commission']+$product['commition_click']); ?></span></li>

                                                        </ul>

                                                    </div>

                                                </td>

                                            </tr>

                                        <?php } else if(isset($product['is_product'])) { ?>

                                            <?php 

                                                $productLink = base_url('store/'. base64_encode($userdetails['id']) .'/product/'.$product['product_slug'] );

                                            ?>

                                            <tr class="<?= $display_class ?>">

                                                <td class="text-center">                                                    

                                                    <button type="button" class="toggle-child-tr"><i class="fa fa-plus"></i></button>

                                                </td>

                                                <td><img width="50px" height="50px" src="<?php echo base_url('assets/images/product/upload/thumb/'. $product['product_featured_image']) ?>" ></td>

                                                <td>

                                                    <?php echo $product['product_name'];?>

                                                    <div>

                                                        <small>

                                                            <a target="_blank" href="<?= $productLink ?>"><?= __('admin.public_npage') ?></a> / 

                                                            <a href="javascript:void(0);" onclick="generateCode(<?php echo $product['product_id'];?>,this);" ><?= __('admin.get_ncode') ?></a>

                                                        </small>

                                                    </div>        

                                                </td>

                                                <td>
                                                    
                                                    <?php if(!empty($product['product_commision_type'])) { ?>

                                                        <b><?= __('admin.you_will_get') ?></b> : 

                                                        <?php

                                                        if($product['product_commision_type'] == 'default'){

                                                            if($default_commition['product_commission_type'] == 'percentage'){

                                                                echo $default_commition['product_commission']. "% ".__('admin.per_sale');

                                                            } else {

                                                                echo c_format($default_commition['product_commission']) ."  ".__('admin.per_sale');

                                                            }

                                                        } else if($product['product_commision_type'] == 'percentage'){

                                                            echo $product['product_commision_value']. "%  ".__('admin.per_sale');

                                                        } else{

                                                            echo c_format($product['product_commision_value']) ."  ".__('admin.per_sale');

                                                        }

                                                        ?>

                                                    <br><b><?= __('admin.you_will_get') ?></b> :

                                                    <?php

                                                        if($product['product_click_commision_type'] == 'default'){

                                                            echo c_format($default_commition['product_ppc']) ." ".__('admin.per')." {$default_commition['product_noofpercommission']} ".__('admin.click');   

                                                            echo "</small>";

                                                        } else {

                                                            echo c_format($product['product_click_commision_per']) ." ".__('admin.per')." {$product['product_click_commision_ppc']} ".__('admin.click');

                                                        }

                                                        ?>
                                                        
                                                        <?php } else { ?>
                                                            
                                                            <b><?= __('admin.product_comission_setting_not_available') ?></b>
                                                            
                                                        <?php } ?>

                                                        <div>
                                                            <?php 
                                                                if($product['product_recursion_type']){
                                                                    if($product['product_recursion_type'] == 'custom'){
                                                                        if($product['product_recursion'] != 'custom_time'){
                                                                            echo '<b>'. __('admin.recurring') .' </b> : ' . __('admin.'.$product['product_recursion']);
                                                                        } else {
                                                                            echo '<b>'. __('admin.recurring') .' </b> : '. timetosting($product['recursion_custom_time']);
                                                                        }
                                                                    } else{
                                                                        if($pro_setting['product_recursion'] == 'custom_time' ){
                                                                            echo '<b>'. __('admin.recurring') .' </b> : '. timetosting($pro_setting['recursion_custom_time']);
                                                                        } else {
                                                                            echo '<b>'. __('admin.recurring') .' </b> : '. __('admin.'.$pro_setting['product_recursion']);
                                                                        }
                                                                    }
                                                                }
                                                            ?>
                                                        </div>

                                                </td>

                                                <td>

                                                    <div class="form-group m-0">

                                                        <div class="input-group copy-input">

                                                            <input readonly="readonly" value="<?= $productLink ?>" class="form-control">

                                                            <button type="button" copyToClipboard="<?= $productLink ?>" class="input-group-addon">

                                                            

                                                            </button>

                                                        </div>

                                                    </div>

                                                </td>

                                                <td>

                                                <span class="btn btn-md btn-primary" data-social-share data-share-url="<?= $productLink; ?>?id=<?= $userdetails['id'] ?>" data-share-title="<?= $product['product_name'];?>" data-share-desc="<?= $product['product_short_description'];?>"><i class="fa fa-share-alt" aria-hidden="true"></i></span>

                                                </td>

                                            </tr>

                                            <tr class="detail-tr">

                                                <td colspan="100%">

                                                    <div>

                                                        <ul>

                                                            <li><b><?= __('admin.price') ?> :</b><span><?php echo c_format($product['product_price']); ?></span></li>

                                                            <li><b><?= __('admin.sku') ?> :</b><span><?php echo $product['product_sku'];?></span></li>

                                                            <li>

                                                                <b><?= __('admin.sales_/_commission') ?> :</b>

                                                                <span>

                                                                <?php echo $product['order_count'];?> / 

                                                                <?php echo c_format($product['commission']) ;?>

                                                                </span>

                                                            </li>

                                                            <li>

                                                                <b><?= __('admin.clicks_/_commission') ?> :</b>

                                                                <span>

                                                                <?php echo (int)$product['commition_click_count'];?> / <?php echo c_format($product['commition_click']) ;?>

                                                                </span>

                                                            </li>

                                                            <li>

                                                                <b><?= __('admin.total') ?> :</b>

                                                                <span>

                                                                <?php echo c_format((float)$product['commition_click'] + (float)$product['commission']); ?>

                                                                </span>

                                                            </li>

                                                            <li><b><?= __('admin.display') ?> :</b> <span><?= $product['on_store'] == '1' ? __('admin.yes') : __('admin.no') ?></span></li>

                                                        </ul>

                                                    </div>

                                                </td>

                                            </tr>

                                        <?php } else { ?>

                                            <tr class="<?= $display_class ?>">

                                                <td><button type="button" class="toggle-child-tr"><i class="fa fa-plus"></i></button></td>

                                                 <td>
                                                    <img width="50px" height="50px" src="<?php echo base_url('assets/images/product/upload/thumb/'. $product['featured_image']) ?>" >
                                                </td>

                                                <td>

                                                    <?= $product['name'] ?>

                                                    <div>

                                                        <small>

                                                            <a class="get-code" href="javascript:void(0)" data-id="<?= $product['id'] ?>"><?= __('admin.get_code') ?></a>

                                                        </small>

                                                    </div>          

                                                </td>

                                                <td>

                                                    <div class="wallet-toggle ">

                                                        <div class="<?= $product['_tool_type'] == 'program' && $product['sale_status'] ? '' : 'd-none' ?>">

                                                            <?php 

                                                                $comm = '';

                                                                if($product['commission_type'] == 'percentage'){ $comm = $product['commission_sale'].'%'; }

                                                                else if($product['commission_type'] == 'fixed'){ $comm = c_format($product['commission_sale']); }

                                                                

                                                                echo "<b>".__('admin.you_can_earn')." :</b><small> {$comm} ".__('admin.per_sale')." </small><br>";

                                                                ?>

                                                        </div>

                                                    </div>

                                                    <div class="wallet-toggle ">

                                                        <div class="<?= $product['_tool_type'] == 'program' && $product['click_status'] ? '' : 'd-none' ?>">

                                                            <?php 

                                                                echo "<b>".__('admin.you_can_earn')." :</b><small> ";

                                                                echo c_format($product["commission_click_commission"]). " per ". $product['commission_number_of_click'] ." ".__('admin.clicks')." </small><br>";

                                                                ?>

                                                        </div>

                                                    </div>

                                                    <div class="wallet-toggle ">

                                                        <div class="<?= $product['_tool_type'] == 'general_click' ? '' : 'd-none' ?>">

                                                            <?php 

                                                                echo "<b>".__('admin.you_can_earn')." :</b><small> ";

                                                                echo c_format($product["general_amount"]). " ".__('admin.per')." ". $product['general_click'] ." ".__('admin.general_click')." </small><br>";

                                                                ?>

                                                        </div>

                                                    </div>

                                                    <div class="wallet-toggle ">

                                                        <div class="<?= ($product['_tool_type'] == 'action' || $product['_tool_type'] == 'single_action') ? '' : 'd-none' ?>">

                                                            <?php 

                                                                echo "<b>".__('admin.you_can_earn')." :</b><small> ";

                                                                echo c_format($product["action_amount"]). " ".__('admin.per')." ". $product['action_click'] ." ".__('admin.actions')." </small><br>"; 

                                                                ?>

                                                        </div>

                                                    </div>

                                                    <?php 
                                                        if($product['recursion']){
                                                            if($product['recursion'] != 'custom_time'){
                                                                echo '<b>'. __('admin.recurring') .' </b> : ' . __('admin.'.$product['recursion']);
                                                            } else {
                                                                echo '<b>'. __('admin.recurring') .' </b> : '. timetosting($product['recursion_custom_time']);
                                                            }
                                                        }
                                                    ?>  

                                                </td>

                                                <td>

                                                    <div class="form-group m-0">

                                                        <div class="input-group copy-input">

                                                            <input readonly="readonly" value="<?= $product['redirectLocation'][0] ?>" class="form-control">

                                                            <button type="button" copyToClipboard="<?= $product['redirectLocation'][0] ?>" class="input-group-addon">

                                                            </button>

                                                        </div>

                                                    </div>

                                                </td>

                                                <td class="-d-sm-table-cell -d-none">

                                                <span class="btn btn-md btn-primary" data-social-share data-share-url="<?= $product['redirectLocation'][0]; ?>?id=<?= $userdetails['id'] ?>" data-share-title="<?= $product['product_name'];?>" data-share-desc="<?= $product['product_short_description'];?>"><i class="fa fa-share-alt" aria-hidden="true"></i></span>

                                                </td>

                                            </tr>

                                            <tr class="detail-tr">

                                                <td colspan="100%">

                                                    <div>
                                                        <ul>

                                                            

                                                            <?php 

                                                                if($product['_tool_type'] == 'program' && $product['sale_status']){ 

                                                                    echo "<li><b>".__('admin.sale_count')." :</b> <span>". (int)$product['total_sale_count'] ."</span></li>";
                                                                    
                                                                    echo "<li><b>".__('admin.sale_amount')." :</b> <span>". c_format($product['total_order_amount']) ."</span></li>";

                                                                    echo "<li><b>".__('admin.sale_commission_amount')." :</b> <span>". $product['total_sale_amount'] ."</span></li>";

                                                                }



                                                                if($product['_tool_type'] == 'program' && $product['click_status']){

                                                                    echo "<li><b>".__('admin.click_count')." :</b> <span>". (int)$product['total_click_count'] ."</span></li>";

                                                                    echo "<li><b>".__('admin.click_amount')." :</b> <span>". $product['total_click_amount'] ."</span></li>";

                                                                }



                                                                if($product['_tool_type'] == 'general_click'){

                                                                    echo "<li><b>".__('admin.general_count')." :</b> <span>". (int)$product['total_general_click_count'] ."</span></li>";

                                                                    echo "<li><b>".__('admin.general_amount')." :</b> <span>". $product['total_general_click_amount'] ."</span></li>";

                                                                }



                                                                if($product['_tool_type'] == 'action' ||  $product['_tool_type'] == 'single_action'){

                                                                    echo "<li><b>".__('admin.action_count')." :</b> <span>". (int)$product['total_action_click_count'] ."</span></li>";

                                                                    echo "<li><b>".__('admin.action_amount')." :</b> <span>". $product['total_action_click_amount'] ."</span></li>";

                                                                }

                                                            ?>

                                                        </ul>

                                                    </div>

                                                </td>

                                            </tr>

                                        <?php } ?>

                                    <?php } ?>

                                </tbody>

                                <?php if($index > $pagination){ ?>

                                    <tfoot>

                                        <tr>

                                            <td colspan="100%">

                                                <button type="button" class="btn btn-primary show-more"><?= __('admin.show_more') ?></button>

                                            </td>

                                        </tr>

                                    </tfoot>

                                <?php } ?>

                            </table>

                        </div>



                        <div class="text-right">

                            <ul class="pagination">

                                <?= $pagination_link ?>

                            </ul>

                        </div>



                    <?php } ?>

                </div>

            </div>

        </div>

    </div>

</div>



<div class="modal" id="model-codemodal">

  <div class="modal-dialog">

    <div class="modal-content">

      <div class="modal-body"></div>

      <div class="modal-footer">

        <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><?= __('admin.close') ?></button>

      </div>

    </div>

  </div>

</div>

<div class="modal" id="model-codeformmodal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
            <?= __('admin.close') ?></button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="integration-code"><div class="modal-dialog"><div class="modal-content"></div></div></div>

<?= $social_share_modal; ?>

<script type="text/javascript">

    $(".get-code").on('click',function(){

        $this = $(this);

        $.ajax({

            url:'<?= base_url("integration/tool_get_code/admincontrol") ?>',

            type:'POST',

            dataType:'json',

            data:{id:$this.attr("data-id")},

            beforeSend:function(){ $this.btn("loading"); },

            complete:function(){ $this.btn("reset"); },

            success:function(json){

                if(json['html']){

                    $("#integration-code .modal-content").html(json['html']);

                    $("#integration-code").modal("show");

                }

            },

        })

    })



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

    $(".show-more").on('click',function(){

        $(this).parents("tfoot").remove();

        $("#product-list tr.d-none").hide().removeClass('d-none').fadeIn();

    });



    function generateCode(affiliate_id,t){

        $this = $(t);

        $.ajax({

            url:'<?php echo base_url();?>admincontrol/generateproductcode/'+affiliate_id,

            type:'POST',

            dataType:'html',

            beforeSend:function(){

                $this.btn("loading");

            },

            complete:function(){

                $this.btn("reset");

            },

            success:function(json){

                $('#model-codemodal .modal-body').html(json)

                $("#model-codemodal").modal("show")

            },

        })

    }



    function generateCodeForm(form_id,t){ 

        $this = $(t);

        $.ajax({

            url:'<?php echo base_url();?>admincontrol/generateformcode/'+form_id,

            type:'POST',

            dataType:'html',

            beforeSend:function(){

                $this.btn("loading");

            },

            complete:function(){

                $this.btn("reset");

            },

            success:function(json){

                $('#model-codeformmodal .modal-body').html(json)

                $("#model-codeformmodal").modal("show")

            },

        })

    }

   

   

</script>