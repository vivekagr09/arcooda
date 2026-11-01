<?php

ob_start();

function __diag_get_phpinfo() {
	ob_flush();
	ob_start();
	phpinfo(45);
	$phpinfo = ob_get_contents();
	ob_get_clean();

	// Remove sensitive information
	$phpinfo = preg_replace(
		'/<td class="e">((?:HTTP_COOKIE|Cookie|\$_COOKIE\[[^\]]+\]|\$_SERVER\[\'(?:HTTP_COOKIE|UNIQUE_ID)\'\])\s*)<\/td><td class="v">[^<]+<\/td>/',
		'<td class="e">$1</td><td class="v">[redacted]</td>',
		$phpinfo
	);

	// Mask any remaining session IDs
	$phpinfo = str_replace(session_id(), '[redacted]', $phpinfo);

	// Remove the user's IP address
	$phpinfo = str_replace($_SERVER['REMOTE_ADDR'], '[redacted]', $phpinfo);

	// var_dump(INFO_GENERAL | INFO_CONFIGURATION | INFO_MODULES | INFO_VARIABLES);
	return $phpinfo;
}

require "includes/init.php";
$page->setPath("../")->requirePermission('admin-access');

if (isset($_GET['preview']) && !isset($_POST['confirm'])) {
	if (DEMO) {
		echo "Disabled on demo.";
		die;
	}

	echo __diag_get_phpinfo();
	die;
}

$page->setPage(16)->setTitle("phpinfo")->header();

if (isset($_POST['confirm']) && !DEMO) {
	$report = base64_encode(__diag_get_phpinfo());
	$data = array(
		'report' => $report,
		'studio_version' => Studio\Base\Studio::VERSION_STR,
		'php_version' => phpversion(),
		'mysql_version' => $studio->sql->server_info
	);

	try {
		$response = $api->sendServerInformation($data);

		if (!$response->success) {
			throw new Exception($response->msg);
		}

		header("Location: diagnostics.php?success=1");
		die;
	}
	catch (Exception $ex) {
?>
<div class="error">
	Error sending report: <?php echo $ex->getMessage(); ?>
</div>
<?php
	}
}
?>

<div class="heading">
    <h1>Diagnostics</h1>
    <h2>Find and fix problems</h2>
</div>

<div class="panel v2 back">
    <a href="diagnostics.php">
        <i class="material-icons">&#xE5C4;</i> Back
    </a>
</div>

<div class="row">
    <div class="col-md-9">
        <div class="panel">
            <h3>Are you sure?</h3>
            <p style="margin: 0 0 15px; max-width: 1000px;">
				This will send your website's full PHP configuration to customer support. Generally, this will not contain any sensitive information. The report will be sent using SSL encryption and stored securely. It will automatically be deleted from our side after 30 days.
			</p>

			<form action="" method="post">
				<input type="hidden" name="confirm" value="1">
            	<button class="btn blue" type="submit">Confirm and send</button>
				<a class="btn gray" href="diagnostics.php">Cancel</a>
			</form>
		</div>

        <div class="panel">
            <h3>Preview</h3>
            <p style="margin: 0 0 15px;" class="to-hide">
				Below is a preview of the report. The developer will receive all information shown.
			</p>

			<iframe class="preview" src="?preview=1" style="width: 100%; max-width: 1000px; height: 600px; border: 2px solid #eee;"></iframe>
		</div>
    </div>
</div>

<?php
$page->footer();
?>
