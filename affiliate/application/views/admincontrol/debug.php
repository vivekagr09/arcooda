<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="<?= base_url('assets/template/css/system_update.css') ?>?v=<?= av() ?>">

    <!-- Bootstrap 5 Css -->
    <link rel="stylesheet" href="<?= base_url('assets/template/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/template/css/bootstrap-icons.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/template/css/bootstrap-toggle.min.css') ?>">
    <!-- Bootstrap 5 Css -->

    <?php if($dtype=="sysupdatereport") { ?>

    <?php } ?>
    <title>Developers Tools</title>
  </head>
<body class="p-3">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Developers Tools</a>
        <button 
            class="navbar-toggler" 
            type="button" 
            data-bs-toggle="collapse" 
            data-bs-target="#navbarNav" 
            aria-controls="navbarNav" 
            aria-expanded="false" 
            aria-label="Toggle navigation"
        >
            <span class="bi bi-list"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a 
                        class="nav-link mx-2 rounded <?= $dtype=="logs" ? "active bg-white text-dark" : ""; ?>" 
                        aria-current="page" 
                        href="<?= base_url('debug/logs');?>"
                    >
                        Logs
                    </a>
                </li>
                <li class="nav-item">
                    <a 
                        class="nav-link mx-2 rounded <?= $dtype=="dbstructure" ? "active bg-white text-dark" : ""; ?>" 
                        href="<?= base_url('debug/dbstructure');?>"
                    >
                        Database Structure
                    </a>
                </li>
                <li class="nav-item">
                    <a 
                        class="nav-link mx-2 rounded <?= $dtype=="sysupdatereport" ? "active bg-white text-dark" : ""; ?>" 
                        href="<?= base_url('debug/sysupdatereport');?>"
                    >
                        System Update Report
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<section class="container-fluid">
        <?php if(isset($error)) { ?>
            <span class="badge bg-danger w-100 fs-4 d-block"><?= $error ?></span>
        <?php } else { ?>
            <?php
                switch ($dtype) {
                    case 'logs':
                        ?>
                        <div id="content">
                            <div class="container-fluid">
                                <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title"><i class="fa fa-exclamation-triangle"></i></h3>
                                </div>
                                <div class="panel-body">
                                    <textarea wrap="off" rows="15" readonly class="form-control"><?php echo $log; ?></textarea>
                                </div>
                                </div>
                            </div>
                        </div>
                        <?php
                        break;
                    case 'dbstructure':
                        ?>
<div id="content">
    <div class="container-fluid py-2">
        <!-- Title showing number of tables -->
        <h1 class="text-center mb-4">Database Structure (<?= count($dbstructure); ?> Tables)</h1>
        
        <?php foreach ($dbstructure as $table => $structure) { ?>
            <div class="accordion" id="accordion-dbstructure">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading-<?= $table; ?>">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?= $table; ?>" aria-expanded="false" aria-controls="collapse-<?= $table; ?>">
                            <strong><?= $table; ?></strong>
                            <!-- Add space between table name and badge -->
                            <span class="ms-2 badge <?= $structure[0]->primary_key ? 'bg-success' : 'bg-danger' ?>">Primary Key</span>
                        </button>
                    </h2>
                    <div id="collapse-<?= $table; ?>" class="accordion-collapse collapse" aria-labelledby="heading-<?= $table; ?>" data-bs-parent="#accordion-dbstructure">
                        <div class="accordion-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Max Length</th>
                                        <th>Default</th>
                                        <th>Primary Key</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    foreach ($structure as $str) {
                                        ?>
                                        <tr>
                                            <td><?= $str->name; ?></td>
                                            <td><?= $str->type; ?></td>
                                            <td><?= $str->max_length; ?></td>
                                            <td><?= $str->default; ?></td>
                                            <td><?= $str->primary_key; ?></td>
                                        </tr>
                                        <?php
                                    }
                                ?>
                                </tbody>
                            </table>                                                
                        </div>
                    </div>
                </div>
            </div>                                    
        <?php } ?>
    </div>
</div>





                        <?php
                        break;
                    case 'sysupdatereport':
                        ?>

    <div id="content">
        <section class="terminal-container terminal-fixed-top">
    <header class="terminal d-flex justify-content-between align-items-center bg-dark text-light p-3 shadow-lg">
        <div class="d-flex align-items-center">
            <button type="button" class="btn btn-danger rounded-circle me-2" style="width: 24px; height: 24px;"></button>
            <button type="button" class="btn btn-warning rounded-circle me-2" style="width: 24px; height: 24px;"></button>
            <button type="button" class="btn btn-success rounded-circle" style="width: 24px; height: 24px;"></button>
        </div>
        <div class="dropdown">
            <button class="btn btn-outline-light dropdown-toggle" type="button" id="log-dropdown-btn" data-bs-toggle="dropdown" aria-expanded="false">
                Log Files
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="log-dropdown">
                <?php 
                    if (empty($log_files)) {
                        echo '<li><span class="dropdown-item text-muted">No log files available</span></li>';
                    } else {
                        foreach($log_files as $file) { 
                            $nameExplode = explode('-', str_replace('.json', '', $file));
                            $selected = (isset($selected_file) && $selected_file == $file) ? "selected" : "";
                ?>
                    <li>
                        <a class="dropdown-item <?= $selected ?>" href="<?= base_url('/debug/sysupdatereport/').str_replace('.json', '', $file) ?>">
                            <?= date('d-m-Y H:i:s', (int) $nameExplode[0]) ?> | <?= str_replace('_', '.', $nameExplode[1]) ?>
                        </a>
                    </li>
                <?php 
                        }
                    }
                ?>
            </ul>
        </div>
    </header>

<div class="terminal-home bg-dark text-light p-3 rounded">
    <?php 
    $update_attempted = false; // Flag to determine whether an update was attempted
    
    if (isset($result) && is_array($result) && !empty($result)) {
        $update_attempted = true; // An update was attempted if $result is set
        $is_successfully_updated = true;
        for ($i=0; $i < sizeof($result); $i++) { 
            foreach ($result[$i] as $key => $value) {
                if($key == 'error') { 
                    if($is_successfully_updated == true && str_contains($value, 'already a latest version')) {
                        $already_latest_version = true;
                    }
                    $is_successfully_updated = false;
                }
                echo '<p class="console mb-0 '.$key.'">'.$value.'</p>';
            }
        }
    }
    ?>
</div>

<div class="row mt-2">
    <div class="col-12 d-flex justify-content-center">
        <?php if ($update_attempted): ?>
            <div class="badge-container w-100 text-center">
                <?php
                if ($is_successfully_updated) {
                    echo '<span class="badge bg-success fs-4 d-block w-100"><i class="bi bi-check-circle-fill me-2"></i> The system was updated successfully!</span>';
                } elseif (isset($already_latest_version)) {
                    echo '<span class="badge bg-info fs-4 d-block w-100"><i class="bi bi-info-circle-fill me-2"></i> The system is already updated to the latest version!</span>';
                } else {
                    echo '<span class="badge bg-danger fs-4 d-block w-100"><i class="bi bi-exclamation-triangle-fill me-2"></i> Something went wrong while upgrading the system!</span>';
                }
                ?>
            </div>
        <?php endif; ?>
    </div>
</div>




</section>

<footer class="fixed-bottom bg-dark text-light">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 d-flex justify-content-center align-items-center py-2">
                <span class="me-2 fw-bold">Current Version:</span>
                <span class="badge bg-secondary"><?php echo SCRIPT_VERSION; ?></span>
            </div>
        </div>
    </div>
</footer>

</div>

                        <?php
                        break;
                    default:
                        ?>
<span class="badge bg-warning w-100 fs-4 d-block mt-3">
Something went wrong, please try again!
</span>
                        <?php
                        break;
                }
            ?>
        <?php } ?>
    </section>
    <!--bootstrap 5 js files-->
     <script src="<?= base_url('assets/template/js/jquery-3.6.0.min.js'); ?>"></script>
     <script src="<?= base_url('assets/template/js/popper.min.js'); ?>"></script>
     <script src="<?= base_url('assets/template/js/bootstrap.min.js'); ?>"></script>
    <!--bootstrap 5 js files-->

    <?php if($dtype=="sysupdatereport") { ?>

<script type="text/javascript"> 
    var $container = $('.terminal-home'),
    $scrollTo = $('.console:last-child');
    $container.animate({
        scrollTop: $scrollTo.offset().top - $container.offset().top + $container.scrollTop()
    });

    function redirectTOLog(that) {
        window.location.href = "<?= base_url('/debug/sysupdatereport/') ?>"+that.value;
    }
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
    // Check if the page has been automatically reloaded
    if (sessionStorage.getItem('autoSelectedLastItem') !== 'true') {
        const dropdownMenu = document.querySelector('.dropdown-menu');
        const lastItem = dropdownMenu.lastElementChild;
        
        if (lastItem) {
            const lastItemLink = lastItem.querySelector('a');
            const lastItemURL = lastItemLink.getAttribute('href');
            const dropdownButton = document.getElementById('log-dropdown-btn');

            // Update button label
            if (dropdownButton) {
                dropdownButton.textContent = lastItemLink.textContent;
            }

            // Set a flag in sessionStorage
            sessionStorage.setItem('autoSelectedLastItem', 'true');

            // Redirect to the URL of the last item
            if (lastItemURL) {
                window.location.href = lastItemURL;
            }
        }
    } else {
        // Reset the flag so manual selection still works
        sessionStorage.setItem('autoSelectedLastItem', 'false');
    }
    });
</script>
    <?php } ?>
  </body>
</html>