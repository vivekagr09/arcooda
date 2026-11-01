<div class="modal-header text-center">
    <h6 class="modal-title">
        <?= __('user.terms') ?>
    </h6>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

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
                    echo "There is not terms available";
                }
            ?>

        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= __('user.close') ?></button>
</div>