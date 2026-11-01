<div class="row">
    <div class="col-12">
        <div class="card mb-4 shadow">
            <div class="card-header">
                <h5 class="mb-0"><?= __('admin.integration_modules') ?></h5>
            </div>

            <div class="card-body">
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-6 g-3">
                    <?php foreach ($integration_modules as $key => $module) { ?>
                        <div class="col">
                            <div class="card h-100 shadow-sm">
                                <a href="<?= base_url('usercontrol/instructions/'. $key) ?>" class="text-decoration-none">
                                    <img src="<?= $module['image'] ?>" class="card-img-top">
                                    <div class="card-body">
                                        <h5 class="card-title text-center text-primary"><?= $module['name'] ?></h5>
                                    </div>
                                </a>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
