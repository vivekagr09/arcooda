<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __('front.site_maintenance') ?></title>
    <!-- Include layout.php -->
    <?php include(APPPATH.'views/includes/layout.php'); ?>
    <!-- Include layout.php -->
</head>

<body class="bg-light d-flex justify-content-center align-items-center vh-100">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="card text-center">
            <div class="card-body">
                <span class="display-1 text-warning">&#9888;</span>
                <h1 class="card-title"><?= __('front.we_will_be_back_soon') ?></h1>
                <p class="card-text lead"><?= __('front.sorry_for_offline') ?></p>
                <p class="card-text text-muted"><?= __('front.support_team') ?></p>
            </div>
        </div>
    </div>
</body>
</html>