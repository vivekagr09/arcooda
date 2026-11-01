<?php
require "../../includes/init.php";
$page->setPath("../../../")->requirePermission('admin-access')->setPage(11)->header('content');

if (isset($_POST['isPostback']) && !DEMO) {
    $bools = array("embedding-enabled", "embedding-strict");
    $strs = array("embedding-domains", "embedding-method", "embedding-header", "embedding-footer");

    foreach ($bools as $bool) {
        if (!isset($_POST[$bool])) $_POST[$bool] = 'Off';

        $val = $_POST[$bool];
        if ($val != "On" && $val != "Off") $val = "Off";

        $studio->setopt($bool, $val);
    }

    foreach ($strs as $str) {
        if (!isset($_POST[$str])) $studio->showFatalError("Missing POST parameter $str");

        $val = $_POST[$str];
        $studio->setopt($str, $val);
    }

    header("Location: embed-settings.php?success=1");
    die;
}
?>

<section>
	<div class="header with-border">
		<h1>
			<a class="back" href="embed.php">
				<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-circle" viewBox="0 0 16 16">
					<path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8zm15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-4.5-.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5z"/>
				</svg>
			</a>
			Embed settings
		</h1>
		<p>
			Manage how tool embeds work.
		</p>
	</div>

	<form action="" method="post">
		<input type="hidden" name="isPostback" value="1">

		<div class="save-container">
			<div class="navigation-container">
				<div class="navigation">
					<div class="nav nav-pills">
						<button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tab-settings" type="button">
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear" viewBox="0 0 16 16">
								<path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492zM5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0z"/>
								<path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52l-.094-.319zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115l.094-.319z"/>
							</svg>
							Settings
						</button>
						<button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-content" type="button">
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-code-square" viewBox="0 0 16 16">
								<path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
								<path d="M6.854 4.646a.5.5 0 0 1 0 .708L4.207 8l2.647 2.646a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 0 1 .708 0zm2.292 0a.5.5 0 0 0 0 .708L11.793 8l-2.647 2.646a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708 0z"/>
							</svg>
							Content
						</button>
					</div>
				</div>
				<div class="saveable tab-content">
					<div class="card mb-4 tab-pane show active" id="tab-settings">
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
										<input class="form-check-input" name="embedding-enabled" type="checkbox" value="On" id="enableToolEmbedding" <?php if ($studio->getopt("embedding-enabled") === 'On') echo 'checked'; ?>>
										<label class="form-check-label" for="enableToolEmbedding">
											Enable tool embedding
										</label>
									</div>
									<div class="form-check">
										<input class="form-check-input" name="embedding-strict" type="checkbox" value="On" id="enableStrictEmbedding" <?php if ($studio->getopt("embedding-strict") === 'On') echo 'checked'; ?>>
										<label class="form-check-label" for="enableStrictEmbedding">
											Enable strict mode
											<span class="badge help" title="When enabled, tool embeds will not load if no referring website is found." data-bs-toggle="tooltip" data-bs-placement="top">
												<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-question" viewBox="0 0 16 16">
													<path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z"/>
												</svg>
											</span>
										</label>
									</div>
								</div>
							</div>
							<div class="mb-3">
								<label class="form-label">
									<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-settings" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
										<path stroke="none" d="M0 0h24v24H0z" fill="none"/>
										<path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" />
										<circle cx="12" cy="12" r="3" />
									</svg>
									Where can tools be embedded?
								</label>
								<div class="form-check-container">
									<div class="form-check">
										<input class="form-check-input" name="embedding-method" type="radio" value="all" id="embeddingMethod1" <?php if ($studio->getopt("embedding-method") === 'all') echo 'checked'; ?>>
										<label class="form-check-label" for="embeddingMethod1">
											Allow all websites
										</label>
									</div>
									<div class="form-check">
										<input class="form-check-input" name="embedding-method" type="radio" value="custom" id="embeddingMethod3" <?php if ($studio->getopt("embedding-method") === 'custom') echo 'checked'; ?>>
										<label class="form-check-label" for="embeddingMethod3">
											Allow a list of approved websites
										</label>
									</div>
									<div class="form-check">
										<input class="form-check-input" name="embedding-method" type="radio" value="local" id="embeddingMethod2" <?php if ($studio->getopt("embedding-method", 'local') === 'local') echo 'checked'; ?>>
										<label class="form-check-label" for="embeddingMethod2">
											Allow only <code><?php
												$publicLink = $studio->getopt('public-url');
												$publicParsed = @parse_url($publicLink);

												if ($publicParsed && isset($publicParsed['host'])) {
													if (isset($publicParsed['scheme'])) {
														echo $publicParsed['scheme'] . '://';
													}

													echo $publicParsed['host'];
												}
												?></code>
										</label>
									</div>
								</div>
							</div>
							<div class="mb-3" id="whitelist">
								<label class="form-label">
									<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-world" width="16" height="16" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
										<path stroke="none" d="M0 0h24v24H0z" fill="none"/>
										<circle cx="12" cy="12" r="9" />
										<line x1="3.6" y1="9" x2="20.4" y2="9" />
										<line x1="3.6" y1="15" x2="20.4" y2="15" />
										<path d="M11.5 3a17 17 0 0 0 0 18" />
										<path d="M12.5 3a17 17 0 0 1 0 18" />
									</svg>
									Whitelisted domains
								</label>
								<textarea class="form-control" name="embedding-domains" rows="4"><?php echo sanitize_html($studio->getopt("embedding-domains", "")); ?></textarea>
								<div class="form-text">
									Add one domain name per line such as <code>example.com</code>.
									The <code>www.</code> version of each whitelisted domain is automatically allowed.
									You can use an asterisk to accept all other subdomains such as <code>*.example.com</code>.
									All domains not listed here will be shown an error when attempting to embed your tools.
								</div>
							</div>
						</div>
					</div>

					<div class="card mb-4 tab-pane" id="tab-content">
						<div class="card-body">
							<div class="mb-3">
								<label class="form-label">
									<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-code-square" viewBox="0 0 16 16">
										<path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
										<path d="M6.854 4.646a.5.5 0 0 1 0 .708L4.207 8l2.647 2.646a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 0 1 .708 0zm2.292 0a.5.5 0 0 0 0 .708L11.793 8l-2.647 2.646a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708 0z"/>
									</svg>
									Header (full width)
								</label>
								<div class="form-text">Enter custom HTML that will appear at the very top of the frames for embedded tools.</div>
								<textarea class="form-control" name="embedding-header" rows="3" spellcheck="false"><?php echo $studio->getopt("embedding-header"); ?></textarea>
							</div>
							<div class="mb-3">
								<label class="form-label">
									<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-code-square" viewBox="0 0 16 16">
										<path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
										<path d="M6.854 4.646a.5.5 0 0 1 0 .708L4.207 8l2.647 2.646a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 0 1 .708 0zm2.292 0a.5.5 0 0 0 0 .708L11.793 8l-2.647 2.646a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708 0z"/>
									</svg>
									Footer (full width)
								</label>
								<div class="form-text">Enter custom HTML that will appear at the very bottom of the page for embedded tools.</div>
								<textarea class="form-control" name="embedding-footer" rows="3" spellcheck="false"><?php echo $studio->getopt("embedding-footer"); ?></textarea>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="save no-padding">
				<div class="save-box right-on-mobile">
					<button type="submit">
						<span>Save changes</span>
						<span>Saved</span>
						<img src="../../../resources/images/load32.gif" width="16px" height="16px">
					</button>
				</div>
			</div>
		</div>
	</form>
</section>

<script>
	var inputs = $('input[type=radio][name=embedding-method]');
	var whitelist = $('#whitelist');

	function updateWhitelistRenderState() {
		var selected = inputs.filter(':checked').first();

		if (selected.length > 0) {
			var value = selected.val();

			if (value === 'custom') {
				whitelist.removeClass('hidden');
				return;
			}
		}

		whitelist.addClass('hidden');
	}

	inputs.on('change', function() {
		updateWhitelistRenderState();
	});

	updateWhitelistRenderState();
</script>

<?php
$page->footer();
?>
