<div class="modal-dialog modal-xl">
    <div class="modal-content">
        <div class="modal-header">
            <h6 class="modal-title m-0 text-center d-block w-100">
                <?= $name." ".__('admin.integration_on_website') ?> 
            </h6>
            <button type="button" class="btn-close" aria-label="Close" data-bs-dismiss="modal"></button>
        </div>

        <?php 
            function ___h($text,$lan){
                $text = implode("\n", $text);
                $text = htmlentities($text);
                $text = '<div class="lang-copy" ><div class="copy">'.__('admin.copy').'</div><pre class="language-'.$lan.'"><code class="language-'.$lan.'">'.$text.'</code></pre></div>';
                return $text;
            }

            $base_url  = base_url();
        ?>

        <link rel="stylesheet" type="text/css" href="<?= base_url('assets/integration/prism/css.css') ?>?v=<?= av() ?>">
        <script type="text/javascript" src="<?= base_url('assets/integration/prism/js.js') ?>"></script>
        <script type="text/javascript" src="<?= base_url('assets/integration/prism/clipboard.min.js') ?>"></script>
        
        <div class="modal-body m-1 pl-1 pr-1">
            <div class="modal-ins">
                <div class="modal-ins-head">
                    <h6><?= __('admin.tool_type') ?> : <?= parseIntegrationType($tool['tool_type']); ?></h6>
                    <?php if($tool['tool_type'] == 'program'){ ?>
                    <h6><?= __('admin.integration_plugin_name') ?> : <?= isset($integration_plugins[$tool['tool_integration_plugin']]) ? $integration_plugins[$tool['tool_integration_plugin']]['name'] : "<span class='text-muted'>".__('admin.not_available_dashed')."</span>" ?></h6>
                    <?php } ?>
                    <div class="row auto-fill-filed">
                        <div class="col-sm-6">
                            <label class="control-label"><?= __('admin.you_can_edit_website_link_desc') ?></label>
                            <input type="text" name="WebsiteUrl" data-default='WebsiteUrl' class="form-control" placeholder="<?= __('admin.enter_website_url') ?>" value="<?= $target_link ?>">
                        </div>
                    </div>
                </div>
                
                <div class="modal-ins-body">

                    <?php 

                if(isset($tool_integration_plugin_html)) {
                    echo $tool_integration_plugin_html;
                } else { ?>
                    <div>
                        <section>
                            <h2><?= __('admin.common_tracking_script') ?></h2>
                            <div>
                                <p><?= __('admin.add_following_script_to_all_pages') ?></p>
                                <?php
                                    $code = array();
                                    $code[] = '<script type="text/javascript" src="'. $base_url .'integration/general_integration"></script>';
                                    echo ___h($code,'html');
                                ?>
                            </div>
                        </section>
                        <?php if(in_array($tool_type, ['general_click'])){ ?>
                            <h2><?= __('admin.general_click_tracking') ?></h2>
                            <div>
                                <p><?= __('admin.use_follow_code_to_track_general_cicks') ?></p>    
                                <?php
                                    $code = array();
                                    $code[] = '<script type="text/javascript" src="'. $base_url .'integration/general_integration"></script>';
                                    $code[] = '<script type="text/javascript">';
                                    $code[] = ' AffTracker.setWebsiteUrl( "WebsiteUrl" );';
                                    $code[] = ' AffTracker.generalClick( "'. $general_code .'" );';
                                    $code[] = '</script>';
                                    
                                    echo ___h($code,'html');
                                ?>

                                <p>
                                    <h6><?= __('admin.all_possible_tracking_para') ?></h6>
                                    <div class="well">
                                        <strong>WebsiteUrl</strong>       : <?= __('admin.website_root_url') ?> <br>
                                        <strong>general_code</strong> : <?= __('admin.unique_code_of_general_click_val_msg') ?>
                                    </div>
                                </p>
                            </div>
                        <?php } ?>
                        
                        <?php if(in_array($tool_type, ['action']) || in_array($tool_type, ['single_action'])){ ?>
                            <h2><?= __('admin.cpa_cost_per_action') ?></h2>
                            <div>
                                <p><?= __('admin.about_any_action_description') ?></p>
                                <p><?= __('admin.under_integration_tools_create_new') ?></p>
                                <p><?= __('admin.last_step_javascript_integration') ?></p>
                                <p><?= __('admin.about_registration_page_tracking_info') ?></p>

                                <?php
                                    $code = array();
                                    $code[] = '<script type="text/javascript" src="'. $base_url .'integration/general_integration"></script>';
                                    $code[] = '<script type="text/javascript">';
                                    $code[] = ' AffTracker.setWebsiteUrl( "WebsiteUrl" );';
                                    $code[] = ' //set custom value';
                                    $code[] = ' AffTracker.setData("custom_data_1","value");';
                                    $code[] = ' AffTracker.setData("custom_data_2","value");';
                                    $code[] = ' AffTracker.setData("custom_data_...","value");';
                                    $code[] = ' AffTracker.createAction( "'. $action_code .'" )';

                                    $code[] = '</script>';
                                    
                                    echo ___h($code,'html');
                                ?>

                                <p>
                                    <h6><?= __('admin.all_possible_tracking_para') ?></h6>
                                    <div class="well">
                                        <strong>WebsiteUrl</strong>       : <?= __('admin.website_root_url') ?> <br>
                                        <strong>actionCode</strong>       : <?= __('admin.general_click_action_code') ?><br>
                                    </div>
                                </p>
                            </div>
                        <?php } ?>

                        <?php if(in_array($tool_type, ['program'])){ ?>
                            <h2><?= __('admin.order_tracking') ?></h2>
                            <div>
                                <p><?= __('admin.order_tracking_intruction_1') ?></p>

                                <h6><?= __('admin.php_example_using_js') ?></h6>
                                <?php
                                    $code = array();
                                    $code[] = '<script type="text/javascript" src="'. $base_url .'integration/general_integration"></script>';
                                    $code[] = '<script type="text/javascript">';
                                    $code[] = ' AffTracker.setWebsiteUrl( "WebsiteUrl" );';
                                    $code[] = ' ';
                                    $code[] = ' //AffTracker.setData( "custom_data_1", "value" );';
                                    $code[] = ' //AffTracker.setData( "custom_data_2", "value" );';
                                    $code[] = ' //AffTracker.setData( "custom_data_...", "value" );';
                                    $code[] = ' ';
                                    $code[] = ' AffTracker.add_order({';
                                    $code[] = '     order_id        : "<?php echo $variable_OrderId ?>",';
                                    $code[] = '     order_currency  : "<?php echo $variable_OrderCurrency ?>",';
                                    $code[] = '     order_total     : "<?php echo $variable_OrderTotal ?>",';
                                    $code[] = '     product_ids     : "<?php echo $variable_ProductIDs ?>"';
                                    $code[] = ' })';
                                    $code[] = '</script>';
                                    
                                    echo ___h($code,'html');
                                ?>
                                </br>


                                <h6><?= __('admin.cust_order_integration_api_using_phplib') ?></h6>
                                <p><?= __('admin.order_tracking_intruction_2') ?> <a target="_blank" href="<?= base_url('integration/download_plugin/php_api_library') ?>"><?= __('admin.download_library') ?></a>
                                <?php
                                    $code = array();
                                    $code[] = '<?php';
                                    $code[] = 'require "affiliatepro.php";';
                                    $code[] = '';
                                    $code[] = '$tracking = new AffiliatePro();';
                                    $code[] = '$tracking->orderId = $variable_OrderId;';
                                    $code[] = '$tracking->orderCurrency = $variable_OrderCurrency;';
                                    $code[] = '$tracking->orderTotal = $variable_OrderTotal;';
                                    $code[] = '$tracking->productIds = array($variable_ProductIDs);';
                                    $code[] = '$tracking->websiteUrl = "WebsiteUrl";';
                                    $code[] = '';
                                    $code[] = '//set custom value';
                                    $code[] = '//$tracking->setData("custom_data_1","value");';
                                    $code[] = '//$tracking->setData("custom_data_2","value");';
                                    $code[] = '//$tracking->setData("custom_data_...","value");';
                                    $code[] = '';
                                    $code[] = '//place order in affiliate script';
                                    $code[] = '$tracking->placeOrder();';
                                    echo ___h($code,'php');
                                ?>

                                <br>
                                <p>
                                    <h6><?= __('admin.all_possible_tracking_para') ?></h6>
                                    <div class="well">
                                        <strong>WebsiteUrl</strong>    : <?= __('admin.website_root_url') ?> <br>
                                        <strong>OrderId</strong>       : <?= __('admin.unique_order_id') ?> <br>
                                        <strong>OrderCurrency</strong> : <?= __('admin.currency_symbol_of_order') ?> <br>
                                        <strong>OrderTotal</strong>    : <?= __('admin.total_amt_of_order_product_com_separated') ?> <br>
                                        <strong>ProductIDs</strong>    : <?= __('admin.pro_ids_of_order_com_separated') ?> <br>
                                    </div>

                                    <div class="alert alert-info">
                                        <strong><?= __('admin.script_tag') ?></strong><?= __('admin.script_tag') ?> <?= __('admin.is_optional_if_already_added_header_footer') ?>
                                    </div>
                                </p>
                            </div>

                            <br><hr>

                            <h2><?= __('admin.stop_recurring_payment_of_order') ?></h2>
                            <div>
                                <p><?= __('admin.to_stop_recurring_pay_orders') ?></p>

                                <?php
                                    $code = array();
                                    $code[] = '<script type="text/javascript" src="'. $base_url .'integration/general_integration"></script>';
                                    $code[] = '<script type="text/javascript">';
                                    $code[] = ' AffTracker.setWebsiteUrl( "WebsiteUrl" );';
                                    $code[] = ' AffTracker.stop_recurring("$variable_OrderId ")';
                                    $code[] = '</script>';
                                    
                                    echo ___h($code,'html');
                                ?>

                                <p>
                                    <h6><?= __('admin.all_possible_tracking_para') ?></h6>
                                    <div class="well">
                                        <strong>WebsiteUrl</strong> : <?= __('admin.website_root_url') ?> <br>
                                        <strong>variable_OrderId </strong>    : <?= __('admin.unique_order_id') ?> <br>
                                    </div>

                                    <div class="alert alert-info">
                                        <strong>OrderId</strong> <?= __('admin.variable_OrderId_info') ?> 
                                    </div>
                                </p>
                                </br>
                            </div>
                        <?php } ?>

                        <?php if(in_array($tool_type, ['program'])){ ?>
                            <h2><?= __('admin.pro_click_tracking') ?></h2>
                            <div>
                                <p><?= __('admin.pro_click_tracking_info') ?></p>
                                <?php
                                    $code = array();
                                    $code[] = '<script type="text/javascript" src="'. $base_url .'integration/general_integration"></script>';
                                    $code[] = '<script type="text/javascript">';
                                    $code[] = ' AffTracker.setWebsiteUrl( "WebsiteUrl" );';
                                    $code[] = ' AffTracker.productClick( "ProductID" );';
                                    $code[] = '</script>';
                                    
                                    echo ___h($code,'html');
                                ?>

                                <p>
                                    <h6><?= __('admin.all_possible_tracking_para') ?></h6>
                                    <div class="well">
                                        <strong>WebsiteUrl</strong>       : <?= __('admin.website_root_url') ?> <br>
                                        <strong>ProductID</strong> : <?= __('admin.unique_product_id') ?>
                                    </div>
                                </p>
                            </div>
                            </br>
                        <?php } ?>
                    </div>
                <?php } ?>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><?= __('admin.close') ?></button>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        setTimeout(function(){
            $(".token.string").each(function(){
                var c = $(this).text().replace(/[^a-z_0-9\s]/gi, '')
                $(this).addClass(c)
            });
            $(".auto-fill-filed input").trigger("keyup");

            const clipboard = new Clipboard('.copy', {
              target: (trigger) => {
                return trigger.nextElementSibling;
              }
            });

            clipboard.on('success', (event) => {
              event.trigger.textContent = '<?= __('admin.copied') ?>';
              setTimeout(() => {
                event.clearSelection();
                event.trigger.textContent = '<?= __('admin.copy') ?>';
              }, 2000);
            });

        }, 1000);
    })

    $(".auto-fill-filed input").keyup(function(){
        var val = $(this).val();
        val = val ? val : $(this).attr("data-default");

        $("." +$(this).attr("name")  ).text('"' + val +'"')
    });
</script>