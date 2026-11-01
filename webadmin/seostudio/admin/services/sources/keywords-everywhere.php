<?php

use SEO\Services\KeywordsEverywhere;

require "../../includes/init.php";
$page->setPath("../../../")->requirePermission('admin-access')->setPage(22)->header('services');

if (isset($_POST['kwe-key']) && !DEMO) {
    $ierror = "";

    $bools = array("kwe-enabled", "kwe-exclusive", "kwe-notify-enabled");
	$strings = array("kwe-notify-email");
	$ints = array("kwe-notify-threshold", "kwe-max-keywords");

    foreach ($bools as $bool) {
        if (!isset($_POST[$bool])) $_POST[$bool] = "Off";

        $val = $_POST[$bool];
        if ($val != "On" && $val != "Off") $val = "Off";

        $studio->setopt($bool, $val);
    }

	foreach ($strings as $str) {
        if (!isset($_POST[$str])) $studio->showFatalError("Missing POST parameter $str");

        $val = $_POST[$str];
        $studio->setopt($str, $val);
    }

    foreach ($ints as $int) {
        if (!isset($_POST[$int])) $studio->showFatalError("Missing POST parameter $int");

        $val = $_POST[$int];
        if (!is_numeric($val)) $val = 0;

        $studio->setopt($int, $val);
    }

	if (!isset($_POST["kwe-key"])) die("Missing POST parameter kwe-key");
	$key = trim($_POST["kwe-key"]);
	$redirect = true;

	if ($key !== $studio->getopt('kwe-key', '')) {
		if (empty($key)) {
			$studio->setopt('kwe-key', '');
		}
		else {
			try {
				$id = KeywordsEverywhere::getAccountId($key);
				$studio->setopt('kwe-key', $key);
				$studio->setopt('kwe-key-id', $id);
			}
			catch (Exception $e) {
				echo "<div class='error'>{$e->getMessage()}</div>";
				$redirect = false;
			}
		}
	}

	if ($redirect) {
		header("Location: keywords-everywhere.php?success=1");
		die;
	}
}

$balance = DEMO ? null : $studio->getopt('kwe-balance');
$balanceTimestamp = $studio->getopt('kwe-balance-timestamp', 0);

if (!DEMO && $key = $studio->getopt('kwe-key')) {
	try {
		if ($balance === null || isset($_GET['refresh'])) {
			$balance = KeywordsEverywhere::getBalance();
			$studio->setopt('kwe-balance', $balance);
			$studio->setopt('kwe-balance-timestamp', time());

			if (isset($_GET['refresh'])) {
				header("Location: keywords-everywhere.php?success=1");
				die;
			}
		}
		else {
			$balance = (int) $balance;
		}

	}
	catch (Exception $ex) {
		$balance = $ex->getMessage();
	}
}

?>

<section>
	<div class="header with-border">
		<h1>
			<a class="back" href="index.php">
				<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-circle" viewBox="0 0 16 16">
					<path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8zm15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-4.5-.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5z"/>
				</svg>
			</a>
			Keywords Everywhere
		</h1>
	</div>

	<form action="" method="post">
		<div class="save-container">
			<div class="saveable">
				<div class="intro-text mb-4">
					<p>
						<a href="https://keywordseverywhere.com/" rel="nofollow noopener">Keywords Everywhere</a>
						is a premium browser extension that offers access to accurate Google Keyword Planner data.
						When enabled, the keyword research tool will use their API to acquire its data for supported
						countries, which will add competition and trend data.
					</p>
					<p>
						Supported countries are Australia, Canada, India, New Zealand, South Africa, United Kingdom,
						and United States. If you keep the free built-in data source enabled, other countries will still
						be displayed and will use the free data source instead. In addition, if you ever run out of
						credits, we'll automatically fall back to the free data source.
					</p>
					<p>
						It is strongly recommended that you enable <a href="../../settings/ratelimit.php">rate limiting</a>
						on the "keywords" key with the captcha provider of your choice in order to prevent
						scrapers and bad actors from draining your credits, even if you lock the tool behind login
						or a paywall.
					</p>
				</div>

				<div class="replaceable">
					<?php if (!DEMO && $studio->getopt('kwe-key') && $studio->getopt('kwe-enabled') === 'On') { ?>
					<div class="card mb-4">
						<div class="card-header with-actions">
							<div class="title flex-grow-1">
								Credit balance
								<?php if (KeywordsEverywhere::isLowBalance()) { ?>
								<span class="badge bg-warning ms-2">Low balance!</span>
								<?php } ?>
							</div>
							<div class="actions">
								<span class="timestamp time right">
									Updated <?php echo (new \Studio\Display\TimeAgo($balanceTimestamp))->get(); ?>
									<span data-time="<?php echo $balanceTimestamp; ?>"><i class="material-icons">access_time</i></span>
								</span>
								<a href="?refresh" class="btn btn-xs">Refresh</a>
								<a href="https://keywordseverywhere.com/credits.html?apiKey=<?php echo sanitize_attribute($studio->getopt('kwe-key')); ?>&id=<?php echo sanitize_attribute($studio->getopt('kwe-key-id')); ?>" class="btn btn-xs" target="_blank" rel="nofollow noopener">Purchase credits</a>
							</div>
						</div>
						<div class="card-body">
							<?php if (is_int($balance)) { ?>
								<div class="d-flex">
									<div class="progress-number me-3">
										<div class="number">
											<?php echo number_format($balance); ?>
										</div>
										<span>keywords remaining</span>
									</div>
								</div>
							<?php } ?>
						</div>
					</div>
					<?php } ?>
				</div>

				<div class="card mb-4">
					<div class="card-header">
						Settings
					</div>
					<div class="card-body">
						<div class="mb-3">
							<label class="form-label">
								<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-circle-check" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
									<path stroke="none" d="M0 0h24v24H0z" fill="none"/>
									<circle cx="12" cy="12" r="9" />
									<path d="M9 12l2 2l4 -4" />
								</svg>
								Status
							</label>
							<div class="form-check-container">
								<div class="form-check">
									<input class="form-check-input" name="kwe-enabled" type="checkbox" value="On" id="$ctlCheck01" <?php if ($studio->getopt("kwe-enabled") === 'On') echo 'checked'; ?>>
									<label class="form-check-label" for="$ctlCheck01">
										Enable this data source
									</label>
								</div>
								<div class="form-check">
									<input class="form-check-input" name="kwe-exclusive" type="checkbox" value="On" id="$ctlCheck02" <?php if ($studio->getopt("kwe-exclusive") === 'On') echo 'checked'; ?>>
									<label class="form-check-label" for="$ctlCheck02">
										Disable unsupported countries
										<span class="badge help" title="Unsupported countries will still be shown, and will use the free data source instead, unless you check this" data-bs-toggle="tooltip" data-bs-placement="top">
											<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-question" viewBox="0 0 16 16">
												<path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z"/>
											</svg>
										</span>
									</label>
								</div>
							</div>
						</div>
						<div class="mb-3">
							<label class="form-label d-flex align-items-center" for="$inputCtl01">
								<div class="d-flex align-items-center flex-grow-1">
									<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-key" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
										<path stroke="none" d="M0 0h24v24H0z" fill="none"/>
										<circle cx="8" cy="15" r="4" />
										<line x1="10.85" y1="12.15" x2="19" y2="4" />
										<line x1="18" y1="5" x2="20" y2="7" />
										<line x1="15" y1="8" x2="17" y2="10" />
									</svg>
									API key
								</div>
								<div>
									<a href="https://keywordseverywhere.com/first-install-addon.html" target="_blank" rel="nofollow noopener">Click here to get a key</a>
								</div>
							</label>
							<input id="$inputCtl01" type="password" class="form-control" name="kwe-key" value="<?php echo sanitize_attribute(DEMO ? '' : $studio->getopt('kwe-key', '')); ?>" placeholder="For example: 602e40604dbce7a8fd42">
						</div>
						<div class="mb-3">
							<label class="form-label d-flex align-items-center" for="$inputCtl04">
								<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-urgent" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
									<path stroke="none" d="M0 0h24v24H0z" fill="none"/>
									<path d="M8 16v-4a4 4 0 0 1 8 0v4" />
									<path d="M3 12h1m8 -9v1m8 8h1m-15.4 -6.4l.7 .7m12.1 -.7l-.7 .7" />
									<rect x="6" y="16" width="12" height="4" rx="1" />
								</svg>
								Max number of keywords to fetch
							</label>
							<input id="$inputCtl04" step="10" min="20" max="100" type="number" class="form-control" name="kwe-max-keywords" value="<?php echo sanitize_attribute($studio->getopt('kwe-max-keywords', 100)); ?>" placeholder="">
						</div>
					</div>
				</div>

				<div class="card mb-4">
					<div class="card-header">
						Balance alerts
					</div>
					<div class="card-body">
						<div class="mb-3">
							<label class="form-label">
								<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-circle-check" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
									<path stroke="none" d="M0 0h24v24H0z" fill="none"/>
									<circle cx="12" cy="12" r="9" />
									<path d="M9 12l2 2l4 -4" />
								</svg>
								Status
							</label>
							<div class="form-check-container">
								<div class="form-check">
									<input class="form-check-input" name="kwe-notify-enabled" type="checkbox" value="On" id="$ctlCheck03" <?php if ($studio->getopt("kwe-notify-enabled") === 'On') echo 'checked'; ?>>
									<label class="form-check-label" for="$ctlCheck03">
										Email me when my balance gets too low
									</label>
								</div>
							</div>
						</div>
						<div class="mb-3">
							<label class="form-label d-flex align-items-center" for="$inputCtl02">
								<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-mail" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
									<path stroke="none" d="M0 0h24v24H0z" fill="none"/>
									<rect x="3" y="5" width="18" height="14" rx="2" />
									<polyline points="3 7 12 13 21 7" />
								</svg>
								Email address
							</label>
							<input id="$inputCtl02" type="email" class="form-control" name="kwe-notify-email" value="<?php echo sanitize_attribute(DEMO ? '' : $studio->getopt('kwe-notify-email', '')); ?>" placeholder="">
						</div>
						<div class="mb-3">
							<label class="form-label d-flex align-items-center" for="$inputCtl03">
								<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-urgent" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
									<path stroke="none" d="M0 0h24v24H0z" fill="none"/>
									<path d="M8 16v-4a4 4 0 0 1 8 0v4" />
									<path d="M3 12h1m8 -9v1m8 8h1m-15.4 -6.4l.7 .7m12.1 -.7l-.7 .7" />
									<rect x="6" y="16" width="12" height="4" rx="1" />
								</svg>
								Email me when my balance drops below...
							</label>
							<input id="$inputCtl03" type="number" class="form-control" name="kwe-notify-threshold" value="<?php echo sanitize_attribute($studio->getopt('kwe-notify-threshold', 10000)); ?>" placeholder="">
						</div>
					</div>
				</div>
			</div>
			<div class="save">
				<div class="save-box">
					<button type="submit">
						<span>Save changes</span>
						<span>Saved</span>
						<img src="<?php echo $page->getPath(); ?>resources/images/load32.gif" width="16px" height="16px">
					</button>
				</div>
			</div>
		</div>
	</form>
</section>

<?php
$page->footer();
?>
