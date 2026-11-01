<?php

use Studio\Util\Ratelimit;

require "../includes/init.php";
$page->setPath("../../")->requirePermission('admin-access')->setPage(3)->setTitle("Rate limiting")->header();


if (isset($_POST['ratelimit-enabled']) && !DEMO) {
    $ierror = "";

    $bools = array("ratelimit-enabled");
	$strings = array("ratelimit-action", "ratelimit-sitekey", "ratelimit-secretkey");

    foreach ($bools as $bool) {
        if (!isset($_POST[$bool])) $studio->showFatalError("Missing POST parameter $bool");

        $val = $_POST[$bool];
        if ($val != "On" && $val != "Off") $val = "Off";

        $studio->setopt($bool, $val);
    }

	foreach ($strings as $str) {
        if (!isset($_POST[$str])) $studio->showFatalError("Missing POST parameter $str");

        $val = $_POST[$str];
        $studio->setopt($str, $val);
    }

	foreach (Ratelimit::keys() as $i => $row) {
		foreach (Ratelimit::intervals() as $interval) {
			$name = sprintf('ratelimit-%s-%s', $row['key'], $interval);
			if (!isset($_POST[$name])) $studio->showFatalError("Missing POST parameter $name");
			if (!is_numeric($_POST[$name])) $studio->showFatalError("Invalid POST parameter $name");

			$value = min(max((int) $_POST[$name], 0), 999999);
			Ratelimit::setRateLimit($row['key'], $interval, $value);
		}

		$name = sprintf('ratelimit-%s-duration', $row['key']);
		if (!isset($_POST[$name])) $studio->showFatalError("Missing POST parameter $name");
		if (!is_numeric($_POST[$name])) $studio->showFatalError("Invalid POST parameter $name");

		$value = min(max((int) $_POST[$name], 1), 10080);
		Ratelimit::setDuration($row['key'], $value);
	}

    header("Location: ratelimit.php?success=1&{$ierror}");
    die;
}

?>

<div class="heading">
    <h1>Rate limiting</h1>
    <h2>Manage captchas and rate limiting</h2>
</div>

<form action="" method="post">
    <div class="panel v2 back">
        <a href="../settings.php">
            <i class="material-icons">&#xE5C4;</i> Back
        </a>
    </div>
    <div class="save-container">
        <div class="saveable">
            <div class="panel v2">
                <div class="setting-group">
                    <h3>Settings</h3>

                    <div class="setting toggle">
                        <label data-switch="Ctl_Switch_01">Enable rate limiting <span class="help tooltip" title="If enabled, we'll record certain requests in the database to enforce rate limits."><i class="material-icons">&#xE8FD;</i></span></label>

                        <div class="switch" id="Ctl_Switch_01">
                            <input type="hidden" name="ratelimit-enabled" value="<?php echo $studio->getopt("ratelimit-enabled"); ?>">
                            <div class="handle"></div>
                        </div>
                    </div>
                </div>
                <div class="setting-group">
                    <h3>Action</h3>

                    <div class="setting select">
                        <label for="$ctlInput1">
                            Rate limit action
                            <span class="help tooltip" title="Choose what to do when users are rate limited."><i class="material-icons">&#xE8FD;</i>
                        </label>
                        <div class="dropdown">
                            <select id="$ctlInput1" name="ratelimit-action">
                                <option value="" <?php if ($studio->getopt("ratelimit-action", '') == "") echo "selected"; ?>>Block for duration</option>
                                <option value="recaptcha" <?php if ($studio->getopt("ratelimit-action") == "recaptcha") echo "selected"; ?>>Use ReCAPTCHA (v2 Checkbox)</option>
                                <option value="hcaptcha" <?php if ($studio->getopt("ratelimit-action") == "hcaptcha") echo "selected"; ?>>Use hCaptcha</option>
                                <option value="turnstile" <?php if ($studio->getopt("ratelimit-action") == "turnstile") echo "selected"; ?>>Use Cloudflare Turnstile</option>
                            </select>
                        </div>
                    </div>

					<div class="collapsed-action action-recaptcha action-hcaptcha action-turnstile" style="display: none">
						<div class="setting text">
							<label for="$ctlInput3">
								Site key
							</label>
							<div class="text">
								<input id="$ctlInput3" type="text" name="ratelimit-sitekey" value="<?php echo $studio->getopt('ratelimit-sitekey'); ?>">
							</div>
						</div>

						<div class="setting text">
							<label for="$ctlInput4">
								Secret key
								<span class="help tooltip" title="You'll likely find this in your account settings."><i class="material-icons">&#xE8FD;</i>
							</label>
							<div class="text">
								<input id="$ctlInput4" type="password" name="ratelimit-secretkey" value="<?php echo DEMO ? '' : $studio->getopt('ratelimit-secretkey'); ?>">
							</div>
						</div>
					</div>
                </div>
                <div class="setting-group">
                    <h3>Rate limits</h3>

					<div class="settings-table">
						<table>
							<thead>
								<th></th>
								<th>
									1 min
									<span class="help tooltip" title="The number of requests allowed in a 1-minute interval before rate limiting. Set 0 to disable this interval."><i class="material-icons">&#xE8FD;</i>
								</th>
								<th>
									5 min
									<span class="help tooltip" title="The number of requests allowed in a 5-minute interval before rate limiting. Set 0 to disable this interval."><i class="material-icons">&#xE8FD;</i>
								</th>
								<th>
									15 min
									<span class="help tooltip" title="The number of requests allowed in a 15-minute interval before rate limiting. Set 0 to disable this interval."><i class="material-icons">&#xE8FD;</i>
								</th>
								<th>
									30 min
									<span class="help tooltip" title="The number of requests allowed in a 30-minute interval before rate limiting. Set 0 to disable this interval."><i class="material-icons">&#xE8FD;</i>
								</th>
								<th>
									1 hr
									<span class="help tooltip" title="The number of requests allowed in a 1-hour interval before rate limiting. Set 0 to disable this interval."><i class="material-icons">&#xE8FD;</i>
								</th>
								<th>
									Duration
									<span class="help tooltip" title="The number of minutes to block or show a captcha for when a rate limit in this row is hit. The user can complete a captcha to bypass this."><i class="material-icons">&#xE8FD;</i>
								</th>
							</thead>
							<tbody>
								<?php
								foreach (Ratelimit::keys() as $i => $row) {
								?>

								<tr>
									<th>
										<?php echo ucfirst($row['key']); ?>
										<span class="help tooltip" title="<?php echo sanitize_attribute($row['description']); ?>"><i class="material-icons">&#xE8FD;</i>
									</th>
									<?php
									foreach (Ratelimit::intervals() as $interval) {
									?>
									<td>
										<input type="number" id="$ctlRateLimit<?php echo $i; ?>_<?php echo $interval; ?>" name="ratelimit-<?php echo $row['key']; ?>-<?php echo $interval; ?>" value="<?php echo Ratelimit::getRateLimit($row['key'], $interval); ?>" min="0" max="999999" required>
									</td>
									<?php } ?>
									<td>
										<input type="number" id="$ctlDuration<?php echo $i; ?>" name="ratelimit-<?php echo $row['key']; ?>-duration" value="<?php echo Ratelimit::getDuration($row['key']); ?>" min="1" max="10080" required>
									</td>
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
        <div class="save">
            <div class="save-box">
                <button type="submit">
                    <span>Save changes</span>
                    <span>Saved</span>
                    <img src="../../resources/images/load32.gif" width="16px" height="16px">
                </button>
            </div>
        </div>
    </div>
</form>

<script type="text/javascript">
    $(".collapsed-action.action-" + $("select[name=ratelimit-action]").val()).show();
    $("select[name=ratelimit-action]").on('change', function() {
        $(".collapsed-action").hide();
        $(".collapsed-action.action-" + $("select[name=ratelimit-action]").val()).show();
    });

    var auth = $("input[name=smtp-auth]").val() === 'On';
    var authInputs = $('.requires-authentication');
    if (auth) authInputs.show();

    $("input[name=smtp-auth]").on('change', function() {
        auth = $("input[name=smtp-auth]").val() === 'On';
        if (auth) authInputs.show();
        else authInputs.hide();
    });
</script>

<?php
$page->footer();
?>
