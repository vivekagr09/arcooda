<div class="row">
    <div class="col-sm-8">
        <div class="card mb-3">
            <div class="card-header bg-secondary text-white">
                <h5><?= __('admin.system_status_help_line') ?></h5>
            </div>
            <div class="card-body p-0 overflow-auto">
                <div class="system-status">
                    <?php
                    $serverReqs = ['php', 'curl', 'openssl_encrypt', 'mysqli', 'ipapi', 'zip', 'allow_url_fopen', 'max_input_vars', 'upload_max_filesize', 'post_max_size', 'gd'];
                    $serverReq = [
                        'php' => !version_compare(PHP_VERSION, '7.4.0', '>='),
                        'curl' => !extension_loaded('curl'),
                        'openssl_encrypt' => !function_exists('openssl_encrypt'),
                        'mysqli' => !extension_loaded('mysqli'),
                        'ipapi' => !function_exists('file_get_contents'),
                        'zip' => !extension_loaded('zip'),
                        'allow_url_fopen' => !ini_get('allow_url_fopen'),
                        'max_input_vars' => ini_get('max_input_vars') < 1000,
                        'upload_max_filesize' => intval(str_replace('M', '', ini_get('upload_max_filesize'))) < 128,
                        'post_max_size' => intval(str_replace('M', '', ini_get('post_max_size'))) < 128,
                        'gd' => !extension_loaded('gd'),
                    ];

                    foreach ($serverReqs as $req) {
                        ?>
                        <div class="card mb-2">
                            <div class="card-body py-2">
                                <h5 class="card-title"><?= $req ?></h5>
                                <p class="card-text"><?= __('admin.extension') ?> <i><?= $req ?></i></p>
                            </div>
                            <div class="card-footer bg-transparent border-0 d-flex justify-content-end align-items-center">
                                <?php if (isset($serverReq[$req]) && $serverReq[$req]) { ?>
                                    <span class="badge rounded-pill bg-danger"><?= __('admin.not_installed') ?></span>
                                <?php } else { ?>
                                    <span class="badge rounded-pill bg-success"><?= __('admin.installed') ?></span>
                                <?php } ?>
                            </div>
                        </div>
                        <?php
                    }
                    
                    // SSL Check
                    ?>
                    <div class="card mb-2">
                        <div class="card-body py-2">
                            <h5 class="card-title"><?= is_ssl() ? __('admin.ssl') : __('admin.non_ssl') ?></h5>
                            <p class="card-text"><?= __('admin.install') ?> <i><?= __('admin.ssl') ?></i> <?= __('admin.certificate') ?></p>
                        </div>
                        <div class="card-footer bg-transparent border-0 d-flex justify-content-end align-items-center">
                            <?php if (is_ssl()) { ?>
                                <span class="badge rounded-pill bg-success"><?= __('admin.installed') ?></span>
                            <?php } else { ?>
                                <span class="badge rounded-pill bg-danger"><?= __('admin.not_installed') ?></span>
                            <?php } ?>
                        </div>
                    </div>
                    <?php
                    // GD Library Check
                    ?>
                    <div class="card mb-2">
                        <div class="card-body py-2">
                            <h5 class="card-title"><?= extension_loaded('gd') ? __('admin.gd_library_installed') : __('admin.no_gd_library_installed') ?></h5>
                            <p class="card-text"><?= __('admin.install') ?> <i><?= __('admin.gd') ?></i> <?= __('admin.library') ?></p>
                        </div>
                        <div class="card-footer bg-transparent border-0 d-flex justify-content-end align-items-center">
                            <?php if (extension_loaded('gd')) { ?>
                                <span class="badge rounded-pill bg-success"><?= __('admin.installed') ?></span>
                            <?php } else { ?>
                                <span class="badge rounded-pill bg-danger"><?= __('admin.not_installed') ?></span>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <!-- System Information Help Line -->
        <div class="card mb-3">
            <div class="card-header bg-secondary text-white">
                <h5><?= __('admin.system_information_help_line') ?></h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <?php
                    $serverInfo = [
                        'server_php_version' => phpversion(),
                        'server_database_version' => database_version(),
                        'server_database_software' => database_software(),
                        'server_system_os' => server_os(),
                        'server_memory_limit' => check_limit(),
                        'server_ip' => check_server_ip(),
                        'server_max_file_upload_size' => php_max_upload_size(),
                        'server_post_variable_size' => php_max_post_size(),
                        'server_max_execution_time' => php_max_execution_time()
                    ];
                    foreach ($serverInfo as $key => $value) {
                        ?>
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            <span class="fw-bold"><?= __('admin.'.$key) ?></span>
                            <span><?= $value ?></span>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
</div>