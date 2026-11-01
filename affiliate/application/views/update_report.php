<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Upgrade Details</title>

	<!-- Bootstrap 5 Css -->
    <link rel="stylesheet" href="<?= base_url('assets/template/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/template/css/bootstrap-icons.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/template/css/bootstrap-toggle.min.css') ?>">
    <!-- Bootstrap 5 Css -->

	<link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@300;400;500&display=swap" rel="stylesheet">

<style type="text/css">
	body {
		background-color: #f8f9fa;
		font-family: 'Roboto Mono', monospace;
	}

	.terminal-container {
		width: 80%;
		margin: auto;
		margin-bottom: 14px;
		box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2);
	}

	.terminal-home {
		padding: 1.5em 1em 1em 2em;
		background-color: #000;
		color: #fff;
		max-height: 70vh;
		overflow-y: scroll;
	}

	.console {
		margin: 0;
		padding: 0;
		font-size: 16px;
		line-height: 1.5;
	}

	.console.success {
		color: #4CAF50;
	}

	.console.error {
		color: #F44336;
	}

	.console.warning {
		color: #FFC107;
	}

	.alert {
		border-radius: 8px;
		padding: 1em;
		margin-bottom: 20px;
		box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2);
	}

	.alert-success {
		background-color: #E9F5E9;
		color: #4CAF50;
	}

	.alert-info {
		background-color: #EAF3FA;
		color: #2196F3;
	}

	.alert-danger {
		background-color: #FCE8E8;
		color: #F44336;
	}

	.btn {
		border-radius: 4px;
		padding: 8px 16px;
		background-color: #2196F3;
		color: #FFFFFF;
		text-decoration: none;
		border: none;
		box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2);
		transition: background-color 0.3s ease;
	}

	.btn:hover {
		background-color: #1976D2;
	}

	footer {
		background: #E0E8F0;
		padding: 1em;
		border-radius: 8px;
		box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2);
	}
</style>

<script src="<?php echo base_url(); ?>assets/js/jquery.min.js"></script>
</head>


<body class="p-3">
	<section class="terminal-container terminal-fixed-top">
	  <header class="terminal">
	    <span class="button red"></span>
	    <span class="button yellow"></span>
	    <span class="button green"></span>
	  </header>

<div class="terminal-home">
    <?php 
    $update_attempted = is_array($result) && !empty($result);
    if ($update_attempted) {
        $is_successfully_updated = true;
        for ($i = 0; $i < sizeof($result); $i++) { 
            foreach ($result[$i] as $key => $value) {
                if ($key == 'error') { 
                    if ($is_successfully_updated == true && str_contains($value, 'already a latest version')) {
                        $already_latest_version = true;
                    }
                    $is_successfully_updated = false;
                }
                echo '<p class="console '.$key.'">'.$value.'</p>';
            }
        }
    } else {
        echo '<p class="console error">No results found</p>';
    }
    ?>
</div>

<!-- Container -->
<div class="container mt-4">
  
  <!-- Update Status Row -->
  <div class="row justify-content-center mb-3">
    <div class="col-12 text-center">
      <?php if ($update_attempted): ?>
          <div class="p-3 rounded <?php echo ($is_successfully_updated) ? 'bg-success' : (isset($already_latest_version) ? 'bg-info' : 'bg-danger'); ?>">
            <span class="badge fs-4 text-white">
              <?php
              if ($is_successfully_updated) {
                  echo 'The system was updated successfully!';
              } elseif (isset($already_latest_version)) {
                  echo 'The system is already updated to the latest version!';
              } else {
                  echo 'Something went wrong while upgrading the system!';
              }
              ?>
            </span>
          </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Back to Admin Button Row -->
  <div class="row justify-content-center mt-3">
    <div class="col-12 text-center">
      <button class="btn btn-primary btn-lg rounded-pill" onclick="location.replace('dashboard');">
        <?= __('user.back_to_admin') ?>
      </button>
    </div>
  </div>
  
</div>

	  <footer class="col-12 text-center fw-bold mt-3">
		Current Version: <?php echo SCRIPT_VERSION;?>	  	
	  </footer>
	</section>
</body>

<script type="text/javascript">
	var $container = $('.terminal-home'),
	$scrollTo = $('.console:last-child');

	$container.animate({
	    scrollTop: $scrollTo.offset().top - $container.offset().top + $container.scrollTop()
	});
</script>
</html>