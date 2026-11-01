<div class="modal-dialog modal-xl">
    <div class="modal-content">
        <div class="modal-header">
            <h6 class="modal-title m-0 text-center d-block w-100 ">
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
                    <h6><?= __('admin.product_name') ?> : <?= $product->product_name; ?></h6>
                    <h6><?= __('admin.product_price') ?> : <?= $product->product_price; ?></h6>                    
                    <h6><?= __('admin.product_purchase_url') ?> : <a target="_blank" href="<?= $product->product_url; ?>"><?= $product->product_url; ?></a></h6>
                </div>

                <div class="modal-ins-body">
                    <div>
                        <section>
                            <div>
                                <h5 class="mt-4"><?= __('admin.add_following_script_to_page_footer') ?></h5>
                                <?php
                                $code = array();
                                $code[] = '<script type="text/javascript" src="'. $base_url .'integration/general_integration"></script>';
                                echo ___h($code,'html');
                                ?>

                            </div>
                            <div>
                            <h4 class="notification_on_pages">
                                <div class="well">
                                <span class="bg-danger text-white">
                                    <?= __('admin.add_following_attribute_to_buy_button') ?>
                                </span>
                                    <ul>
                                        <li><?= __('admin.classified_integration_instruction_1') ?></li>
                                        <li><?= __('admin.classified_integration_instruction_2') ?></li>
                                    </ul>
                                </div>
                            </h4>
                                <?php
                                $productsCampaignCode = _encrypt_decrypt($product->product_id);
                                $code = array();
                                $code[] = 'AffTrackerProcutCampaign="'.$productsCampaignCode.'"';
                                echo ___h($code,'html');
                                ?>
                                <h5 class="mt-4"><?= __('admin.example') ?></h5>
                                <?php
                                $code = array();
                                $code[] = '<button AffTrackerProcutCampaign="'.$productsCampaignCode.'">Buy Now</button>';
                                echo ___h($code,'html');
                                ?>
                            </div>
                            <?php for ($i=1; $i <=7 ; $i++) { ?>
                                <div>
                                    <h5 class="mt-4">Button Template <?=$i ?></h5>
                                    <?php
                                    $code = array();
                                    $code[] = '<img AffTrackerProcutCampaign="'.$productsCampaignCode.'" src="'.base_url('store/show_classified_buy_button/'.$product->product_id.'/'.$i).'" width="500" >';
                                    echo ___h($code,'html');
                                    ?>
                                    <img src="<?= base_url('store/show_classified_buy_button/'.$product->product_id.'/'.$i); ?>" width="500"/>
                                </div>
                            <?php } ?>
                        </section>
                    </div>

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
</script>