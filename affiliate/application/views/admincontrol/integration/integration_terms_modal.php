<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header text-center">
            <h6 class="modal-title">
                <?= __('admin.terms') ?>
            </h6>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>


        <link rel="stylesheet" type="text/css" href="<?= base_url('assets/integration/prism/css.css') ?>?v=<?= av() ?>">
        <script type="text/javascript" src="<?= base_url('assets/integration/prism/js.js') ?>"></script>
        <script type="text/javascript" src="<?= base_url('assets/integration/prism/clipboard.min.js') ?>"></script>
        
        <div class="modal-body m-0 p-0">
            <div class="modal-ins">
                
                <div class="modal-ins-body">
                    <?php
                        if(!empty($terms_data['terms']))
                        {
                            echo $terms_data['terms'];
                        }
                        else
                        {
                            echo __('admin.there_is_not_terms_available');
                        }
                    ?>

                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= __('user.close') ?></button>
        </div>
    </div>
</div>
