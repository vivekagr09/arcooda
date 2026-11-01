<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><?= __('admin.integration_modules') ?></h5>
            </div>

            <div class="card-body">
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-6 g-4">
                    <?php foreach ($integration_modules as $key => $module): ?>
                        <div class="col">
                            <div class="card h-100">
                                <div class="card-body d-flex flex-column justify-content-center text-center flex-grow-1">
                                    <?php if ($key === 'postback'): ?>
                                        <a href="<?= base_url('admincontrol/market_tools_setting') ?>" class="btn btn-primary d-inline-flex flex-column align-items-center text-nowrap">
                                    <?php else: ?>
                                        <a href="<?= base_url("integration/instructions/{$key}") ?>" class="btn btn-primary d-inline-flex flex-column align-items-center text-nowrap">
                                    <?php endif; ?>
                                            <img src="<?= $module['image'] ?>">
                                            <div class="modules-title text-truncate w-100 px-1"><?= $module['name'] ?></div>
                                        </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>